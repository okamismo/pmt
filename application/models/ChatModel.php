<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ChatModel extends CI_Model{

	function __construct()
	{
		parent::__construct();
		$this->load->library("Utilerias");
    }
    
    private function getPersonasDeProyecto($id_proyecto)
    {
        $r = $this->db->select("p.id_persona,p.paterno,p.materno,p.nombre,p.email,p.avatar")->
            from("personas as p")->
            join("proyectos_personas as pp","p.id_persona=pp.id_persona")->
            where("pp.id_proyecto",$id_proyecto)->
            where("p.id_persona <> ".$this->session->id_persona)->
            get();
        
        if($r)
        {
            $d = $r->result_array();
            $data = $d;//$this->utilerias->utf8Encode($d);
            return $data;
        }else{
            return array();
        }
    }

    function listarChats($id_proyecto)
    {
        $personas = $this->getPersonasDeProyecto($id_proyecto);
        return $personas;
    }

}