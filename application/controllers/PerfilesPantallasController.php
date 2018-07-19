<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PerfilesPantallasController extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model("PerfilesPantallasModel");
	}

	public function listar()
	{
		$id_perfil = intval($this->input->post("id_perfil"));
		if(empty($id_perfil)){
			print json_encode(array("encontrado"=>"0","error"=>"1","msg"=>"El id de pantalla no puede ser vacío o nulo"));
			die();
		}else{
			$data = $this->PerfilesPantallasModel->listar($id_perfil);
			print json_encode($data);
		}
		
	}

	public function agregar()
	{
		$id_perfil = intval($this->input->post("id_perfil"));
		$ids_pantallas = $this->input->post("ids");

		if(empty($id_perfil) || empty($ids_pantallas)){
			print json_encode(array("insertado"=>"0","error"=>"1","msg"=>"Los parametros esperados no fueron recibidos"));
			die();
		}else{
			$data = $this->PerfilesPantallasModel->agregar($id_perfil,$ids_pantallas);
			print json_encode($data);
		}
	}

	public function borrar()
	{
		$id_perfil = intval($this->input->post("id_perfil"));
		$ids_pantallas = $this->input->post("ids");

		if(empty($id_perfil) || empty($ids_pantallas)){
			print json_encode(array("insertado"=>"0","error"=>"1","msg"=>"Los parametros esperados no fueron recibidos"));
			die();
		}else{
			$data = $this->PerfilesPantallasModel->borrar($id_perfil,$ids_pantallas);
			print json_encode($data);
		}
	}

}