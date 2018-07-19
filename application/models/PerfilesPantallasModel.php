<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PerfilesPantallasModel extends CI_Model{

	function __construct()
	{
		parent::__construct();
		$this->load->library("Utilerias");
	}

	function listar($id_perfil)
	{
		$r = $this->db->select("pan.id_pantalla,pan.nombre_menu")->
			from("perfiles_pantallas as pp")->
			join("pantallas as pan","pp.id_pantalla=pan.id_pantalla")->
			join("perfiles as per","per.id_perfil=pp.id_perfil")->
			where("pp.id_perfil",$id_perfil)->
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

	function agregar($id_perfil, $ids_pantallas)
	{
		$this->db->trans_start();
		$len = count($ids_pantallas);
		for($i=0; $i<$len; $i++){
			$this->db->insert("perfiles_pantallas",array("id_perfil"=>$id_perfil,"id_pantalla"=>$ids_pantallas[$i]));
		}
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE)
		{
			return array("insertado"=>"0","error"=>"1","msg"=>"No se pudo completar la transacción...");
		}else{
			return array("insertado"=>"1","error"=>"0","msg"=>"Transacción completada...");
		}
	}

	function borrar($id_perfil, $ids_pantallas)
	{
		$this->db->trans_start();
		$len = count($ids_pantallas);
		for($i=0; $i<$len; $i++){
			$this->db->delete("perfiles_pantallas",array("id_perfil"=>$id_perfil,"id_pantalla"=>$ids_pantallas[$i]));
		}
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE)
		{
			return array("borrado"=>"0","error"=>"1","msg"=>"No se pudo completar la transacción...");
		}else{
			return array("borrado"=>"1","error"=>"0","msg"=>"Transacción completada...");
		}
	}

}