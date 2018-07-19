<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ComentariosModel extends CI_Model{

	function __construct()
	{
		parent::__construct();
		$this->load->library("Utilerias");
	}

	function listar($id_paquete)
	{
		$r = $this->db->select("id_nota,nota,pn.factualizado,p.nombre,p.paterno,p.id_persona,p.avatar")->
			from("paquetes_notas as pn")->
			join("personas as p","pn.id_persona = p.id_persona")->
			where("id_paquete",$id_paquete)->
			order_by("pn.factualizado","DESC")->
			get();

		if($r)
		{
			if($r->num_rows()>0){
				$d = $r->result_array();
				$data = $d;//$this->utilerias->utf8Encode($d);
				return array("error"=>"0","comentarios"=>$data,"msg"=>"comentarios retraidos existosamente");
			}else{
				return array("error"=>"0","comentarios"=>array(),"msg"=>"No se encontraron comentarios");
			}
		}else{
			return array("error"=>"1","msg"=>"No fue posible obtener los comentarios...");
		}

	}

	public function buscarNotaById($id_nota)
	{
		$r = $this->db->select("id_nota,nota")->
			from("paquetes_notas")->
			where("id_nota",$id_nota)->
			get();

		if($r)
		{
			if($r->num_rows()>0){
				$d = $r->row()->nota;
				$data = $d;//$this->utilerias->utf8Encode($d);
				return array("error"=>"0","comentario"=>$data,"msg"=>"comentarios retraidos existosamente");
			}else{
				return array("error"=>"0","comentario"=>array(),"msg"=>"No se encontraron comentarios");
			}
		}else{
			return array("error"=>"1","msg"=>"No fue posible obtener los comentarios...");
		}
	}

	public function insertar($id_paquete,$nota)
	{
		$data["id_paquete"] = $id_paquete;
		$data ["nota"] = $nota;
		$data ["finsertado"] = date("Y-m-d H:i:s");
		$data ["factualizado"] = $data ["finsertado"];
		$data ["id_persona"] = $this->session->id_persona;

		if($this->db->insert("paquetes_notas",$data)){
			return array("error"=>"0","msg"=>"Comentario agregado");
		}else{
			return array("error"=>"1","msg"=>"No fue posible agregar el comentario");
		}
	}

	private function esAuthorDeNota($id_nota)
	{
		$r = $this->db->from("paquetes_notas")->
			where("id_nota",$id_nota)->
			where("id_persona", $this->session->id_persona)->
			count_all_results();

		return ($r == 1);
	}

	public function actualizar($id_nota,$nota)
	{
		$data ["nota"] = $nota;
		$data ["factualizado"] = date("Y-m-d H:i:s");

		if($this->esAuthorDeNota($id_nota)){
			if($this->db->update("paquetes_notas",$data,"id_nota=".$id_nota)){
				return array("error"=>"0","msg"=>"Comentario actualizado");
			}else{
				return array("error"=>"1","msg"=>"No fue posible actualizar el comentario");
			}
		}else{
			return array("error"=>"1","msg"=>"No es posible editar ni borrar un comentario escrito por otra persona");
		}
	}

	function borrar($id_nota)
	{
		if($this->esAuthorDeNota($id_nota)){
			if($this->db->delete("paquetes_notas","id_nota=".$id_nota)){
				return array("error"=>"0","msg"=>"Comentario borrado");
			}else{
				return array("error"=>"1","msg"=>"No fue posible borrar el comentario");
			}
		}else{
			return array("error"=>"1","msg"=>"No es posible editar ni borrar un comentario escrito por otra persona");
		}
	}

}