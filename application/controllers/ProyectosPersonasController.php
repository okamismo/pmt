<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ProyectosPersonasController extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model("ProyectosPersonasModel");
	}

	public function listar()
	{
		$id_proyecto = intval($this->input->post("id_proyecto"));
		if(empty($id_proyecto)){
			print json_encode(array("encontrado"=>"0","error"=>"1","msg"=>"El proyecto no puede ser vacío o nulo"));
			die();
		}else{
			$data = $this->ProyectosPersonasModel->listar($id_proyecto);
			print json_encode($data);
		}
		
	}

	public function listarForSelect()
	{
		$id_proyecto = intval($this->input->post("id_proyecto"));
		if(empty($id_proyecto)){
			print json_encode(array("encontrado"=>"0","error"=>"1","msg"=>"El proyecto no puede ser vacío o nulo"));
			die();
		}else{
			$data = $this->ProyectosPersonasModel->listarForSelect($id_proyecto);
			print json_encode($data);
		}
		
	}

	public function agregar()
	{
		$id_proyecto = intval($this->input->post("id_proyecto"));
		$ids_personas = $this->input->post("ids");

		if(empty($id_proyecto) || empty($ids_personas)){
			print json_encode(array("insertado"=>"0","error"=>"1","msg"=>"Los parametros esperados no fueron recibidos"));
			die();
		}else{
			$data = $this->ProyectosPersonasModel->agregar($id_proyecto,$ids_personas);
			print json_encode($data);
		}
	}

	public function borrar()
	{
		$id_proyecto = intval($this->input->post("id_proyecto"));
		$ids_personas = $this->input->post("ids");

		if(empty($id_proyecto) || empty($ids_personas)){
			print json_encode(array("insertado"=>"0","error"=>"1","msg"=>"Los parametros esperados no fueron recibidos"));
			die();
		}else{
			$data = $this->ProyectosPersonasModel->borrar($id_proyecto,$ids_personas);
			print json_encode($data);
		}
	}

	public function update()
	{
		$id_proyecto = intval($this->input->post("id_proyecto"));
		$id_persona = intval($this->input->post("id_persona"));
		$id_rol = intval($this->input->post("id_rol"));

		$data = $this->ProyectosPersonasModel->update($id_proyecto,$id_persona,$id_rol);
		print json_encode($data);
	}

	public function getDatosActuales()
	{
		$id_proyecto = intval($this->input->post("id_proyecto"));
		$id_persona = intval($this->input->post("id_persona"));

		$data = $this->ProyectosPersonasModel->getDatosActuales($id_proyecto,$id_persona);
		print json_encode($data);
	}

}