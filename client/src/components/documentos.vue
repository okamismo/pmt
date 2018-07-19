<template>
  <div>

		<q-table :data="documentos" :columns="columnas" row-key="id"  :filter="filter"  color="secondary" class="" :visible-columns="visibleColumns" selection="single" :selected.sync="selected" dense>
			
			<template slot="top-left" slot-scope="props">
				<q-search v-model="filter" placeholder="Buscar Documento"/>
		    </template>
			<template slot="top-right" slot-scope="props">
				<q-btn-group rounded>
					<q-btn icon="far fa-trash-alt" color="secondary" size="md" @click="confirmarBorrado">
						<q-tooltip anchor="center left" :offset="[10, 10]">borrar</q-tooltip>
					</q-btn>
					<q-btn icon="fas fa-cloud-download-alt" color="secondary" size="md" @click="descargarArchivo">
						<q-tooltip anchor="center left" :offset="[10, 10]">Descargar</q-tooltip>
					</q-btn>
				</q-btn-group>

		        <q-table-columns color="secondary" class="q-mr-sm q-ml-lg"
        		v-model="visibleColumns" :columns="columnas"/>
		    </template>
				
		</q-table>

		<div class="row">
			<div class="col-sm-12">
				<q-field label="Descripción del archivo a subir" :error="errorDescripcion" error-label="Debe escribir una descripcion para el archivo">
					<q-input type="text" v-model="descripcion" />
				</q-field>
			</div>
		</div>
		
		<div class="row">
			<div class="col-sm-12">
				<q-field label="Seleccione un archivo" :error="errorUploader" :error-label="errorLabel">
					<q-uploader ref="uploader" name='archivo' :url="url" auto-expand :additional-fields="additionalFields" :extensions="extensiones" clearable :hide-upload-button = "true" @uploaded="fileUploaded" @fail="uploadFailed"/>
				</q-field>
			</div>
			
		</div>
		<div class="row">
			<div class="col-sm-12">
				<q-field label="Categoria del Documento" error-label="Seleccione la categoria del documento" :error="errorCategoria">
				  <q-select v-model="id_categoria" :options="categorias" clearable/>
				</q-field>
			</div>
		</div>

		<div class="row">
			<div class="col-sm-12 text-right">
				<q-btn icon="fas fa-upload" color="secondary" size="sm" @click="validarSubida"/>
			</div>
		</div>
		

  </div>
</template>

<script>
import {QTable, QTableColumns, QTd, QSearch, QBtnGroup, QUploader, QInput, QField, QSelect, Notify, Dialog} from 'quasar';
import request from '../plugins/request';

