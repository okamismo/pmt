<?php
defined('BASEPATH') OR exit('No direct script access allowed');

//Role Base Access Control
class RBAC
{
	var $CI;
	function __construct()
	{
		$this->CI =& get_instance();
	}

	function hasUserProject($id_proyecto)
	{
		//los administradores del sistema tienen acceso a todos los proyectos
		if($this->CI->session->id_perfil === PMT_ADMINISTRADOR){
			return TRUE;
		}

		$id_proyecto = intval($id_proyecto);
		$r = $this->CI->db->where("id_proyecto", $id_proyecto)->
			where("id_persona", $this->CI->session->id_persona)->
			get("proyectos_personas");

		if($r){
			if($r->num_rows()==1){
				return TRUE;
			}else{
				return FALSE;
			}
		}else{
			return FALSE;
		}
	}

}