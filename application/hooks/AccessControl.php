<?php
class AccessControl 
{
	var $CI;
	function __construct(){
	    $this->CI =& get_instance();
        $data = json_decode(file_get_contents('php://input'), true);
        $_POST = ($data["data"] !== null && $data["data"] !== "")?$data["data"]:$_POST;
	}

	function verify()
    {
		
		$clase = $this->CI->router->class;
		if(!isset($this->CI->session->id_perfil) && $clase != "LoginController" && $clase != "AccesControlController")
        {
            print json_encode(array("sesionTerminada"=>"1","accesoNoAutorizado"=>"0"));
            redirect('/LoginController/vacio', 'refresh');
        }
        else if(isset($this->CI->session->id_perfil))
        {
        	
        	if($clase == "LoginController"){
        		return true;
        	}
        	$p = explode(",",$this->CI->session->pantallas);

	        if( !in_array($clase, $p)){
                print json_encode(array("accesoNoAutorizado"=>"1","sesionTerminada"=>"0"));
                redirect('/LoginController/vacio', 'refresh');
	        }
        	
        }

        return true;
	}//function
}//class