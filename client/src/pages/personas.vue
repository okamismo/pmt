<template>
  <q-page padding>
  	<q-table
	    :data="tableData"
	    :columns="columns"
	    row-key="id_persona"
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
	        <q-field label="Apellido Paterno" :error="paternoError" error-label="El Apellido es obligatorio">
				<q-input v-model="paterno" clearable/>
			</q-field>
			<q-field  label="Apellido Materno">
				<q-input v-model="materno" clearable/>
			</q-field>
			<q-field label="Nombre" :error="nombreError" error-label="El Nombre es obligatorio">
			  <q-input v-model="nombre" clearable/>
			</q-field>
			<q-field  label="E-mail" helper="Correo electronico: ejemplo@gmail.com" :error="emailError" error-label="El Email es obligatorio">
			  <q-input type="email" v-model="email" clearable/>
			</q-field>
			<q-field label="Contraseña" helper="Escriba una contraseña para la persona" :error="passError" error-label="La contraseña es obligatorio">
			  <q-input type="password" v-model="pass" clearable/>
			</q-field>
			<q-field label="Confirmación" helper="Confirme la contraseña por favor" :error="passError2" error-label="Las contraseñas no coinciden">
			  <q-input type="password" v-model="pass2" clearable/>
			</q-field>

			<q-field label="Perfil" helper="Seleccione el Perfil de la persona" :error="perfilError" error-label="El perfil es un campo obligatorio">
			  <q-select v-model="perfil" :options="perfiles" clearable/>
			</q-field>
	      
	    </div>
	  </q-modal-layout>
	</q-modal>
  </q-page>
</template>

<script>
import {Notify, Dialog, QTable, QField, QInput, QSearch, QCheckbox, QSelect, QModal, QModalLayout} from 'quasar'
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
				name: 'id_persona',
				required: true,
				label: 'ID',
				align: 'left',
				field: 'id_persona',
				sortable: true
			},
			{
				name: 'paterno',
				required: true,
				label: 'PATERNO',
				align: 'left',
				field: 'paterno',
				sortable: true
			},
			{
				name: 'materno',
				required: true,
				label: 'MATERNO',
				align: 'left',
				field: 'materno',
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
				name: 'email',
				required: false,
				label: 'EMAIL',
				align: 'left',
				field: 'email',
				sortable: true
			}
		],
		tableData: [],
	    selected:[],
	    filter: '',
	    id_persona: 0,
	    paterno: '',
	    materno: '',
	    nombre: '',
	    email: '',
	    pass: '',
	    pass2:'',
	    perfiles: [],
	    perfil: null,
	    perfilError: false,
	    paternoError: false,
	    nombreError: false,
	    emailError: false,
	    passError:false,
	    passError2:false,
  	}
  },
  components:{
  	Notify, Dialog, QTable, barraAbc, QField, QInput, QSearch, QCheckbox,QSelect, QModal, QModalLayout
  },
  methods: {
  	limpiarCtrls() {
  		this.paterno = "";
  		this.materno = "";
  		this.nombre = "";
  		this.email = "";
  		this.pass = "";
  		this.pass2 = "";
  		this.perfil = null;
  	},
  	esValido(tipo) {
  		if(this.paterno == ""){
  			this.paternoError = true;
  			return false;
  		}
  		if(this.nombre == ""){
  			this.nombreError = true;
  			return false;
  		}
  		if(this.email == ""){
  			this.emailError = true;
  			return false;
  		}
  		if(tipo == "insertar")
  		{
  			if(this.pass == ""){
	  			this.passError = true;
	  			return false;
	  		}
	  		if(this.pass2 == ""){
	  			this.passError2 = true;
	  			return false;
	  		}
  		}
  		
  		if(this.pass != this.pass2){
  			this.passError2 = true;
  			return false;
  		}

  		if(this.perfil === null){
  			this.perfilError = true;
  			return false;
  		}

  		return true;
  	},

  	resetErrors() {
  		this.paternoError = false;
  		this.nombreError = false;
  		this.emailError = false;
  		this.passError = false;
  		this.passError2 = false;
  		this.perfilError = false;
  	},
  	listar() {
  		const _self = this;
	  	const payload = {
	  		ruta: 'PersonasController/listar'
	  	}

	  	request.postRequest(payload,this.$store).then(res=>{
	  		_self.tableData = res.data;
	  	}).catch(error=>{_self.$store.dispatch("muestraError",error)});
  	},
  	nuevo() {
  		this.limpiarCtrls();
  		this.resetErrors();
  		this.title = "Nuevo Registro";
  		this.opened = true;
  	},
  	editar() {
  		this.limpiarCtrls();
  		this.title = "Editando Registro";

  		if(this.selected.length == 1)
  		{
	  		const _self = this;
	  		const payload = {
	  			ruta: 'PersonasController/getPersonaById',
	  			data: {
	  				id_persona: this.selected[0].id_persona
	  			}
	  		};

	  		request.postRequest(payload,this.$store).then(res=>{
	  			this.paterno = res.data.paterno;
	  			this.materno = res.data.materno;
	  			this.nombre = res.data.nombre;
	  			this.email = res.data.email;
	  			this.perfil = res.data.id_perfil;
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
			    ruta: 'PersonasController/borrarById',
			    data: { id_persona:this.selected[0].id_persona }
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
  				ruta: 'PersonasController/insertar',
  				data: {
  					paterno:this.paterno, materno:this.materno, nombre:this.nombre, email:this.email, pass:this.pass, id_perfil:this.perfil
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
  				ruta: 'PersonasController/update',
  				data: {
  					id_persona:this.selected[0].id_persona, paterno:this.paterno, materno:this.materno, nombre:this.nombre, email:this.email, pass:this.pass, id_perfil:this.perfil
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
  	},

  	listarPerfiles() {
  		const _self = this;
  		const payload = {
	        ruta: 'PerfilesController/listarPerfilesForSelect',
	        data: ''
	    }
	      
	    request.postRequest(payload,this.$store).then( (res) => {
	      this.perfiles = res.data;
	    }).catch(error =>_self.$store.dispatch("main/muestraError",error));
  	}

  },//methods
  mounted() {
  	this.listar();
  	this.listarPerfiles();
  }
};
</script>

<style>
</style>
