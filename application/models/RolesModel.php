<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class RolesModel extends CI_Model{

	function __construct()
	{
		parent::__construct();
		$this->load->library("Utilerias");
	}

	function listar()
	{
		$r = $this->db->get("roles");
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

	function listarRolesForSelect()
	{
		$r = $this->db->select("id_rol as value, rol as label")->from("roles")->get();
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

	function insertar($rol)
	{
		if($this->db->insert("roles",array("rol"=>$rol))){
			return array("insertado"=>"1","error"=>"0","msg"=>"El rol se ha guardado exitosamente");
		}else{
			return array("insertado"=>"0","error"=>"1","msg"=>"Ocurrio un problema y no fue posible guardar el rol");
		}
	}

	function buscaRolById($id_rol)
	{
		$r = $this->db->select("*")->
			from("roles")->
			where("id_rol",$id_rol)->
			get();

		if($r)
		{
			$d = $r->row();
			$data = $d;//$this->utilerias->utf8Encode($d);

			return $data;
		}
		else{
			return array("error"=>"1","msg"=>"Ocurrio un problema y no fue posible obtener el rol");
		}
	}

	function updateRolById($id_rol,$rol)
	{
		$this->db->where("id_rol",$id_rol);
		if($this->db->update("roles",array("rol"=>$rol))) {
			return array("actualizado"=>"1","error"=>"0","msg"=>"Los cambios se han guardado exitosamente");
		}else{
			return array("actualizado"=>"0","error"=>"1","msg"=>"Ocurrio un problema y no fue posible guardar los cambios");
		}
	}

	function borrarRolById($id_rol)
	{
		$this->db->where("id_rol",$id_rol);
		if($this->db->delete("roles")) {
			return array("borrado"=>"1","error"=>"0","msg"=>"El rol ha sido borrado");
		}else{
			return array("borrado"=>"0","error"=>"1","msg"=>"Ocurrio un problema y no fue posible borrar el rol");
		}
	}

}