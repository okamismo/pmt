<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Uploader
{
	protected $CI;
	private $carpeta = '';
	private $allowedTypes;

	function __construct()
	{
		$this->CI =& get_instance();
	}

	public function setAllowedTypes($types)
	{
		$this->allowedTypes = $types;
	}

	public function setCarpeta($carpeta)
	{
		$this->carpeta = $carpeta;
	}

	public function uploadFile($index)
	{
		$file = $_FILES[$index];
		if(empty($this->allowedTypes)){
			return array("error"=>"1","msg"=>"Error en el tipo de archivo (1)");
		}
		if(count($file)<=0){
			return array("error"=>"1","msg"=>"Error Archivo vacio (2)");
		}

		$this->CI->load->helper("string");
		$fileName = "";
		
		$config = array();

		$config["upload_path"] = $this->carpeta;
		$config['allowed_types'] = $this->allowedTypes;
		$config['max_size'] = "2048";//2MB

		$micro = str_replace(".","",microtime()); 
		$micro = str_replace(" ","",$micro)."_".random_string("alpha",10);
		
		$ext = substr($file["name"],strripos($file["name"],"."));
		$config['file_name'] = $micro.$ext;
		$fileName = $config['file_name'];
		//print_r($config);

		$this->CI->load->library('upload', $config);
		$this->CI->upload->initialize($config);

		//si no se sube:
		if(!$this->CI->upload->do_upload($index)){
			return array("error"=>"1","msg"=>"No fue posible guardar el archivo: ".$fileName,"reason"=>$this->CI->upload->display_errors());
		}else
		{
			$this->CI->upload = null;
			unset($this->CI->upload);
		}

		return array("error"=>"0","ruta"=>$this->carpeta,"docto"=>$fileName);
	}


}//class