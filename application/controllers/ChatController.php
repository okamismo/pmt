<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ChatController extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model("ChatModel");
    }
    
    function listarChats()
    {
        $id_proyecto = intval($this->input->post("id_proyecto"));
        $data = $this->ChatModel->listarChats($id_proyecto);

		print json_encode($data);
    }
}