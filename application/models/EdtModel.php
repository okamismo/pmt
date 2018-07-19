<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class EdtModel extends CI_Model{

	function __construct()
	{
		parent::__construct();
		$this->load->library("Utilerias");
	}

	function listarTareas($id_proyecto)
	{
		$this->load->library("RBAC");
		if($this->rbac->hasUserProject($id_proyecto))
		{
			$page = intval($this->input->post("page"));
			$rowsPerPage = intval($this->input->post("rowsPerPage"));
			$offset = ($page * $rowsPerPage) - $rowsPerPage;
			$sortBy = (empty($this->input->post("sortBy")))?"avance,finicio":$this->input->post("sortBy");
			$sentido = $this->input->post("sentido");
			$filter = $this->input->post("filter");
			$id_persona = (empty($this->input->post("id_persona")))?$this->session->id_persona:intval($this->input->post("id_persona"));

			//se pueden agregar ORs para filtrar por mas de un campo
			$filterQuery = "(actividad like '%".$this->db->escape_like_str($filter)."%')";
			$r = $this->db->select("e.id_paquete,actividad,finicio,ffin,duracion,avance,'' as semaforo,e.objetivo")->
				from("edt as e")->
				join("paquetes_personas as pp","e.id_paquete = pp.id_paquete")->
				where("e.id_proyecto", $id_proyecto)->
				where("pp.id_persona", $id_persona)->
				where($filterQuery)->
				order_by($sortBy,$sentido)->
				limit($rowsPerPage,$offset)->get();
			
			$sql = $this->db->last_query();
			//die($sql);

			if($r)
			{
				$d = $r->result_array();
				$this->activaSemaforos($d);
				
				$data["rowsNumber"] = $this->db->from("edt as e")->
					join("paquetes_personas as pp","e.id_paquete = pp.id_paquete")->
					where("id_proyecto", $id_proyecto)->
					where("id_persona", $this->session->id_persona)->
					count_all_results();

				$data["rows"] = $d;//$this->utilerias->utf8Encode($d);
				$data["sql"] = $sql;
				return $data;
			}else{
				return array("error"=>"1","msg"=>"Ocurrio un problema y no fue posible obtener la información");
			}
		}else{
			return array("error"=>"1","msg"=>"Acceso denegado: el usuario no tiene permisos sobre el proyecto");
		}
	}

	private function activaSemaforos(&$actividades)
	{
		$len = count($actividades);
		$hoy = new DateTime(date("Y-m-d"));
		$ndias = null;
		for($i=0; $i<$len; $i++)
		{

			if(!empty($actividades[$i]["ffin"]) && $actividades[$i]["ffin"] != "0000-00-00")
			{
				$fechaActividad = new DateTime($actividades[$i]["ffin"]);
				$dif = $hoy->diff($fechaActividad);
				
				$actividades[$i]["semaforo"] = intval($dif->format("%R%a"));
				$actividades[$i]["tvencido"] = $dif->invert;

				if($actividades[$i]["semaforo"] <= 0){
					$actividades[$i]["color"] = "negative";
				}else if($actividades[$i]["semaforo"]>0 && $actividades[$i]["semaforo"]<=3){
					$actividades[$i]["color"] = "warning";
				}else if($actividades[$i]["semaforo"] > 3){
					$actividades[$i]["color"] = "positive";
				}
			}else
			{
				$actividades[$i]["color"] = "light";
				$actividades[$i]["semaforo"] = "";
				$actividades[$i]["tvencido"] = "";
			}
			
		}
	}

	function buscarSubNodos($id_proyecto,$id_paquete)
	{
		$this->db->select("id_paquete as id,actividad as label,'true' as lazy, id_padre,ponderacion,icono as icon")->
			from("edt")->
			where("id_proyecto",$id_proyecto);
			
		if($id_paquete != "root" && $id_paquete != null){
			$this->db->where("id_padre",$id_paquete);
		}else{
			$this->db->where("id_padre is null");
		}

		$r = $this->db->get();

		if($r)
		{
			if($r->num_rows()==0){
				return array();
			}
			$d = $r->result_array();
			$data = $d;//$this->utilerias->utf8Encode($d);
			return $data;
		}
		else{
			return array("error"=>"1");
		}
	}

	/*
	// ESTAS FUNCIONES SON PARA EL ARBOL SIN LAZY LOADING
	//busca un id y regresa la referencia de ese id (la referencia es la posicion de un array)
	private function &buscaPadre($aguja,&$pajar)
	{
		$len = count($pajar);
		for($i=0; $i<$len; $i++){
			if($aguja == $pajar[$i]["id_paquete"]){
				return $pajar[$i]["index"];
			}
		}
		return null;
	}

	

	function listar($id_proyecto)
	{
		$r = $this->db->select("id_paquete,actividad,id_padre")->
				where("id_proyecto",$id_proyecto)->
				order_by("id_padre,id_paquete")->
				get("edt");
		if($r)
		{
			$d = $r->result_array();// nodos completos
			$len = count($d);// longitud del primer nivel de nodos
			$data; //arreglo que contendra la estructura del arbol
			$pIndex; //arreglo de referencias hacia $d (arreglo original de nodos)
			$j=0; //indice para $data (se usa solo para el primer nivel), para los anidamientos se usan las referencias
			

			for($i=0; $i<$len; $i++)
			{
				if( !empty($d[$i]["id_padre"]) )//sub-nodos hijos de algun nodo diferente del proyecto (tienen padre)
				{
					//regresa la referencia al indice children quien será padre del nodo actual $d[$i]
					//se podria decir que padre es clon de data(si se afecta uno el otro lo refleja tambien)
					$padre = &$this->buscaPadre($d[$i]["id_padre"],$pIndex);
					if($padre !== null)
					{
						$padre[ ] = array(
							"id" => $d[$i]["id_paquete"],
							"label"=> $d[$i]["actividad"],
							"children" => array()
						);

						//se guarda la referencia hacia el id_paquete actual
						$pIndex[$i]["id_paquete"] = $d[$i]["id_paquete"];
						//la refencia tiene que apuntar al children nuevo (que no tiene nada por el momento)
						$pIndex[$i]["index"] = & $padre[count($padre)-1]["children"];
						
					}
					
				}
				else
				{//nodos directamente hijos del proyecto (no tienen padre)
					
					//se crea el primer nivel del arbol dejando children vacio por el momento
					$data[$j]["id"] = $d[$i]["id_paquete"];
					$data[$j]["label"] = $d[$i]["actividad"];
					$data[$j]["children"] = array();

					//se guarda la referencia hacia el id_paquete actual
					$pIndex[$i]["id_paquete"] = $data[$j]["id"];
					//la refencia tiene que apuntar al children nuevo (que no tiene nada por el momento)
					$pIndex[$i]["index"] = & $data[$j]["children"];
					$j++; 
				}

				
			}

			$res["error"] = "0";
			$res["nodos"] = $data;
			//$data = $d;//$this->utilerias->utf8Encode($d);

			return $res;
		}
		else{
			return array("error"=>"1");
		}
	}
	*/

	function getPaqueteById($id_proyecto,$id_paquete)
	{
		$r = $this->db->where("id_proyecto",$id_proyecto)->
			where("id_paquete",$id_paquete)->
			get("edt");

		if($r)
		{
			$d = (array)$r->row();
			$r = $this->db->select("id_persona")->
				from("paquetes_personas")->
				where("id_paquete",$id_paquete)->
				get();

			if($r){
				if($r->num_rows()>0){
					$d["ids_personas"] = array_column($r->result_array(),"id_persona");
				}else{
					$d["ids_personas"] = array();
				}
			}else{
				$d["ids_personas"] = array();
			}
			
			$data = $d;//$this->utilerias->utf8Encode($d);

			return $data;
		}
		else{
			return array("error"=>"1");
		}
	}

	private function getNodeByName($id_proyecto, $nombre)
	{
		$r = $this->db->select("id_paquete")->
				from("edt")-> 
				where("id_proyecto",$id_proyecto)->
				where("actividad",$nombre)->
				order_by("factualizado","DESC")->get();
		if($r){
			$id_paquete = $r->row()->id_paquete;
			return array("error"=>"0","id_paquete"=>$id_paquete);
		}else{
			return array("error"=>"1","id_paquete"=>"-1");
		}
	}

	function obtenerPonderacionDePaquete($id_paquete)
	{
		$r = $this->db->select("ponderacion")->
			from("edt")->
			where("id_paquete",$id_paquete)->get();
		
		if($r)
		{
			if($r->num_rows()>0){
				return $r->row()->ponderacion;
			}else{
				return false;
			}
		}
	}

	//valida que el nuevo nodo mas sus hermanos tengan una ponderacion menor o igual a la del padre
	private function validarNuevaPonderacion($id_padre,$ponderacionActual)
	{
		$filtro = ($id_padre === null)? "id_padre is null":"id_padre=".$id_padre;
		$r = $this->db->select("ponderacion")->
			from("edt")->
			where($filtro)->get();
		
		if($r)
		{
			$len = $r->num_rows();
			if($len>0)
			{
				$hermanos = $r->result_array();
				$sumaActual = 0;
				for($i=0; $i<$len; $i++){
					if(is_numeric($hermanos[$i]["ponderacion"])) {
						$sumaActual += ($hermanos[$i]["ponderacion"]);//suma de hermanos
					}
				}
				$sumaActual += $ponderacionActual;//hermanos + nuevo nodo
				$ponderacionPadre = 0;
				if($id_padre !== null) {
					$ponderacionPadre = $this->obtenerPonderacionDePaquete($id_padre);
				}else{
					$ponderacionPadre = 100;
				}

				return ($ponderacionActual>0 && $sumaActual <= $ponderacionPadre);
				
			}else{
				return ($ponderacionActual>0 && $ponderacionActual <= 100);
			}
		}else{
			return false;
		}
	}

	//valida que la actualizacion de la ponderacion del nodo mas sus hermanos tengan un valor menor o igual a la del padre y que sea mayor que la de sus posibles hijos
	private function validarActualizacionPonderacion($id_proyecto,$id_padre,$id_paquete,$ponderacionActual)
	{
		$filtro = ($id_padre === null)? "id_padre is null":"id_padre=".$id_padre;
		$r = $this->db->select("id_paquete, ponderacion")->
			from("edt")->
			where($filtro)->get();
		
		if($r)
		{
			$len = $r->num_rows();
			if($len>0)
			{
				$hermanos = $r->result_array();
				$sumaActual = 0;
				for($i=0; $i<$len; $i++){
					if(is_numeric($hermanos[$i]["ponderacion"])) {
						if($hermanos[$i]["id_paquete"] == $id_paquete){
							continue;//se trata de la ponderacion (vieja) del paquete que se esta actualizando
						}
						$sumaActual += ($hermanos[$i]["ponderacion"]);//suma de hermanos
					}
				}
				$sumaActual += $ponderacionActual;//hermanos + ponderacion nueva del nodo
				$ponderacionPadre = 0;
				if($id_padre !== null) {
					$ponderacionPadre = $this->obtenerPonderacionDePaquete($id_padre);
				}else{
					$ponderacionPadre = 100;
				}
				
				if(($ponderacionActual<0) || ($sumaActual > $ponderacionPadre)) {
					//die($ponderacionActual." - ".$sumaActual." - ".$ponderacionPadre);
					return FALSE;
				}
				
				$hijos = $this->buscarSubNodos($id_proyecto,$id_paquete);
				$len = count($hijos);
				$sumaActual = 0;
				for($i=0; $i<$len; $i++) {
					if(is_numeric($hijos[$i]["ponderacion"])) {
						$sumaActual += $hijos[$i]["ponderacion"];
					}
				}

				if($len > 0 &&  $sumaActual > $ponderacionActual) {
					return FALSE;
				}
								
				return TRUE;
			}else{
				return ($ponderacionActual>0 && $ponderacionActual <= 100);
			}
		}else{
			return false;
		}
	}

	function insertar($d)
	{
		$ponderacion = false;
		$ponderacion = $this->validarNuevaPonderacion($d["id_padre"],$d["ponderacion"]);
		if(!$ponderacion) {
			$d["icono"] = "fas fa-exclamation-triangle";
		}
		
		$d["finsertado"] = date("Ymd H:i:s");
		$d["factualizado"] = date("Ymd H:i:s");
		
		$ids_personas = $d["ids_personas"];
		unset($d["ids_personas"]);

		$this->db->trans_start();
		if($this->db->insert("edt",$d))
		{
			
			$id_paquete = $this->getNodeByName($d["id_proyecto"],$d["actividad"]);
			$len = count($ids_personas);
			
			for($i=0; $i<$len; $i++){
				$this->db->insert("paquetes_personas",array("id_paquete"=>$id_paquete["id_paquete"],"id_persona"=>$ids_personas[$i]));
			}
			
			$this->db->trans_complete();
			if($this->db->trans_status() === FALSE)
			{
				return array("error"=>"1","insertado"=>"0","msg"=>"No fue posible guardar el registro...","ponderacion"=>$ponderacion);
			}
			else
			{
				return array("error"=>"0","insertado"=>"1","msg"=>"El registro ha sido guardado...","id_paquete"=>$id_paquete["id_paquete"],"ponderacion"=>$ponderacion);
			}
			
		}else{
			return array("error"=>"1","insertado"=>"0","msg"=>"No fue posible guardar el registro...");
		}
	}

	function actualizar($d)
	{
		$ponderacion = false;
		$ponderacion = $this->validarActualizacionPonderacion($d["id_proyecto"],$d["id_padre"],$d["id_paquete"],$d["ponderacion"]);
		if(!$ponderacion) {
			$d["icono"] = "fas fa-exclamation-triangle";
		}else{
			$d["icono"] = null;
		}

		$d["factualizado"] = date("Ymd H:i:s");
		$this->db->trans_start();
		$this->db->where("id_proyecto",$d["id_proyecto"])->
			where("id_paquete",$d["id_paquete"]);
		
		$ids_personas = $d["ids_personas"];

		unset($d["ids_personas"]);
		unset($d["id_proyecto"]);
		unset($d["id_padre"]);

		if($this->db->update("edt",$d)){
			$this->db->delete("paquetes_personas",array("id_paquete"=>$d["id_paquete"]));

			$len = count($ids_personas);
			for($i=0; $i<$len; $i++){
				$this->db->insert("paquetes_personas",array("id_paquete"=>$d["id_paquete"],"id_persona"=>$ids_personas[$i]));
			}
			$this->db->trans_complete();
			if($this->db->trans_status() === FALSE) {
				
				return array("error"=>"1","actualizado"=>"0","msg"=>"No fue posible guardar el registro...");
			}else{
				$nodo = array("label"=>$d["actividad"],"icon"=>$d["icono"],"ponderacion"=>$d["ponderacion"]);
				return array("error"=>"0","actualizado"=>"1","msg"=>"El registro ha sido guardado...","nodo"=>$nodo);
			}
		}else{
			return array("error"=>"1","actualizado"=>"0","msg"=>"No fue posible guardar el registro...");
		}
	}

	function borrarById($id_proyecto,$id_paquete)
	{
		$this->db->trans_start();
		
		$this->db->where("id_proyecto",$id_proyecto)->
			where("id_padre",$id_paquete);
		$this->db->delete("edt");
		
		$this->db->flush_cache();

		$this->db->where("id_proyecto",$id_proyecto)->
			where("id_paquete",$id_paquete);
		$this->db->delete("edt");

		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE)
		{
			return array("borrado"=>"0","error"=>"1","msg"=>"No se pudo completar la transacción...");
		}else{
			return array("borrado"=>"1","error"=>"0","msg"=>"Transacción completada...");
		}
	}

	function cambiarAvanceTarea($id_proyecto,$id_paquete,$avance)
	{
		$this->load->library("RBAC");
		if($this->rbac->hasUserProject($id_proyecto))
		{

			$this->db->where("id_proyecto",$id_proyecto)->
				where("id_paquete",$id_paquete);

			if($this->db->update("edt",array("avance"=>$avance,"factualizado"=>date("Y-m-d H:i:s")))){
				return array("actualizado"=>"1","error"=>"0","msg"=>"Avance actualizado...");
			}else{
				return array("actualizado"=>"0","error"=>"1","msg"=>"No fue posible actualizar el Avance...");
			}
		}else{
			return array("actualizado"=>"0","error"=>"1","msg"=>"No se tienen los permisos sobre el proyecto...");
		}
	}

}