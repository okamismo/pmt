<template>
  <q-page padding>
    
	<q-table
	    :data="tableData"
	    :columns="columns"
	    row-key="id_documento"
	    selection="single"
	    :selected.sync="selected"
	    :filter="filter"
	    color="secondary">

	    <template slot="top-left" slot-scope="props">
			<q-search
				color="secondary"
				v-model="filter"
				placeholder="Buscar"
				clearable
				/>
		</template>

	    <template slot="top-right" slot-scope="props">
	    	<barra-abc @nuevo-clicked="nuevo" @editar-clicked="editar" @borrar-clicked="borrar"/>
	    </template>

	</q-table>

	<q-modal ref="modal" v-model="opened" :content-css="{minWidth: '60vw', minHeight: '60vh'}">
	  <q-modal-layout>
	    
	    <q-toolbar slot="header">
	      <q-btn
	        flat
	        round
	        dense
	        v-close-overlay
	        icon="keyboard_arrow_left"
	      />
	      <q-toolbar-title>
	        {{title}}
	      </q-toolbar-title>
	    </q-toolbar>

	    <q-toolbar slot="footer" class="justify-end">
	      <q-btn class="q-mr-sm" glossy color="secondary" rounded label="Cerrar" @click="opened=false"/>
	      <q-btn  glossy color="secondary" rounded label="Guardar" @click="guardarModalEvent"/>
	    </q-toolbar>

	    <div class="layout-padding">
	    	<!--contenido-->
	        <q-field
			  icon="fas fa-file-alt"
			  label="Documento"
			  helper="Escriba el nombre/descripcion del documento"
			  :error="docError"
			  error-label="El campo es obligatorio">
				<q-input v-model="nombre" />
			</q-field>
	      
	    </div>
	  </q-modal-layout>
	</q-modal>

  </q-page>
</template>

<script>
import {Notify, Dialog, QTable, QField, QInput, QSearch, QModal, QModalLayout } from 'quasar'
import request from '../plugins/request';
import barraAbc from '../components/barra-abc'

export default {
  // name: 'PageName',
  data () {
    return {
    	title:'Titulo de prueba',
    	opened:false,
    	footer:'Pie de prueba',
    	columns: 
    	[
			{
				name: 'id_documento',
				required: true,
				label: 'ID',
				align: 'left',
				field: 'id_documento',
				sortable: true
			},
			{
				name: 'nombre',
				required: true,
				label: 'DOCUMENTO',
				align: 'left',
				field: 'nombre',
				sortable: true
			}
		],
		tableData: [],
	    selected:[],
	    filter: '',
	    nombre: '',
	    docError: false
    }
  },
  components:{
  	Notify, Dialog, QTable, barraAbc, QField, QInput, QSearch, QModal, QModalLayout 
  },

  methods: {
  	limpiarCtrls() {
  		this.nombre = "";
  		this.docError = false;
  	},
  	nuevo() {
  		this.limpiarCtrls();
  		this.title = "Nuevo Registro";
  		this.opened = true;
  	},
  	
  	
  	listar() {
  		const _self = this;
  		const payload = {
	        ruta: 'CategoriasDocumentosController/listar',
	        data: ''
	    }
	      
	      request.postRequest(payload,this.$store).then( (res) => {
	      	this.tableData = res.data;
	      }).catch(error =>_self.$store.dispatch("main/muestraError",error));
  	},

  	insertar() {
  		if(this.nombre == ""){
  			this.docError = true;
  			return;
  		}else{
  			const _self = this;
  			this.docError = false;
  			const payload = {
		        ruta: 'CategoriasDocumentosController/insertar',
		        data: {nombre:this.nombre}
		    }

		    request.postRequest(payload,this.$store).then((res)=>{
		    	_self.$store.dispatch("main/muestraMsg",res);
		    	_self.opened=false;
		    	_self.listar();
		    }).catch(error =>_self.$store.dispatch("main/muestraError",error));
  		}

  	},//insertar

  	editar() {
  		this.limpiarCtrls();
  		this.title = "Editando Registro";

  		if(this.selected.length == 1){
  			const _self = this;
  			const payload = {
		        ruta: 'CategoriasDocumentosController/buscaDocumentoById',
		        data: {id_documento:this.selected[0].id_documento}
		    }

		    request.postRequest(payload,this.$store).then( (res) => {
		      	
		      	if(res.data.error == "1") {
		      		_self.$store.dispatch("main/muestraMsg",res);
			    	_self.opened=false;
			    	_self.listar();
		      	}else{
		      		this.nombre = (res.data.nombre).trim();
		      		this.opened = true;
		      	}
		    	

		    }).catch(error =>_self.$store.dispatch("main/muestraError",error));

  		}else{
  			Notify.create({
	          message: "Seleccione un registro de la tabla",
	          timeout: 2000,
	          type: 'warning',
	          position: 'top-right',
	          icon:'fas fa-exclamation-triangle'
	        })
  		}
  	},

  	update() {
  		const _self = this;
  		const payload = {
		    ruta: 'CategoriasDocumentosController/updateDocumentoById',
		    data: { id_documento:this.selected[0].id_documento, nombre:this.nombre }
		}

		request.postRequest(payload,this.$store).then( function (res) {
			_self.$store.dispatch("main/muestraMsg",res);
	    	_self.opened=false;
	    	_self.listar();
		}).catch(error =>_self.$store.dispatch("main/muestraError",error));
  	},

  	borrar() {
  		if(this.selected.length == 1) {
  			const _self = this;
  			const payload = {
			    ruta: 'CategoriasDocumentosController/borrarDocumentoById',
			    data: { id_documento:this.selected[0].id_documento }
			}

			Dialog.create({
				title: 'Confirme su Acción',
				message: '¿Esta seguro de borrar el registro?',
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
  		else{
  			Notify.create({
	          message: "Seleccione un registro",
	          timeout: 2000,
	          type: 'warning',
	          position: 'top-right',
	          icon:'fas fa-exclamation-triangle'
	        })
  		}
  	},

  	guardarModalEvent() {
  		if( (this.title).startsWith("Nuevo")){
  			this.insertar();
  		}else{
  			this.update();
  		}
  	}
  },

  mounted: function () {
  	this.listar();
  }
};
</script>

<style>
</style>
