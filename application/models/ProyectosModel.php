<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ProyectosModel extends CI_Model{

	function __construct()
	{
		parent::__construct();
		$this->load->library("Utilerias");
	}

	function listar()
	{
		$r = $this->db->get("proyectos");
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

	function listarProyectosForSelect()
	{
		$r = $this->db->select("id_proyecto as value, nombre as label")->from("proyectos")->get();
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

	function getProyectoById($id_proyecto)
	{
		$this->db->where("id_proyecto",$id_proyecto);
		$r = $this->db->get("proyectos");
		if($r)
		{
			$d = $r->row();
			$data = $d;//$this->utilerias->utf8Encode($d);

			return $data;
		}
		else{
			return array("error"=>"1");
		}
	}

	function insertar($d)
	{
		if($this->db->insert("proyectos",$d)){
			return array("error"=>"0","insertado"=>"1","msg"=>"El registro ha sido guardado...");
		}else{
			return array("error"=>"1","insertado"=>"0","msg"=>"No fue posible guardar el registro...");
		}
	}

	function update($d)
	{
		$this->db->where("id_proyecto",$d["id_proyecto"]);
		unset($d["id_proyecto"]);
		if($this->db->update("proyectos",$d)){
			return array("error"=>"0","actualizado"=>"1","msg"=>"El registro ha sido guardado...");
		}else{
			return array("error"=>"1","actualizado"=>"0","msg"=>"No fue posible guardar el registro...");
		}
	}

	function borrarById($id_proyecto)
	{
		$this->db->where("id_proyecto",$id_proyecto);
		if($this->db->delete("proyectos")) {
			return array("borrado"=>"1","error"=>"0","msg"=>"El registro ha sido borrado");
		}else{
			return array("borrado"=>"0","error"=>"1","msg"=>"Ocurrio un problema y no fue posible borrar el registro");
		}
	}

}