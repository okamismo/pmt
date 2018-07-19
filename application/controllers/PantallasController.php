<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PantallasController extends CI_Controller {

	private $_error = false;
	function __construct()
	{
		parent::__construct();
		$this->load->model("PantallasModel");
	}

	public function listar()
	{
		$data = $this->PantallasModel->listar();
		print json_encode($data);
	}

	private function validar($esUpdate)
	{
		$params["nombre_menu"] = trim($this->input->post("nombre_menu"));
		$params["descripcion"] = trim($this->input->post("descripcion"));
		$params["controlador"] = trim($this->input->post("controlador"));
		$params["icono"] = trim($this->input->post("icono"));
		$params["uri"] = trim($this->input->post("uri"));
		$params["tiene_hijos"] = trim($this->input->post("tiene_hijos"));
		$params["id_padre"] = trim($this->input->post("id_padre"));

		if($esUpdate){
			$params["id_pantalla"] = intval($this->input->post("id_pantalla"));
			if(empty($params["id_pantalla"])){
				$this->_error = true;
				return json_encode( array("insertado"=>"0","error"=>"1","msg"=>"El id de pantalla no puede ser vacío o nulo"));
			}
		}

		if(empty($params["nombre_menu"])){
			$this->_error = true;
			return json_encode( array("insertado"=>"0","error"=>"1","msg"=>"El nombre del menu a guardar no puede ser vacío o nulo"));
		}else if(empty($params["descripcion"])){
			$this->_error = true;
			return json_encode( array("insertado"=>"0","error"=>"1","msg"=>"La descripcion del menu a guardar no puede ser vacío o nulo"));
		}
		else{
			return $params;
		}

	}

	public function insertar()
	{
		$params = $this->validar("0");
		if($this->_error) {
			print $params;
			die();
		}
		else{
			$data = $this->PantallasModel->insertar($params);
			print json_encode($data);
		}
	}

	public function buscaById()
	{
		$id_pantalla = intval($this->input->post("id_pantalla"));
		if(empty($id_pantalla)){
			print json_encode( array("encontrado"=>"0","error"=>"1","msg"=>"No se recibieron los parametros esperados"));
			die();
		}else{
			$data = $this->PantallasModel->buscaById($id_pantalla);
			print json_encode($data);
		}
		
	}

	public function updateById()
	{
		$params = $this->validar("1");
		if($this->_error) {
			print $params;
			die();
		}else{
			$data = $this->PantallasModel->updateById($params);
			print json_encode($data);
		}
	}

	public function borrarById()
	{
		$id_pantalla = intval($this->input->post("id_pantalla"));
		if(empty($id_pantalla)){
			print json_encode( array("borrado"=>"0","error"=>"1","msg"=>"No se recibieron los parametros esperados"));
			die();
		}else{
			$data = $this->PantallasModel->borrarById($id_pantalla);
			print json_encode($data);
		}
	}

	public function listaMenusPadres()
	{
		$data = $this->PantallasModel->listaMenusPadres();
		print json_encode($data);
	}

}//class