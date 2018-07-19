<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class LoginController extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model("LoginModel");
	}

	public function index()
	{
		$this->load->view('main');
	}

	public function validarCredenciales()
	{
		//$_POST = json_decode(file_get_contents('php://input'), true);
		$usr = $this->input->post("usr");
		$pass = $this->input->post("pass");

		$data = $this->LoginModel->validarCredenciales($usr,$pass);
		print json_encode($data);
	}

	public function getMenu()
	{
		$data = $this->LoginModel->getMenu();
		print json_encode($data);	
	}

	public function getProyectos()
	{
		$data = $this->LoginModel->getProyectos();
		print json_encode($data);
	}

	public function cerrarSesion()
	{
		$this->session->sess_destroy();
	}

	public function vacio(){
		
	}

	public function getAvatar()
	{
		print json_encode(array("src"=>$this->session->avatar));
	}
}
