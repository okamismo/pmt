<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ProyectosPersonasModel extends CI_Model{

	function __construct()
	{
		parent::__construct();
		$this->load->library("Utilerias");
	}

	function listar($id_proyecto)
	{
		if($this->db->platform() == "mssql" || $this->db->platform() == "sqlsrv") {
			$this->db->select("per.id_persona,(per.nombre) + ' ' + (per.paterno) as nombre");
		}else{
			$this->db->select("per.id_persona,concat(per.nombre, ' ',per.paterno) as nombre");
		}

		$r = $this->db->from("proyectos_personas as pp")->
			join("proyectos as pro","pp.id_proyecto=pro.id_proyecto")->
			join("personas as per","per.id_persona=pp.id_persona")->
			where("pp.id_proyecto",$id_proyecto)->
			get();
			//die($this->db->last_query());
		if($r)
		{
			$d = $r->result_array();
			$data = $d;//$this->utilerias->utf8Encode($d);

			return $data;
		}
		else{
			return array("error"=>"1","msg"=>"Ocurrio un problema y o fue posible obtener la información");
		}
	}

	function listarForSelect($id_proyecto)
	{
		if($this->db->platform() == "mssql" || $this->db->platform() == "sqlsrv") {
			$this->db->select("per.id_persona as value,(per.nombre) + ' ' + (per.paterno) as label");
		}else{
			$this->db->select("per.id_persona as value,concat(per.nombre, ' ',per.paterno) as label");
		}
		
		
		$r = $this->db->from("proyectos_personas as pp")->
			join("proyectos as pro","pp.id_proyecto=pro.id_proyecto")->
			join("personas as per","per.id_persona=pp.id_persona")->
			where("pp.id_proyecto",$id_proyecto)->
			get();

		if($r)
		{
			$d = $r->result_array();
			$data = $d;//$this->utilerias->utf8Encode($d);

			return $data;
		}
		else{
			return array("error"=>"1");
		}
	}

	function agregar($id_proyecto, $ids_personas)
	{
		$this->db->trans_start();
		$len = count($ids_personas);
		for($i=0; $i<$len; $i++){
			$this->db->insert("proyectos_personas",array("id_proyecto"=>$id_proyecto,"id_persona"=>$ids_personas[$i]));
		}
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE)
		{
			return array("insertado"=>"0","error"=>"1","msg"=>"No se pudo completar la transacción...");
		}else{
			return array("insertado"=>"1","error"=>"0","msg"=>"Transacción completada...");
		}
	}

	function borrar($id_proyecto, $ids_personas)
	{
		$this->db->trans_start();
		$len = count($ids_personas);
		for($i=0; $i<$len; $i++){
			$this->db->delete("proyectos_personas",array("id_proyecto"=>$id_proyecto,"id_persona"=>$ids_personas[$i]));
		}
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE)
		{
			return array("borrado"=>"0","error"=>"1","msg"=>"No se pudo completar la transacción...");
		}else{
			return array("borrado"=>"1","error"=>"0","msg"=>"Transacción completada...");
		}
	}

	function update($id_proyecto,$id_persona,$id_rol)
	{
		$this->db->where("id_proyecto",$id_proyecto)->where("id_persona",$id_persona);
		if($this->db->update("proyectos_personas",array("id_rol"=>$id_rol))){
			return array("actualizado"=>"1","error"=>"0","msg"=>"Rol guardado exitosamente");
		}else{
			return array("actualizado"=>"0","error"=>"1","msg"=>"No fue posible guardar...");
		}
	}

	function getDatosActuales($id_proyecto,$id_persona)
	{
		$r = $this->db->select("id_rol")->
			from("proyectos_personas")->
			where("id_proyecto",$id_proyecto)->
			where("id_persona",$id_persona)->
			get();

		if($r)
		{
			$d = $r->row();
			$data = $d;//$this->utilerias->utf8Encode($d);

			return $data;
		}
		else{
			return array("error"=>"1","msg"=>"Ocurrio un problema y o fue posible obtener la información");
		}
	}

}