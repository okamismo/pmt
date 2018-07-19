<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DocumentosController extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model("DocumentosModel");
	}

	public function listar()
	{
		$data = $this->DocumentosModel->listar();
		print json_encode($data);
	}

	public function asociarDoctoPaquete()
	{
		$id_paquete = intval($this->input->post("id_paquete"));
		$descripcion = trim($this->input->post("descripcion"));
		$id_categoria = intval($this->input->post("id_categoria"));

		if(empty($id_paquete) || empty($descripcion) || empty($id_categoria)) {
			print json_encode( array("error"=>"1", "msg"=>"No se recibieron los parametros esperados") );
			die();
		}else{
			$data = $this->DocumentosModel->asociarDoctoPaquete($id_paquete,$id_categoria,$descripcion);
			print json_encode($data);
		}
	
	}

	function borrarDoctoPaquete()
	{
		$id = intval($this->input->post("id"));
		if(empty($id)){
			print json_encode( array("error"=>"1", "msg"=>"No se recibieron los parametros esperados") );
			die();
		}else{
			$data = $this->DocumentosModel->borrarDoctoPaquete($id);
			print json_encode($data);
		}
	}

	public function descargarArchivo()
	{
		$id = intval($this->input->get("id"));
		$nombre = trim($this->input->get("archivo"));
		if(empty($id) || empty($nombre)){
			print "Archivo no encontrado (1)";
			die();
		}else{
			$file = $this->DocumentosModel->descargarArchivo($id,$nombre);
			if($file)
			{
				if(file_exists($file))
				{
					header("Content-type: application/octet-stream");
				    header("Content-Disposition: attachment; filename='".$nombre."'");
				    header('Content-length: '.(string)(filesize($file)));
				    $fp=fopen($file, "r");
				    fpassthru($fp);
				}
			}else{
				print "Archivo no encontrado (2)";
				die();
			}

		}
	}

}