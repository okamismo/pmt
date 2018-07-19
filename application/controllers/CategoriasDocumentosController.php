<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CategoriasDocumentosController extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model("CategoriasDocumentosModel");
	}

	public function listar()
	{
		$data = $this->CategoriasDocumentosModel->listar();
		print json_encode($data);
	}

	public function listarCategoriasDocumentosForSelect()
	{
		$data = $this->CategoriasDocumentosModel->listarCategoriasDocumentosForSelect();
		print json_encode($data);
	}

	public function insertar()
	{
		$nombre = trim($this->input->post("nombre"));
		if(empty($nombre)){
			print json_encode( array("insertado"=>"0","error"=>"1","msg"=>"El documento a guardar no puede ser vacío o nulo"));
			die();
		}else{
			$data = $this->CategoriasDocumentosModel->insertar($nombre);
			print json_encode($data);
		}

	}

	public function buscaDocumentoById()
	{
		$id_documento = intval($this->input->post("id_documento"));
		if(empty($id_documento)){
			print json_encode( array("encontrado"=>"0","error"=>"1","msg"=>"No se recibieron los parametros esperados"));
			die();
		}else{
			$data = $this->CategoriasDocumentosModel->buscaDocumentoById($id_documento);
			print json_encode($data);
		}
		
	}

	public function updateDocumentoById()
	{
		$id_documento = intval($this->input->post("id_documento"));
		$nombre = trim($this->input->post("nombre"));

		if(empty($id_documento) || empty($nombre)){
			print json_encode( array("actualizado"=>"0","error"=>"1","msg"=>"No se recibieron los parametros esperados"));
			die();
		}else{
			$data = $this->CategoriasDocumentosModel->updateDocumentoById($id_documento,$nombre);
			print json_encode($data);
		}
	}

	public function borrarDocumentoById()
	{
		$id_documento = intval($this->input->post("id_documento"));
		if(empty($id_documento)){
			print json_encode( array("borrado"=>"0","error"=>"1","msg"=>"No se recibieron los parametros esperados"));
			die();
		}else{
			$data = $this->CategoriasDocumentosModel->borrarDocumentoById($id_documento);
			print json_encode($data);
		}
	}

}//class