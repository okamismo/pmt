<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PerfilesModel extends CI_Model{

	function __construct()
	{
		parent::__construct();
		$this->load->library("Utilerias");
	}

	function listarPerfiles()
	{
		$r = $this->db->get("perfiles");
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

	function listarPerfilesForSelect()
	{
		$r = $this->db->select("id_perfil as value, perfil as label")->from("perfiles")->get();
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

	function insertarPerfil($perfil)
	{
		if($this->db->insert("perfiles",array("perfil"=>$perfil))){
			return array("insertado"=>"1","error"=>"0","msg"=>"El perfil se ha guardado exitosamente");
		}else{
			return array("insertado"=>"0","error"=>"1","msg"=>"Ocurrio un problema y no fue posible guardar el perfil");
		}
	}

	function buscaPerfilById($id_perfil)
	{
		$r = $this->db->select("*")->
			from("perfiles")->
			where("id_perfil",$id_perfil)->
			get();

		if($r)
		{
			$d = $r->row();
			$data = $d;//$this->utilerias->utf8Encode($d);

			return $data;
		}
		else{
			return array("error"=>"1","msg"=>"Ocurrio un problema y no fue posible obtener el perfil");
		}
	}

	function updatePerfilById($id_perfil,$perfil)
	{
		$this->db->where("id_perfil",$id_perfil);
		if($this->db->update("perfiles",array("perfil"=>$perfil))) {
			return array("actualizado"=>"1","error"=>"0","msg"=>"Los cambios se han guardado exitosamente");
		}else{
			return array("actualizado"=>"0","error"=>"1","msg"=>"Ocurrio un problema y no fue posible guardar los cambios");
		}
	}

	function borrarPerfilById($id_perfil)
	{
		$this->db->where("id_perfil",$id_perfil);
		if($this->db->delete("perfiles")) {
			return array("borrado"=>"1","error"=>"0","msg"=>"El perfil ha sido borrado");
		}else{
			return array("borrado"=>"0","error"=>"1","msg"=>"Ocurrio un problema y no fue posible borrar el perfil");
		}
	}

}