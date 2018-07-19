<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class LoginModel extends CI_Model{

	function __construct()
	{
		parent::__construct();
		$this->load->library("Utilerias");
	}

	private function getUserPass($usr)
	{
		$r = $this->db->select("pass")->
			from("personas")->
			where("email",$usr)->
			get();

		if($r->num_rows()>0)
		{
			return $r->row()->pass;
		}else{
			return "";
		}
	}

	public function validarCredenciales($usr,$pass)
	{
		$hash = $this->getUserPass($usr);
		if(password_verify($pass, $hash))
		{
			$r = $this->db->select("p.id_persona,paterno,materno,nombre,email,per.id_perfil,per.perfil,p.avatar")->
			from("personas as p")->
			join("perfiles as per","p.id_perfil=per.id_perfil")->
			where("email",$usr)->
			get();
			
			if($r->num_rows()>0)
			{
				$d = $r->row();

				$this->session->set_userdata("id_persona",$d->id_persona);
				$this->session->set_userdata("paterno",$d->paterno);
				$this->session->set_userdata("materno",$d->materno);
				$this->session->set_userdata("nombre",$d->nombre);
				$this->session->set_userdata("email",$d->email);
				$this->session->set_userdata("id_perfil",$d->id_perfil);
				$this->session->set_userdata("perfil",$d->perfil);
				$this->session->set_userdata("avatar",$d->avatar);
				$this->session->avatar = (empty($this->session->avatar))?"fas fa-user-circle":$this->session->avatar;

				$r = $this->db->select("p.controlador")->
					from("perfiles_pantallas as pp")->
					join("pantallas as p","pp.id_pantalla=p.id_pantalla")->
					where("pp.id_perfil",$this->session->id_perfil)->
					get();

				$this->session->set_userdata('pantallas','');
				$len = $r->num_rows();
				if($len>0)
				{
					$d = $r->result_array();
					
					for($i=0;$i<$len; $i++){
						$this->session->pantallas .= $d[$i]["controlador"].",";
					}
				}

				$r = $this->db->select("clase")->
					from("perfiles_clases_autorizadas")->
					where("id_perfil",$this->session->id_perfil)->
					get();

				$len = $r->num_rows();
				if($len>0)
				{
					$d = $r->result_array();
					
					for($i=0;$i<$len; $i++){
						$this->session->pantallas .= $d[$i]["clase"].",";
					}
				}

				//var_dump($this->session); die();

				return array("error"=>"0","valido"=>"1","baseUrl"=>base_url(),"id_perfil"=>$this->session->id_perfil);
				session_write_close();

			}else{
				return array("error"=>"1","valido"=>"0","msg"=>"No se encontro el usuario","sql"=>$this->db->last_query());
			}
		}else{
			return array("error"=>"0","valido"=>"0");
		}
		
	}//function

	function getMenu()
	{
		//obtenemos los enlaces
		$r = $this->db->select('p.id_pantalla,p.controlador,p.nombre_menu,p.descripcion,p.icono,p.uri')->
			from("pantallas as p")->
			join("perfiles_pantallas as pp","p.id_pantalla=pp.id_pantalla")->
			where("pp.id_perfil",$this->session->id_perfil)->
			where("(p.id_padre is null or p.id_padre='0') and (p.tiene_hijos is null or p.tiene_hijos='0')")->
			get();
			//die($this->db->last_query());

		if($r)
		{
			$enlaces = $r->result_array();
			$sql = array();

			//obtenemos los menus padres
			$r = $this->db->select('p.id_pantalla,p.controlador,p.nombre_menu,p.descripcion,p.icono')->
				from("pantallas as p")->
				join("perfiles_pantallas as pp","p.id_pantalla=pp.id_pantalla")->
				where("pp.id_perfil",$this->session->id_perfil)->
				where("(p.id_padre is null or p.id_padre='0')")->
				where("p.tiene_hijos","1")->
				get();
				
			if($r)
			{
				$menus["menus"] = $r->result_array();
				$len = count($menus["menus"]);
				
				for($i=0; $i<$len; $i++){
					//obtenemos los submenus
					try{
						
						$r = $this->db->select("p.id_pantalla,p.nombre_menu,p.controlador,p.uri")->
						from("pantallas as p")->
						join("perfiles_pantallas as pp","p.id_pantalla=pp.id_pantalla")->
						where("pp.id_perfil",$this->session->id_perfil)->
						where("p.id_padre",$menus["menus"][$i]["id_pantalla"])->
						get();
						
						if($r)
						{
							$sub = $r->result_array();
							$menus["menus"][$i]["hijos"] = $sub;
						}
					}
					catch(Exception $e){
						var_dump($e->getMessage());
						die();
					}
					
				}

				$menus["enlaces"] = $enlaces;
				$menus = $this->utilerias->utf8Encode($menus);
				return array("error"=>"0","menu"=>$menus);
			}
			else{
				return array("error"=>"1");
			}
		}
		else{
			return array("error"=>"1");
		}
		
	}

	function getProyectos()
	{
		$r = $this->db->select("pr.*")->
			from("proyectos_personas as pp")->
			join("proyectos as pr","pp.id_proyecto=pr.id_proyecto")->
			join("personas as p","p.id_persona = pp.id_persona")->
			where("pp.id_persona",$this->session->id_persona)->
			get();

		if($r){
			$d = $r->result_array();
			return $d;
		}else{
			return array();
		}
	}

}