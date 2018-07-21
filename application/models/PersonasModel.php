<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PersonasModel extends CI_Model{

	const AVATARS = 'client/src/statics/avatars/';
	function __construct()
	{
		parent::__construct();
		$this->load->library("Utilerias");
	}

	function listar()
	{
		$r = $this->db->get("personas");
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

	function getPersonaById($id_persona)
	{
		$this->db->where("id_persona",$id_persona);
		$r = $this->db->get("personas");
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
		if($this->db->insert("personas",$d)){
			return array("error"=>"0","insertado"=>"1","msg"=>"El registro ha sido guardado...");
		}else{
			return array("error"=>"1","insertado"=>"0","msg"=>"No fue posible guardar el registro...");
		}
	}

	function update($d)
	{
		$this->db->where("id_persona",$d["id_persona"]);
		unset($d["id_persona"]);
		if($this->db->update("personas",$d)){
			return array("error"=>"0","actualizado"=>"1","msg"=>"El registro ha sido guardado...");
		}else{
			return array("error"=>"1","actualizado"=>"0","msg"=>"No fue posible guardar el registro...");
		}
	}

	function borrarById($id_persona)
	{
		$this->db->where("id_persona",$id_persona);
		if($this->db->delete("personas")) {
			return array("borrado"=>"1","error"=>"0","msg"=>"La persona ha sido borrada");
		}else{
			return array("borrado"=>"0","error"=>"1","msg"=>"Ocurrio un problema y no fue posible borrar la persona");
		}
	}

	function guardarAvatar($id_persona)
	{
		$this->load->library("Uploader");
		$this->uploader->setCarpeta(FCPATH.self::AVATARS);
		$this->uploader->setAllowedTypes("jpg|jpeg|png|gif");

		foreach($_FILES as $key => $val)
		{
			$r = $this->uploader->uploadFile($key);

			if($r["error"] != "1")
			{
				$datos = array("avatar"=>"statics/avatars/".$r["docto"]);
				if($this->db->update("personas",$datos,"id_persona=".$id_persona)){
					return array("error"=>"0","msg"=>"Avatar guardado","avatar"=>"statics/avatars/".$r["docto"]);
				}else{
					return array("error"=>"1","msg"=>"No fue posible guardar el avatar");
				}
				
			}
			else{
				return array("error"=>"1","msg"=>"No fue posible guardar el avatar");
			}
		}
	}

}