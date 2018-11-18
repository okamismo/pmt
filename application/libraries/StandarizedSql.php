<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class StandarizedSql
{
    var $CI;
	function __construct()
	{
        $this->CI =& get_instance();
	}

    function concatenar($campos,$alias)
    {
        //para MS SQL SERVER
        if($this->CI->db->platform() == "mssql" || $this->CI->db->platform() == "sqlsrv") {
            $union = implode("+' '+",$campos);
            $union .= ' as '.$alias;
            return $union;
			//$this->CI->db->select("per.id_persona,(per.nombre) + ' ' + (per.paterno) as nombre");
		}else{
            //para MYSQL
            $union = 'concat(';
            $len = count($campos);
            for($i=0; $i<$len; $i++) {
                $union .= $campos[$i].",' ',";
            }
            //obtenemos la posicion de las comas y el espacio sobrantes
            //y extraemos solo la parte restante
            $union = substr($union,0, (strlen($union))-5);
            $union .= ") as ".$alias;

            return $union;
			//$this->CI->db->select("per.id_persona,concat(per.nombre, ' ',per.paterno) as nombre");
		}
    }

}