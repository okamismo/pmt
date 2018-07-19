<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CategoriasDocumentosModel extends CI_Model{

	function __construct()
	{
		parent::__construct();
		$this->load->library("Utilerias");
	}

	function listar()
	{
		$r = $this->db->get("categorias_documentos");
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

	function listarCategoriasDocumentosForSelect()
	{
		$r = $this->db->select("id_documento as value, nombre as label")->from("categorias_documentos")->get();
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

	function insertar($documento)
	{
		if($this->db->insert("categorias_documentos",array("nombre"=>$documento))){
			return array("insertado"=>"1","error"=>"0","msg"=>"El documento se ha guardado exitosamente");
		}else{
			return array("insertado"=>"0","error"=>"1","msg"=>"Ocurrio un problema y no fue posible guardar la categoria del documento");
		}
	}

	function buscaDocumentoById($id_documento)
	{
		$r = $this->db->select("*")->
			from("categorias_documentos")->
			where("id_documento",$id_documento)->
			get();

		if($r)
		{
			$d = $r->row();
			$data = $d;//$this->utilerias->utf8Encode($d);

			return $data;
		}
		else{
			return array("error"=>"1","msg"=>"Ocurrio un problema y no fue posible obtener la categoria del documento");
		}
	}

	function updateDocumentoById($id_documento,$documento)
	{
		$this->db->where("id_documento",$id_documento);
		if($this->db->update("categorias_documentos",array("nombre"=>$documento))) {
			return array("actualizado"=>"1","error"=>"0","msg"=>"Los cambios se han guardado exitosamente");
		}else{
			return array("actualizado"=>"0","error"=>"1","msg"=>"Ocurrio un problema y no fue posible guardar los cambios");
		}
	}

	function borrarDocumentoById($id_documento)
	{
		$this->db->where("id_documento",$id_documento);
		if($this->db->delete("categorias_documentos")) {
			return array("borrado"=>"1","error"=>"0","msg"=>"La categoria del documento ha sido borrado");
		}else{
			return array("borrado"=>"0","error"=>"1","msg"=>"Ocurrio un problema y no fue posible borrar la categoria del documento");
		}
	}

}