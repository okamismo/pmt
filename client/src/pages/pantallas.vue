<template>
  <q-page padding>
    
	<q-table
	    :data="tableData"
	    :columns="columns"
	    row-key="id_pantalla"
	    selection="single"
	    :selected.sync="selected"
	    :filter="filter"
	    color="secondary"
	    :visible-columns="visibleColumns">

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
	    	<q-table-columns color="secondary" class="q-mr-sm q-ml-lg"
	        	v-model="visibleColumns" :columns="columns"/>
	    </template>

	</q-table>

	<q-modal ref="modal" v-model="opened" :content-css="{minWidth: '80vw', minHeight: '80vh'}">
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
			  label="Nombre del menú"
			  helper="¿Cómo desea que se vea el nombre del menu?"
			  :error="nombre_menuError"
			  error-label="El campo es obligatorio">
				<q-input v-model="nombre_menu" />
			</q-field>
			<q-field
			  label="Descripción"
			  helper="Descripción corta del item"
			  :error="descripcionError"
			  error-label="El campo es obligatorio">
				<q-input v-model="descripcion" />
			</q-field>
			<q-field
			  label="Controlador"
			  helper="Nombre del controlador que atenderá las peticiones (ServerSide)">
			  <q-input v-model="controlador" />
			</q-field>
			<q-field
			  label="Ícono"
			  helper="Nombre del ícono que aparecerá con el item">
			  <q-input v-model="icono" />
			</q-field>
			<q-field
			  label="URI"
			  helper="Ruta que mostrará el navegador al ir al item">
			  <q-input v-model="uri" />
			</q-field>
			<q-field label="¿Tendrá Hijos?">
			  <q-checkbox v-model="tiene_hijos" />
			</q-field>
			<q-field label="Padre">
			  <q-select v-model="id_padre" :options="padres" clearable/>
			</q-field>
	      
	    </div>
	  </q-modal-layout>
	</q-modal>


  </q-page>
</template>

