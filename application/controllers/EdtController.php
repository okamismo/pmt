<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class EdtController extends CI_Controller {

	private $error;
	function __construct()
	{
		parent::__construct();
		$this->load->model("EdtModel");
		$this->load->library("Utilerias");
	}

	public function listar()
	{
		$id_proyecto = intval($this->input->post("id_proyecto"));
		$data = $this->EdtModel->listar($id_proyecto);
		print json_encode($data);
	}

	public function listarTareas()
	{
		$id_proyecto = intval($this->input->post("id_proyecto"));
		$data = $this->EdtModel->listarTareas($id_proyecto);
		print json_encode($data);
	}

	public function buscarSubNodos()
	{
		$id_proyecto = intval($this->input->post("id_proyecto"));
		$id_paquete = intval($this->input->post("id_paquete"));
		$data = $this->EdtModel->buscarSubNodos($id_proyecto,$id_paquete);
		print json_encode($data);
	}

	
	public function getPaqueteById()
	{
		$id_proyecto = intval($this->input->post("id_proyecto"));
		$id_paquete = intval($this->input->post("id_paquete"));
		$data = $this->EdtModel->getPaqueteById($id_proyecto,$id_paquete);
		print json_encode($data);
	}

	private function validar(&$d,$tipo)
	{
		if($tipo == ""){
			if(empty($d["id_paquete"])){
				$this->error = "Nodo invalido";
				return false;
			}
		}
		if(empty($d["id_proyecto"])){
			$this->error = "Proyecto invalido";
			return false;
		}
		if(empty($d["id_padre"])){
			$this->error = "Nodo Padre invalido";
			return false;
		}
		if(empty($d["actividad"])){
			$this->error = "El nombre del paquete/tarea/subtarea es obligatorio";
			return false;
		}

		if($d["id_padre"] == "root"){
			$d["id_padre"] = null;
		}

		return true;

	}

	public function insertar()
	{
		$d["id_proyecto"] = intval($this->input->post("id_proyecto"));
		$d["id_padre"] = trim($this->input->post("id_padre"));//si es root no tiene padre
		$d["actividad"] = trim($this->input->post("actividad"));
		$d["objetivo"] = trim($this->input->post("objetivo"));
		$d["finicio"] = trim($this->input->post("finicio"));
		$d["ffin"] = trim($this->input->post("ffin"));
		$d["ids_personas"] = $this->input->post("ids_personas");
		$d["duracion"] = intval($this->input->post("duracion"));
		$d["avance"] = (is_numeric($this->input->post("avance")))?$this->input->post("avance"):0;
		$d["ponderacion"] = (is_numeric($this->input->post("ponderacion")))?$this->input->post("ponderacion"):0.1;


		if($this->validar($d,"insertar"))
		{
			$data = $this->EdtModel->insertar($d);
			print json_encode($data);
			die();
		}else{
			print json_encode(array("error"=>"1","insertado"=>"0","msg"=>$this->error));
			die();
		}
	}

	public function actualizar()
	{
		$d["id_paquete"] = intval($this->input->post("id_paquete"));
		$d["id_proyecto"] = intval($this->input->post("id_proyecto"));
		$d["id_padre"] = trim($this->input->post("id_padre"));//si es root no tiene padre
		$d["actividad"] = trim($this->input->post("actividad"));
		$d["objetivo"] = trim($this->input->post("objetivo"));
		$d["finicio"] = trim($this->input->post("finicio"));
		$d["ffin"] = trim($this->input->post("ffin"));
		$d["ids_personas"] = $this->input->post("ids_personas");
		$d["duracion"] = intval($this->input->post("duracion"));
		$d["avance"] = (is_numeric($this->input->post("avance")))?$this->input->post("avance"):0;
		$d["ponderacion"] = (is_numeric($this->input->post("ponderacion")))?$this->input->post("ponderacion"):0.1;

		if($this->validar($d,"update"))
		{
			$data = $this->EdtModel->actualizar($d);
			print json_encode($data);
			die();
		}else{
			print json_encode(array("error"=>"1","actualizado"=>"0","msg"=>$this->error));
			die();
		}
	}

	public function borrarById()
	{
		$id_proyecto = intval($this->input->post("id_proyecto"));
		$id_paquete = intval($this->input->post("id_paquete"));

		if(empty($id_proyecto) || empty($id_paquete)){
			print json_encode( array("borrado"=>"0","error"=>"1","msg"=>"No se recibieron los parametros esperados"));
			die();
		}else{
			$data = $this->EdtModel->borrarById($id_proyecto,$id_paquete);
			print json_encode($data);
		}
	}

	public function cambiarAvanceTarea()
	{
		$id_proyecto = intval($this->input->post("id_proyecto"));
		$id_paquete = intval($this->input->post("id_paquete"));
		$avance = intval($this->input->post("avance"));

		if(empty($id_proyecto) || empty($id_paquete)){
			print json_encode( array("actualizado"=>"0","error"=>"1","msg"=>"No se recibieron los parametros esperados"));
			die();
		}else{
			$data = $this->EdtModel->cambiarAvanceTarea($id_proyecto,$id_paquete,$avance);
			print json_encode($data);
		}
	}

	function obtenerPonderacionDePaquete()
	{
		$id_paquete = intval($this->input->post("id_paquete"));
		if(empty($id_paquete)){
			print json_encode( array("ponderacion"=>"0","error"=>"1","msg"=>"No se recibieron los parametros esperados"));
			die();
		}else{
			$data = $this->EdtModel->obtenerPonderacionDePaquete($id_paquete);
			print json_encode($data);
		}
	}

}