export default {
  // name: 'ComponentName',
  data () {
    return {
    	columnas: 
    	[
    		{
		        name: 'id',
		        required: false,
		        label: 'ID',
		        align: 'left',
		        field: 'id',
		        sortable: true
		    },
		    {
		        name: 'id_docto',
		        required: false,
		        label: 'ID CATEGORIA',
		        align: 'left',
		        field: 'id_docto',
		        sortable: true
		    },
		    {
		        name: 'categoria',
		        required: false,
		        label: 'CATEGORIA',
		        align: 'left',
		        field: 'categoria',
		        sortable: true
		    },
		    {
		        name: 'id_paquete',
		        required: false,
		        label: 'ID PAQUETE',
		        align: 'left',
		        field: 'id_paquete',
		        sortable: true
		    },
		    {
		        name: 'id_persona',
		        required: false,
		        label: 'ID PERSONA',
		        align: 'left',
		        field: 'id_persona',
		        sortable: true
		    },
		    {
		        name: 'descripcion',
		        required: true,
		        label: 'DESCRIPCIÓN',
		        align: 'left',
		        field: 'descripcion',
		        sortable: true
		    },
		    {
		        name: 'ext',
		        required: true,
		        label: 'EXTENSIÓN',
		        align: 'left',
		        field: 'ext',
		        sortable: true
		    },
		    {
		        name: 'persona',
		        required: true,
		        label: 'PERSONA',
		        align: 'left',
		        field: 'persona',
		        sortable: true
		    },
		    {
		        name: 'ruta',
		        required: false,
		        label: 'NOMBRE',
		        align: 'left',
		        field: 'ruta',
		        sortable: true
		    },
		    {
		        name: 'finsertado',
		        required: false,
		        label: 'FECHA GUARDADO',
		        align: 'left',
		        field: 'finsertado',
		        sortable: true
		    }

		],
		visibleColumns:['descripcion','ext','persona','finsertado'],
    	documentos: [],
    	filter:'',
    	selected: [],
    	url: 'index.php/DocumentosController/asociarDoctoPaquete',
    	extensiones: '.pdf,.jpg,.jpeg,.png,.gif,.txt,.doc,.docx,.xls,.xlsx,.ppt,.pptx',
    	descripcion: '',
    	errorDescripcion: false,
    	errorUploader: false,
    	errorCategoria: false,
    	errorLabel: '',
    	maxSize:2,
    	id_categoria:'',
    	categorias:[]
    }
  },
  components: {
  	QTable, QTableColumns, QTd, QSearch, QBtnGroup, QUploader, QInput, QField, QSelect, Notify, Dialog
  },
  props:['paquete',"extensiones-permitidas","tamanio-maximo"],
  watch: {
  	paquete: function() {
  		this.listar();
  		this.descripcion = '';
  	}
  },
  computed: {
  	additionalFields () {
  		return [
  			{ 'name':'descripcion','value':this.descripcion	},
  			{ 'name':'id_categoria','value':this.id_categoria },
  			{ 'name':'id_paquete','value':this.paquete }
  		]
  	}
  },
  methods: {
  	listar() {
  		const _self = this;
  		const payload = {
	        ruta: 'DocumentosController/listar',
	        data: {id_paquete: this.paquete}
	    }
	      
      	request.postRequest(payload,this.$store).then( (res) => {
	      	if(res.data){
	      		this.documentos = res.data;
	      	}else{
	      		this.documentos = [];
	      	}
	    }).catch(error =>_self.$store.dispatch("main/muestraError",error));
  	},
  	listarCategorias() {
  		
  		const _self = this;
  		const payload = {
	        ruta: 'CategoriasDocumentosController/listarCategoriasDocumentosForSelect'
	    }
	      
      	request.postRequest(payload,this.$store).then( (res) => {
	      	if(res.data){
	      		this.categorias = res.data;
	      	}else{
	      		this.categorias = [];
	      	}
	    }).catch(error =>_self.$store.dispatch("main/muestraError",error));
  	},
  	getFileExtension(filename) {
	  return filename.slice((filename.lastIndexOf(".") - 1 >>> 0) + 2);
	},
	isFileSizeCorrect(size) {
		const mb = size / 1024 / 1024; //convertimos a mb y despues comparamos si se supera el maximo
		return (mb < this.maxSize)
	},
  	validarSubida() {
  		this.errorDescripcion = false;
  		this.errorUploader = false;
  		this.errorCategoria = false;

  		if(this.descripcion.trim() == ""){
  			this.errorDescripcion = true;
  			return false;
  		}
  		if(this.$refs.uploader.files.length == 0){
  			this.errorUploader = true;
  			this.errorLabel = "Debe seleccionar un archivo";
  			return false;
  		}else{
  			const allowed = this.extensiones.split(",");
  			const ext = "."+this.getFileExtension(this.$refs.uploader.files[0].name);

  			if(ext == '.' || !allowed.includes(ext)){
  				this.errorUploader = true;
  				this.errorLabel = "Las extensiones permitidas son: "+this.extensiones;
  				return false;
  			}
  			if(!this.isFileSizeCorrect(this.$refs.uploader.files[0].size)) {
  				this.errorUploader = true;
  				this.errorLabel = "El archivo debe medir menos de "+this.maxSize+" MB";
  				return false;
  			}
  		}
  		if(this.id_categoria == undefined || this.id_categoria == "" ) {
  			this.errorCategoria = true;
  			return false;
  		}

  		this.$refs.uploader.upload();
  	},
  	clearCtrls() {
  		this.descripcion = '';
  		this.id_categoria = '';
  	},
  	fileUploaded(file, xhr) {
  		this.clearCtrls();
  		
  		
  		let response = JSON.parse(xhr.response)
  		if(response.error == "0"){
  			this.listar();
  			Notify.create({
	          message: "Archivo guardado",
	          timeout: 2000,
	          type: 'info',
	          position: 'top-right'
	        })
  		}
  		this.$refs.uploader.reset();
  	},
  	uploadFailed(file, xhr){
  		this.$refs.uploader.reset();
  		Notify.create({
          message: "Error al intentar guardar",
          timeout: 2000,
          type: 'negative',
          position: 'top-right'
        })
  	},
  	confirmarBorrado() {
  		if(this.selected.length == 0){
  			Notify.create({
	          message: "Seleccione un registro de la tabla",
	          timeout: 2000,
	          type: 'warning',
	          position: 'top-right',
	          icon:'fas fa-exclamation-triangle'
	        })
  		}else{
  			const _self = this;
  			const payload = {
  				ruta: 'DocumentosController/borrarDoctoPaquete',
			    data: { id:this.selected[0].id }
  			};
  			

  			Dialog.create({
				title: 'Confirme su Acción',
				message: '¿Esta seguro de borrar el Registro?',
				color: 'primary',
				ok:'Si, proceder',
				cancel: 'Cancelar'
			}).then( ()=>{
				request.postRequest(payload,this.$store).then( function (res) {
					_self.$store.dispatch("main/muestraMsg",res);
		    		_self.listar();
				}).catch(error => _self.$store.dispatch("main/muestraError",error));
			}).catch( ()=>{
				
			});
  		}
  	},
  	descargarArchivo() {
  		if(this.selected.length == 0){
  			Notify.create({
	          message: "Seleccione un registro de la tabla",
	          timeout: 2000,
	          type: 'warning',
	          position: 'top-right',
	          icon:'fas fa-exclamation-triangle'
	        })
  		}else{
  			let url = this.$store.getters['main/getBaseUrl'] + "index.php/DocumentosController/descargarArchivo?id="+this.selected[0].id+"&archivo="+this.selected[0].ruta;
  			window.open(url);
  		}
  	}

  },
  mounted() {
  	this.listar();
  	this.listarCategorias();
  	if(this.extensionesPermitidas && this.extensionesPermitidas.trim() != ""){
  		this.extensiones = this.extensionesPermitidas;
  	}
  	if(this.tamanioMaximo && this.tamanioMaximo.trim() != ""){
  		this.maxSize = parseFloat(this.tamanioMaximo.trim());
  	}
  	
  }
};
</script>

<style scoped>
	.row{
		margin-top:5px;
		margin-bottom: 7px;
	}
</style>
