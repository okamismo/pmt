<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PersonasController extends CI_Controller {

	private $error;
	function __construct()
	{
		parent::__construct();
		$this->load->model("PersonasModel");
		$this->load->library("Utilerias");
	}

	public function listar()
	{
		$data = $this->PersonasModel->listar();
		print json_encode($data);
	}

	public function getPersonaById()
	{
		$id_persona = intval($this->input->post("id_persona"));
		$data = $this->PersonasModel->getPersonaById($id_persona);
		print json_encode($data);
	}

	private function validar(&$d,$tipo)
	{
		if(empty($d["paterno"])){
			$this->error = "El apellido paterno es un campo obligatorio";
			return false;
		}
		if(empty($d["materno"])){
			$this->error = "El apellido materno es un campo obligatorio";
			return false;
		}
		if(empty($d["nombre"])){
			$this->error = "El nombre es un campo obligatorio";
			return false;
		}
		if(!$this->utilerias->esEmailValido($d["email"])){
			$this->error = "El email debe cumplir con un formato valido";
			return false;
		}
		if($tipo == "insertar")
		{
			if(empty($d["pass"])){
				$this->error = "La contraseña es un campo obligatorio";
				return false;
			}
		}
		else if($tipo == "update")
		{
			if(empty($d["id_persona"])){
				$this->error = "Parametros incompletos";
				return false;
			}
			if(empty($d["pass"])){
				unset($d["pass"]);//si esta vacia no se debe sobreescribir
			}
		}

		if(!empty($d["pass"])){
			$hash = password_hash($d["pass"],PASSWORD_DEFAULT);
			if($hash === false){
				$this->error = "Ocurrio un problema al intentar crear la contraseña";
				return false;
			}
			$d["pass"] = $hash;
		}

		return true;

	}

	public function insertar()
	{
		$d["paterno"] = trim($this->input->post("paterno"));
		$d["materno"] = trim($this->input->post("materno"));
		$d["nombre"] = trim($this->input->post("nombre"));
		$d["email"] = trim($this->input->post("email"));
		$d["pass"] = trim($this->input->post("pass"));
		$d["id_perfil"] = intval($this->input->post("id_perfil"));

		if($this->validar($d,"insertar"))
		{
			$data = $this->PersonasModel->insertar($d);
			print json_encode($data);
			die();
		}else{
			print json_encode(array("error"=>"1","insertado"=>"0","msg"=>$this->error));
			die();
		}
	}

	public function update()
	{
		$d["id_persona"] = intval($this->input->post("id_persona"));
		$d["paterno"] = trim($this->input->post("paterno"));
		$d["materno"] = trim($this->input->post("materno"));
		$d["nombre"] = trim($this->input->post("nombre"));
		$d["email"] = trim($this->input->post("email"));
		$d["pass"] = trim($this->input->post("pass"));
		$d["id_perfil"] = intval($this->input->post("id_perfil"));

		if($this->validar($d,"update"))
		{
			$data = $this->PersonasModel->update($d);
			print json_encode($data);
			die();
		}else{
			print json_encode(array("error"=>"1","actualizado"=>"0","msg"=>$this->error));
			die();
		}
	}

	public function borrarById()
	{
		$id_persona = intval($this->input->post("id_persona"));
		if(empty($id_persona)){
			print json_encode( array("borrado"=>"0","error"=>"1","msg"=>"No se recibieron los parametros esperados"));
			die();
		}else{
			$data = $this->PersonasModel->borrarById($id_persona);
			print json_encode($data);
		}
	}

}