<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PerfilesController extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model("PerfilesModel");
	}

	public function listarPerfiles()
	{
		$data = $this->PerfilesModel->listarPerfiles();
		print json_encode($data);
	}

	public function listarPerfilesForSelect()
	{
		$data = $this->PerfilesModel->listarPerfilesForSelect();
		print json_encode($data);
	}

	public function insertarPerfil()
	{
		$perfil = trim($this->input->post("perfil"));
		if(empty($perfil)){
			print json_encode( array("insertado"=>"0","error"=>"1","msg"=>"El perfil a guardar no puede ser vacío o nulo"));
			die();
		}else{
			$data = $this->PerfilesModel->insertarPerfil($perfil);
			print json_encode($data);
		}

	}

	public function buscaPerfilById()
	{
		$id_perfil = intval($this->input->post("id_perfil"));
		if(empty($id_perfil)){
			print json_encode( array("encontrado"=>"0","error"=>"1","msg"=>"No se recibieron los parametros esperados"));
			die();
		}else{
			$data = $this->PerfilesModel->buscaPerfilById($id_perfil);
			print json_encode($data);
		}
		
	}

	public function updatePerfilById()
	{
		$id_perfil = intval($this->input->post("id_perfil"));
		$perfil = trim($this->input->post("perfil"));

		if(empty($id_perfil) || empty($perfil)){
			print json_encode( array("actualizado"=>"0","error"=>"1","msg"=>"No se recibieron los parametros esperados"));
			die();
		}else{
			$data = $this->PerfilesModel->updatePerfilById($id_perfil,$perfil);
			print json_encode($data);
		}
	}

	public function borrarPerfilById()
	{
		$id_perfil = intval($this->input->post("id_perfil"));
		if(empty($id_perfil)){
			print json_encode( array("borrado"=>"0","error"=>"1","msg"=>"No se recibieron los parametros esperados"));
			die();
		}else{
			$data = $this->PerfilesModel->borrarPerfilById($id_perfil);
			print json_encode($data);
		}
	}

}//class