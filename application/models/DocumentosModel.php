<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DocumentosModel extends CI_Model{

	const ARCHIVOS_EDT = 'client/src/statics/archivosEdt/';
	function __construct()
	{
		parent::__construct();
		$this->load->library("Utilerias");
	}

	function listar($id_paquete)
	{
		$r = $this->db->select("d.id,d.id_docto,d.id_paquete,p.id_persona,d.ruta,d.ext,d.finsertado,d.descripcion,concat(p.paterno,' ',p.nombre) as persona,cd.nombre as categoria")->
			from("paquetes_doctos as d")->
			join("edt as e","e.id_paquete=d.id_paquete")->
			join("personas as p","d.id_persona=p.id_persona")->
			join("categorias_documentos as cd","cd.id_documento=d.id_docto")->
			where("d.id_paquete",$id_paquete)->
			get();
		if($r)
		{
			$d = $r->result_array();
			$data = $d;//$this->utilerias->utf8Encode($d);

			return $data;
		}
		else{
			return array("error"=>"1");
		}
	}

	public function asociarDoctoPaquete($id_paquete,$id_categoria,$descripcion)
	{
		$this->load->library("Uploader");
		$this->uploader->setCarpeta(FCPATH.self::ARCHIVOS_EDT);
		$this->uploader->setAllowedTypes("pdf");

		foreach($_FILES as $key => $val)
		{
			$r = $this->uploader->uploadFile($key);

			if($r["error"] != "1")
			{
				$ext = explode(".",$r["docto"]);
				$datos = array("id_docto"=>$id_categoria,"id_paquete"=>$id_paquete,"id_persona"=>$this->session->id_persona,"ruta"=>$r["docto"],"ext"=>$ext[1],"finsertado"=>date("Y-m-d H:i:s"),"descripcion"=>$descripcion);
				if($this->db->insert("paquetes_doctos",$datos)){
					return array("error"=>"0","msg"=>"Registro guardado");
				}else{
					return array("error"=>"1","msg"=>"No fue posible guardar el registro");
				}
				
			}
			else{
				return array("error"=>"1","msg"=>"No fue posible guardar el archivo");
			}
		}
		
	}

	private function buscarRutaPorId($id)
	{
		$r = $this->db->select("ruta")->
			from("paquetes_doctos")->
			where("id",$id)->
			get();

		if($r)
		{
			if($r->num_rows()>0){
				return $r->row()->ruta;
			}else{
				return false;
			}
		}
	}

	function borrarDoctoPaquete($id)
	{
		$ruta = $this->buscarRutaPorId($id);
		$this->db->where("id",$id);
		if($this->db->delete("paquetes_doctos")){
			if($ruta)
			{
				try{

					$file = FCPATH.self::ARCHIVOS_EDT.$ruta;
					if(file_exists($file)){
						unlink($file);
					}
					else{
						return array("error"=>"0","rowDeleted"=>"1","fileDeleted"=>"0","fileError"=>"archivo no encontrado","msg"=>"Registro Borrado");
					}
					
				}catch(Exception $e){
					return array("error"=>"0","rowDeleted"=>"1","fileDeleted"=>"0","fileError"=>$e->getMessage(),"msg"=>"Registro Borrado");
				}
			}
			return array("error"=>"0","rowDeleted"=>"1","fileDeleted"=>"1","fileError"=>"none", "msg"=>"Se ha borrado el archivo");
		}else{
			return array("error"=>"1","rowDeleted"=>"0","fileDeleted"=>"0","fileError"=>"none", "msg"=>"No fue posible borrar el archivo");
		}
	}

	private function existeRegistroArchivo($id,$ruta)
	{
		$r = $this->db->where("id",$id)->
			where("ruta",$ruta)->get("paquetes_doctos");

		if($r){
			if($r->num_rows()>0) {
				return TRUE;
			}else{
				return FALSE;
			}

		}
		return FALSE;
	}

	function descargarArchivo($id,$nombre)
	{
		if($this->existeRegistroArchivo($id,$nombre))
		{
			$file = FCPATH.self::ARCHIVOS_EDT.$nombre;
			return $file;
		}else{
			return FALSE;
		}
		
	}

}