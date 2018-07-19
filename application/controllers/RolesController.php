<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class RolesController extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model("RolesModel");
	}

	public function listar()
	{
		$data = $this->RolesModel->listar();
		print json_encode($data);
	}

	public function listarRolesForSelect()
	{
		$data = $this->RolesModel->listarRolesForSelect();
		print json_encode($data);
	}

	public function insertar()
	{
		$rol = trim($this->input->post("rol"));
		if(empty($rol)){
			print json_encode( array("insertado"=>"0","error"=>"1","msg"=>"El rol a guardar no puede ser vacío o nulo"));
			die();
		}else{
			$data = $this->RolesModel->insertar($rol);
			print json_encode($data);
		}

	}

	public function buscaRolById()
	{
		$id_rol = intval($this->input->post("id_rol"));
		if(empty($id_rol)){
			print json_encode( array("encontrado"=>"0","error"=>"1","msg"=>"No se recibieron los parametros esperados"));
			die();
		}else{
			$data = $this->RolesModel->buscaRolById($id_rol);
			print json_encode($data);
		}
		
	}

	public function updateRolById()
	{
		$id_rol = intval($this->input->post("id_rol"));
		$rol = trim($this->input->post("rol"));

		if(empty($id_rol) || empty($rol)){
			print json_encode( array("actualizado"=>"0","error"=>"1","msg"=>"No se recibieron los parametros esperados"));
			die();
		}else{
			$data = $this->RolesModel->updateRolById($id_rol,$rol);
			print json_encode($data);
		}
	}

	public function borrarRolById()
	{
		$id_rol = intval($this->input->post("id_rol"));
		if(empty($id_rol)){
			print json_encode( array("borrado"=>"0","error"=>"1","msg"=>"No se recibieron los parametros esperados"));
			die();
		}else{
			$data = $this->RolesModel->borrarRolById($id_rol);
			print json_encode($data);
		}
	}

}//class