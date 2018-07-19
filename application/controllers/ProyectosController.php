<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ProyectosController extends CI_Controller {

	private $error;
	function __construct()
	{
		parent::__construct();
		$this->load->model("ProyectosModel");
		$this->load->library("Utilerias");
	}

	public function listar()
	{
		$data = $this->ProyectosModel->listar();
		print json_encode($data);
	}

	public function listarProyectosForSelect()
	{
		$data = $this->ProyectosModel->listarProyectosForSelect();
		print json_encode($data);
	}

	public function getProyectoById()
	{
		$id_proyecto = intval($this->input->post("id_proyecto"));
		$data = $this->ProyectosModel->getProyectoById($id_proyecto);
		print json_encode($data);
	}

	private function validar(&$d,$tipo)
	{
		if(empty($d["nombre"])){
			$this->error = "El nombre es un campo obligatorio";
			return false;
		}
		if(empty($d["finicio"])){
			$this->error = "La fecha de inicio del proyecto es obligatoria";
			return false;
		}
		if(empty($d["ffinal"])){
			$this->error = "La fecha de termino del proyecto es obligatoria";
			return false;
		}

		if(!empty($d["logo"])){
			//do something
		}

		return true;

	}

	public function insertar()
	{
		$d["nombre"] = trim($this->input->post("nombre"));
		$d["descripcion"] = trim($this->input->post("descripcion"));
		$d["siglas"] = trim($this->input->post("siglas"));
		$d["finicio"] = trim($this->input->post("finicio"));
		$d["ffinal"] = trim($this->input->post("ffinal"));


		if($this->validar($d,"insertar"))
		{
			$data = $this->ProyectosModel->insertar($d);
			print json_encode($data);
			die();
		}else{
			print json_encode(array("error"=>"1","insertado"=>"0","msg"=>$this->error));
			die();
		}
	}

	public function update()
	{
		$d["id_proyecto"] = intval($this->input->post("id_proyecto"));
		$d["nombre"] = trim($this->input->post("nombre"));
		$d["descripcion"] = trim($this->input->post("descripcion"));
		$d["siglas"] = trim($this->input->post("siglas"));
		$d["finicio"] = trim($this->input->post("finicio"));
		$d["ffinal"] = trim($this->input->post("ffinal"));

		if($this->validar($d,"update"))
		{
			$data = $this->ProyectosModel->update($d);
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
		if(empty($id_proyecto)){
			print json_encode( array("borrado"=>"0","error"=>"1","msg"=>"No se recibieron los parametros esperados"));
			die();
		}else{
			$data = $this->ProyectosModel->borrarById($id_proyecto);
			print json_encode($data);
		}
	}

}