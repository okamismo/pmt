<template>
  <q-page padding>
  	<q-table
	    :data="tableData"
	    :columns="columns"
	    row-key="id_proyecto"
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
	        <q-field label="Nombre" :error="nombreError" error-label="El Nombre del Proyecto es obligatorio">
				<q-input v-model="nombre" clearable/>
			</q-field>
			<q-field  label="Descripción">
				<q-input type="textarea" v-model="descripcion" clearable/>
			</q-field>
			<q-field label="Siglas">
			  <q-input v-model="siglas" helper="Iniciales o Acronimo del nombre del proyecto" clearable/>
			</q-field>
			<q-field  label="Fecha de inicio" helper="Fecha en la que inicia el Proyecto" :error="finicioError" error-label="La fecha de inicio es obligatoria">
			  <q-datetime type="date" v-model="finicio" clearable/>
			</q-field>
			<q-field label="Fecha de termino esperada" helper="Fecha en la que se estima terminará el proyecto" :error="ffinalError" error-label="La Fecha de termino es obligatoria">
			  <q-datetime type="date" v-model="ffinal" clearable/>
			</q-field>
	      
	    </div>
	  </q-modal-layout>
	</q-modal>
  </q-page>
</template>

<script>
import {Notify, Dialog, QTable, QTableColumns, QField, QInput, QSearch, QCheckbox, QSelect, QModal, QModalLayout, QDatetime} from 'quasar'
import request from '../plugins/request';
import barraAbc from '../components/barra-abc'

export default {
  data () {
  	return {
  		title:'Titulo de prueba',
    	opened:false,
    	footer:'Pie de prueba',
    	columns: 
    	[
			{
				name: 'id_proyecto',
				required: false,
				label: 'ID',
				align: 'left',
				field: 'id_proyecto',
				sortable: true
			},
			{
				name: 'nombre',
				required: true,
				label: 'NOMBRE',
				align: 'left',
				field: 'nombre',
				sortable: true
			},
			{
				name: 'descripcion',
				required: false,
				label: 'DESCRIPCIÓN',
				align: 'left',
				field: 'descripcion',
				sortable: true
			},
			{
				name: 'siglas',
				required: true,
				label: 'SIGLAS',
				align: 'left',
				field: 'siglas',
				sortable: true
			},
			{
				name: 'finicio',
				required: true,
				label: 'FECHA INICIO',
				align: 'left',
				field: 'finicio',
				sortable: true
			},
			{
				name: 'ffinal',
				required: true,
				label: 'FECHA TERMINO',
				align: 'left',
				field: 'ffinal',
				sortable: true
			}
		],
		visibleColumns: ["nombre","siglas","finicio","ffinal"],
		tableData: [],
	    selected:[],
	    filter: '',
	    id_proyecto: 0,
	    nombre: '',
	    descripcion: '',
	    siglas: '',
	    finicio: '',
	    ffinal: '',
	    nombreError: false,
	    finicioError: false,
	    ffinalError:false
  	}
  },
  components:{
  	Notify, Dialog, QTable, barraAbc, QField, QInput, QSearch, QCheckbox,QSelect, QModal, QModalLayout,QDatetime, QTableColumns
  },
  methods: {
  	limpiarCtrls() {
  		this.nombre = "";
  		this.descripcion = "";
  		this.siglas = "";
  		this.finicio = "";
  		this.ffinal = "";
  	},
  	esValido(tipo) {
  		
  		if(this.nombre == ""){
  			this.nombreError = true;
  			return false;
  		}
  		if(this.finicio == ""){
  			this.finicioError = true;
  			return false;
  		}
  		if(this.ffinal == ""){
  			this.ffinalError = true;
  			return false;
  		}
  		
  		return true;
  	},

  	resetErrors() {
  		this.nombreError = false;
  		this.finicioError = false;
  		this.ffinalError = false;
  	},
  	listar() {
  		const _self = this;
	  	const payload = {
	  		ruta: 'ProyectosController/listar'
	  	}

	  	request.postRequest(payload,this.$store).then(res=>{
	  		_self.tableData = res.data;
	  	}).catch(error=>{_self.$store.dispatch("main/muestraError",error)});
  	},
  	nuevo() {
  		this.limpiarCtrls();
  		this.resetErrors();
  		this.title = "Nuevo Registro";
  		this.opened = true;
  	},
  	editar() {
  		this.limpiarCtrls();
  		this.title = "Editando Perfil";

  		if(this.selected.length == 1)
  		{
	  		const _self = this;
	  		const payload = {
	  			ruta: 'ProyectosController/getProyectoById',
	  			data: {
	  				id_proyecto: this.selected[0].id_proyecto
	  			}
	  		};

	  		request.postRequest(payload,this.$store).then(res=>{
	  			this.nombre = res.data.nombre;
	  			this.descripcion = res.data.descripcion;
	  			this.siglas = res.data.siglas;
	  			this.finicio = res.data.finicio;
	  			this.ffinal = res.data.ffinal;
	  			this.opened = true; 
	  		}).catch(error=>_self.$store.dispatch("main/muestraError",$error));
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
  	borrar() {
  		if(this.selected.length == 1) {
  			const _self = this;
  			const payload = {
			    ruta: 'ProyectosController/borrarById',
			    data: { id_proyecto:this.selected[0].id_proyecto }
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
  	insertar() {
  		this.resetErrors();
  		if(this.esValido("insertar"))
  		{
  			const _self = this;
  			const payload = {
  				ruta: 'ProyectosController/insertar',
  				data: {
  					nombre:this.nombre, descripcion:this.descripcion, siglas:this.siglas, finicio:this.finicio, ffinal:this.ffinal
  				}
  			};

  			request.postRequest(payload,this.$store).then(res=>{
  				_self.$store.dispatch("main/muestraMsg",res);
  				this.listar();
  				this.opened=false
  			}).catch(error=>_self.$store.dispatch("muestraError",error));
  		}
  	},
  	update() {
  		this.resetErrors();
  		if(this.esValido("update"))
  		{
  			const _self = this;
  			const payload = {
  				ruta: 'ProyectosController/update',
  				data: {
  					id_proyecto:this.selected[0].id_proyecto, nombre:this.nombre, descripcion:this.descripcion, siglas:this.siglas, finicio:this.finicio, ffinal:this.ffinal
  				}
  			};

  			request.postRequest(payload,this.$store).then(res=>{
  				_self.$store.dispatch("main/muestraMsg",res);
  				this.listar();
  				this.opened=false
  			}).catch(error=>_self.$store.dispatch("muestraError",error));
  		}
  	},
  	guardarModalEvent() {
  		if( (this.title).startsWith("Nuevo")){
  			this.insertar();
  		}else{
  			this.update();
  		}
  	}

  },//methods
  mounted() {
  	this.listar();
  	//console.log(this.$t('hola'))
  }
};
</script>

<style>
</style>
