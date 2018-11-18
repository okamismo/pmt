<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ChatGruposModel extends CI_Model{

    const AVATARS = 'client/src/statics/avatars/';
    const AVATARS_RELATIVO = 'statics/avatars/';
	function __construct()
	{
		parent::__construct();
		$this->load->library("Utilerias");
    }
    
    public function getPersonasDeProyecto($id_proyecto)
    {
        $this->load->library("StandarizedSql");
		$concatenacion = $this->standarizedsql->concatenar(array("p.nombre","p.paterno"),"label");
        $this->db->select("p.id_persona as value,p.email,p.avatar,".$concatenacion);
        
        $r = $this->db->from("personas as p")->
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

    private function guardaArchivo($key) {
        $this->load->library("Uploader");
        $this->uploader->setCarpeta(FCPATH.self::AVATARS);
        $this->uploader->setAllowedTypes("jpg|jpeg|png|gif|ico");
        
        $r = $this->uploader->uploadFile($key);
        return $r;
    }

    function guardarGrupo($datos)
    {
        $r = $this->guardaArchivo($_FILES[0]["name"]);
        $fileName = null;
        if($r["error"] == "1") {
            $fileName = $r["docto"];
        }
        
        $this->db->trans_start();

        $datos = array("nombre"=>$datos["nombre"], "avatar"=>self::AVATARS_RELATIVO.$fileName);
        $this->db->insert("chat_grupos", $datos);
        $id_grupo = $this->db->insert_id();
        
        if($id_grupo > 0)
        {

            $len = count($datos["integrantes"]);
            for($i=0; $i<$len; $i++) {
                $reg = array("id_grupo"=>$id_grupo, "id_persona"=>$datos["integrantes"][$i]);
                $this->db->insert("chat_personas_grupos", $reg);
            }

            $reg = array("id_proyecto"=>$datos["id_proyecto"], "id_chat_grupo"=>$id_grupo);
            $this->db->insert("proyectos_chats_grupos", $reg);
            
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE)
            {
                return array("error"=>"1","msg"=>"No fue posible guardar el grupo");
            } else {
                return array("error"=>"0","msg"=>"Grupo guardado");
            }
            
        } else {
            $this->db->trans_complete();
            return array("error"=>"1","msg"=>"No fue posible guardar el grupo");
        }
  
    }//function

}