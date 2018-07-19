<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Utilerias
{
	function __construct()
	{

	}

	public static function utf8Encode(&$data)
    {
        if(is_array($data))
        {
            foreach ($data as $clave => &$records)
            {
                if(is_array($records))
                {
                    //print "1. <br>";
                    self::utf8Encode($records);
                }
                else
                {
                    
                    $records = str_replace("\t", " ", $records);
                    $records = str_replace("\r", " ", $records);
                    $records = str_replace("\n", " ", $records);
                    $records = str_replace('"', '', $records);
                    $records = utf8_encode($records);
                    //print "2. ".$records." <br>";
                    
                }
            }
        }
        
        else
        {
            
            $data = str_replace("\t", " ", $data);
            $data = str_replace("\r", " ", $data);
            $data = str_replace("\n", " ", $data);
            $data = str_replace('"', '', $data);
            $data = utf8_encode($data);
            //print "3. ".$data." <br>";
        }
        
        return $data;
    }//utf8Encode

    public static function utf8Decode(&$data)
    {
        if(is_array($data))
        {
            foreach ($data as $clave => &$records)
            {
                if(is_array($records))
                {
                    //print "1. <br>";
                    self::utf8Decode($records);
                }
                else
                {
                    
                    $records = str_replace("\t", " ", $records);
                    $records = str_replace("\r", " ", $records);
                    $records = str_replace("\n", " ", $records);
                    $records = str_replace('"', '', $records);
                    $records = utf8_decode($records);
                    //print "2. ".$records." <br>";
                    
                }
            }
        }
        
        else
        {
            
            $data = str_replace("\t", " ", $data);
            $data = str_replace("\r", " ", $data);
            $data = str_replace("\n", " ", $data);
            $data = str_replace('"', '', $data);
            $data = utf8_decode($data);
            //print "3. ".$data." <br>";
        }
        
        return $data;
    }//utf8Decode

    public static function esCurpValida($curp)
    {
        $re = '/^[A-Z]{1}[AEIOUX]{1}[A-Z]{2}[0-9]{2}(0[1-9]|1[0-2])(0[1-9]|1[0-9]|2[0-9]|3[0-1])[HM]{1}(AS|BC|BS|CC|CS|CH|CL|CM|DF|DG|GT|GR|HG|JC|MC|MN|MS|NT|NL|OC|PL|QT|QR|SP|SL|SR|TC|TS|TL|VZ|YN|ZS|NE)[B-DF-HJ-NP-TV-Z]{3}[0-9A-Z]{1}[0-9]{1}$/i';
        
        return (preg_match($re, $curp));
    }

    public static function esEmailValido($email)
    {
        $re = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/i';
        return (preg_match($re, $email));
    }

    public static function esCelularValido($cel)
    {
        $re = '/^[0-9]{10}$/';
        return (preg_match($re, $cel));
    }

    public static function esCodigoPostalValido($cp)
    {
        $re = '/^[0-9]{5}$/';
        return (preg_match($re, $cp));
    }

}