<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ChatGruposController extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model("ChatGruposModel");
    }
    
    function getPersonasDeProyecto()
    {
        $id_proyecto = intval($this->input->post("id_proyecto"));
        $data = $this->ChatGruposModel->getPersonasDeProyecto($id_proyecto);

		print json_encode($data);
    }

    private function validar($data) {
        $error = "";
        if(empty($data["id_proyecto"])) {
            $error = "No se recibieron los parametros esperados...";
        } else if(empty($data["nombre"])) {
            $error = "El nombre del grupo es obligatorio";
        } else if(empty($data["integrantes"])) {
            $error = "Se debe seleccionar al menos un integrante";
        }

        if($error != "") {
            return array("error"=>"1", "msg"=>$error);
        }
        
        $data["id_proyecto"] = intval($data["id_proyecto"]);
        $data["nombre"] = trim($data["nombre"]);
        $data["integrantes"] = explode(",", $data["integrantes"]);
        return $data;
    }

    function guardarGrupo()
    {
        $data["nombre"] = $this->input->post("nombre");
        $data["id_proyecto"] = $this->input->post("id_proyecto");
        $data["integrantes"] = $this->input->post("integrantes");
        
        $datos = $this->validar($data);
        if( isset($datos["error"]) && $datos["error"]=="1") {
            print json_encode($datos);
            die();
        } else {
            $data = $this->ChatGruposModel->guardarGrupo($datos);
            print json_encode($data);
        }
        
    }
}

