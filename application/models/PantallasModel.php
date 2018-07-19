<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PantallasModel extends CI_Model{

	function __construct()
	{
		parent::__construct();
		$this->load->library("Utilerias");
	}

	function listar()
	{
		$r = $this->db->get("pantallas");
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

	function insertar($p)
	{
		if($this->db->insert("pantallas",$p)){
			return array("insertado"=>"1","error"=>"0","msg"=>"La pantalla se ha guardado exitosamente");
		}else{
			return array("insertado"=>"0","error"=>"1","msg"=>"Ocurrio un problema y no fue posible guardar la pantalla");
		}
	}

	function buscaById($id_pantalla)
	{
		$r = $this->db->select("*")->
			from("pantallas")->
			where("id_pantalla",$id_pantalla)->
			get();

		if($r)
		{
			$d = $r->row();
			$data = $d;//$this->utilerias->utf8Encode($d);

			return $data;
		}
		else{
			return array("error"=>"1","msg"=>"Ocurrio un problema y no fue posible obtener la pantalla");
		}
	}

	function updateById($p)
	{
		$this->db->where("id_pantalla",$p["id_pantalla"]);
		unset($p["id_pantalla"]);
		if($this->db->update("pantallas",$p)) {
			return array("actualizado"=>"1","error"=>"0","msg"=>"Los cambios se han guardado exitosamente");
		}else{
			return array("actualizado"=>"0","error"=>"1","msg"=>"Ocurrio un problema y no fue posible guardar los cambios");
		}
	}

	function borrarById($id_pantalla)
	{
		$this->db->where("id_pantalla",$id_pantalla);
		if($this->db->delete("pantallas")) {
			return array("borrado"=>"1","error"=>"0","msg"=>"La pantalla ha sido borrado");
		}else{
			return array("borrado"=>"0","error"=>"1","msg"=>"Ocurrio un problema y no fue posible borrar la pantalla");
		}
	}

	function listaMenusPadres()
	{
		$r = $this->db->select("id_pantalla as value,nombre_menu as label")->
			from("pantallas")->
			where("tiene_hijos is not null and tiene_hijos <> '0'")->
			get();

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

}