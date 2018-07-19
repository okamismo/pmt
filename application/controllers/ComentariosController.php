<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ComentariosController extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model("ComentariosModel");
	}

	public function listar()
	{
		$id_paquete = intval($this->input->post("id_paquete"));
		$data = $this->ComentariosModel->listar($id_paquete);
		print json_encode($data);
	}

	public function buscarNotaById()
	{
		$id_nota = intval($this->input->post("id_nota"));
		$data = $this->ComentariosModel->buscarNotaById($id_nota);
		print json_encode($data);
	}
	
	public function insertar()
	{
		$id_paquete = intval($this->input->post("id_paquete"));
		$nota = trim($this->input->post("nota"));
		$data = $this->ComentariosModel->insertar($id_paquete,$nota);
		print json_encode($data);
	}

	public function actualizar()
	{
		$id_nota = intval($this->input->post("id_nota"));
		$nota = trim($this->input->post("nota"));
		$data = $this->ComentariosModel->actualizar($id_nota,$nota);
		print json_encode($data);
	}

	public function borrar()
	{
		$id_nota = intval($this->input->post("id_nota"));
		$data = $this->ComentariosModel->borrar($id_nota);
		print json_encode($data);
	}

}