<script>
import {Notify, Dialog, QTable, QTableColumns, QField, QInput, QSearch, QCheckbox, QSelect, QModal, QModalLayout} from 'quasar'
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
				name: 'id_pantalla',
				required: true,
				label: 'ID',
				align: 'left',
				field: 'id_pantalla',
				sortable: true
			},
			{
				name: 'nombre_menu',
				required: true,
				label: 'NOMBRE',
				align: 'left',
				field: 'nombre_menu',
				sortable: true
			},
			{
				name: 'descripcion',
				required: false,
				label: 'DESCRIPCION',
				align: 'left',
				field: 'descripcion',
				sortable: true
			},
			{
				name: 'controlador',
				required: true,
				label: 'CONTROLADOR',
				align: 'left',
				field: 'controlador',
				sortable: true
			},
			{
				name: 'icono',
				required: false,
				label: 'ICONO',
				align: 'left',
				field: 'icono',
				sortable: true
			},
			{
				name: 'tiene_hijos',
				required: false,
				label: 'HIJOS',
				align: 'left',
				field: 'tiene_hijos',
				sortable: true
			},
			{
				name: 'id_padre',
				required: false,
				label: 'PADRE',
				align: 'left',
				field: 'id_padre',
				sortable: true
			}
		],
		visibleColumns: ["id_pantalla","nombre","controlador"],
		tableData: [],
	    selected:[],
	    filter: '',
	    nombre_menu: '',
	    nombre_menuError: false,
	    descripcion: '',
	    descripcionError: false,
	    controlador: '',
	    icono: '',
	    uri:'',
	    tiene_hijos: false,
	    id_padre:'',
	    padres: []
    }
  },
  components:{
  	Notify, Dialog, QTable, QTableColumns, barraAbc, QField, QInput, QSearch, QCheckbox,QSelect, QModal, QModalLayout
  },

  methods: {
  	limpiarCtrls() {
  		this.nombre_menu = "";
  		this.nombre_menuError = false;
  		this.descripcion = "";
  		this.descripcionError = false;
  		this.controlador = "";
  		this.icono = "";
  		this.uri = "";
  		this.tiene_hijos = false;
  		this.id_padre = "";
  	},
  	nuevo() {
  		this.limpiarCtrls();
  		this.title = "Nuevo Registro";
  		this.opened = true;
  	},

  	listar() {
  		const _self = this;
  		const payload = {
	        ruta: 'PantallasController/listar',
	        data: ''
	    }
	      
	      request.postRequest(payload,this.$store).then( (res) => {
	      	this.tableData = res.data;
	      }).catch(error =>_self.$store.dispatch("main/muestraError",error));
  	},

  	listarMenusPadres() {
  		const _self = this;
  		const payload = {
	        ruta: 'PantallasController/listaMenusPadres',
	        data: ''
	    }
	      
	      request.postRequest(payload,this.$store).then( (res) => {
	      	this.padres = res.data;
	      }).catch(error =>_self.$store.dispatch("main/muestraError",error));
  	},

  	esValido() {
  		if(this.nombre_menu == ""){
  			this.nombre_menuError = true;
  			return false;
  		}
  		if(this.descripcion == ""){
  			this.descripcionError = true;
  			return false;
  		}

  		return true;
  	},

  	resetErrors() {
  		this.nombre_menuError = false;
  		this.descripcionError = false;
  	},

  	insertar() {
  		if(this.esValido())
  		{
  			this.resetErrors();
  			const _self = this;
			const payload = {
	        ruta: 'PantallasController/insertar',
	        data: 
	        	{
	        		nombre_menu:this.nombre_menu, descripcion:this.descripcion, controlador:this.controlador, icono:this.icono, uri:this.uri, tiene_hijos:(this.tiene_hijos)?"1":"0", id_padre:this.id_padre
	        	}
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
  			
			this.resetErrors();
			const _self = this;
  			const payload = {
		        ruta: 'PantallasController/buscaById',
		        data: {id_pantalla:this.selected[0].id_pantalla}
		    }

		    request.postRequest(payload,this.$store).then( (res) => {
		      	
		      	if(res.data.error == "1") {
		      		_self.$store.dispatch("main/muestraMsg",res);
		      	}else{
		      		this.nombre_menu = (res.data.nombre_menu).trim();
		      		this.descripcion = (res.data.descripcion !== null)?(res.data.descripcion).trim():"";
		      		this.controlador = (res.data.controlador !== null)?(res.data.controlador).trim():"";
		      		this.icono = (res.data.icono !== null)?(res.data.icono).trim():"";
		      		this.uri = (res.data.uri !== null)?(res.data.uri).trim():"";
		      		this.tiene_hijos = (res.data.tiene_hijos == "1")?true:false;
		      		this.id_padre = (res.data.id_padre !== null)?(res.data.id_padre).trim():"";
		      		this.opened = true;
		      	}
		    	

		    }).catch(error =>_self.$store.dispatch("main/muestraError",error));
  			

  		}else{
  			Notify.create({
	          message: "Seleccione un Registro de la tabla",
	          timeout: 2000,
	          type: 'warning',
	          position: 'top-right',
	          icon:'fas fa-exclamation-triangle'
	        })
  		}
  	},

  	update() {
  		if(this.esValido())
  		{

	  		const _self = this;
	  		const payload = {
			    ruta: 'PantallasController/updateById',
			    data: { 
			    	id_pantalla:this.selected[0].id_pantalla, nombre_menu:this.nombre_menu, descripcion:this.descripcion, controlador:this.controlador, icono:this.icono, uri:this.uri, tiene_hijos:(this.tiene_hijos)?"1":"0", id_padre:this.id_padre 
			    }
			}

			request.postRequest(payload,this.$store).then( function (res) {
				_self.$store.dispatch("main/muestraMsg",res);
		    	_self.opened=false;
		    	_self.listar();
			}).catch(error =>_self.$store.dispatch("main/muestraError",error));
		}
  	},

  	borrar() {
  		if(this.selected.length == 1) {
  			const _self = this;
  			const payload = {
			    ruta: 'PantallasController/borrarById',
			    data: { id_pantalla:this.selected[0].id_pantalla }
			}

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
  		else{
  			Notify.create({
	          message: "Seleccione un Registro de la Tabla",
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
  	},//guardar

/*
  	muestraError(error) {
  		Notify.create({
          message: error.name+" : "+error.message,
          timeout: 5000,
          type: 'negative',
          position: 'top-right',
          icon:'fas fa-exclamation-triangle'
        });
    	this.opened = false;
  	}
  	*/
  },

  mounted: function () {
  	this.listar();
  	this.listarMenusPadres();
  }
};
</script>

<style>
</style>
