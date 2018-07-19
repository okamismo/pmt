<template>
  <q-page padding>
    
	<q-table
	    :data="tableData"
	    :columns="columns"
	    row-key="id_perfil"
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
	    	<barra-abc @nuevo-clicked="nuevoPerfil" @editar-clicked="editarPerfil" @borrar-clicked="borrarPerfil"/>
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
			  icon="fas fa-user-secret"
			  label="Perfil"
			  helper="Escriba el nombre del perfil"
			  :error="perfilError"
			  error-label="El campo es obligatorio">
				<q-input v-model="perfil" />
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
				name: 'id_perfil',
				required: true,
				label: 'ID',
				align: 'left',
				field: 'id_perfil',
				sortable: true
			},
			{
				name: 'perfil',
				required: true,
				label: 'PERFIL',
				align: 'left',
				field: 'perfil',
				sortable: true
			}
		],
		tableData: [],
	    selected:[],
	    filter: '',
	    perfil: '',
	    perfilError: false
    }
  },
  components:{
  	Notify, Dialog, QTable, barraAbc, QField, QInput, QSearch, QModal, QModalLayout 
  },

  methods: {
  	limpiarCtrls() {
  		this.perfil = "";
  		this.perfilError = false;
  	},
  	nuevoPerfil() {
  		this.limpiarCtrls();
  		this.title = "Nuevo Perfil";
  		this.opened = true;
  	},
  	
  	
  	listarPerfiles() {
  		const _self = this;
  		const payload = {
	        ruta: 'PerfilesController/listarPerfiles',
	        data: ''
	    }
	      
	      request.postRequest(payload,this.$store).then( (res) => {
	      	this.tableData = res.data;
	      }).catch(error =>_self.$store.dispatch("main/muestraError",error));
  	},

  	insertarPerfil() {
  		if(this.perfil == ""){
  			this.perfilError = true;
  			return;
  		}else{
  			const _self = this;
  			this.perfilError = false;
  			const payload = {
		        ruta: 'PerfilesController/insertarPerfil',
		        data: {perfil:this.perfil}
		    }

		    request.postRequest(payload,this.$store).then((res)=>{
		    	_self.$store.dispatch("main/muestraMsg",res);
		    	_self.opened=false;
		    	_self.listarPerfiles();
		    }).catch(error =>_self.$store.dispatch("main/muestraError",error));
  		}

  	},//insertar

  	editarPerfil() {
  		this.limpiarCtrls();
  		this.title = "Editando Perfil";

  		if(this.selected.length == 1){
  			const _self = this;
  			const payload = {
		        ruta: 'PerfilesController/buscaPerfilById',
		        data: {id_perfil:this.selected[0].id_perfil}
		    }

		    request.postRequest(payload,this.$store).then( (res) => {
		      	
		      	if(res.data.error == "1") {
		      		_self.$store.dispatch("main/muestraMsg",res);
			    	_self.opened=false;
			    	_self.listarPerfiles();
		      	}else{
		      		this.perfil = (res.data.perfil).trim();
		      		this.opened = true;
		      	}
		    	

		    }).catch(error =>_self.$store.dispatch("main/muestraError",error));

  		}else{
  			Notify.create({
	          message: "Seleccione un Perfil",
	          timeout: 2000,
	          type: 'warning',
	          position: 'top-right',
	          icon:'fas fa-exclamation-triangle'
	        })
  		}
  	},

  	updatePerfil() {
  		const _self = this;
  		const payload = {
		    ruta: 'PerfilesController/updatePerfilById',
		    data: { id_perfil:this.selected[0].id_perfil, perfil:this.perfil }
		}

		request.postRequest(payload,this.$store).then( function (res) {
			_self.$store.dispatch("main/muestraMsg",res);
	    	_self.opened=false;
	    	_self.listarPerfiles();
		}).catch(error =>_self.$store.dispatch("main/muestraError",error));
  	},

  	borrarPerfil() {
  		if(this.selected.length == 1) {
  			const _self = this;
  			const payload = {
			    ruta: 'PerfilesController/borrarPerfilById',
			    data: { id_perfil:this.selected[0].id_perfil }
			}

			Dialog.create({
				title: 'Confirme su Acción',
				message: '¿Esta seguro de borrar el Perfil?',
				color: 'primary',
				ok:'Si, proceder',
				cancel: 'Cancelar'
			}).then( ()=>{
				request.postRequest(payload,this.$store).then( function (res) {
					_self.$store.dispatch("main/muestraMsg",res);
			    	_self.listarPerfiles();
				}).catch(error => _self.$store.dispatch("main/muestraError",error));
			}).catch( ()=>{
				
			});

  		}
  		else{
  			Notify.create({
	          message: "Seleccione un Perfil",
	          timeout: 2000,
	          type: 'warning',
	          position: 'top-right',
	          icon:'fas fa-exclamation-triangle'
	        })
  		}
  	},

  	guardarModalEvent() {
  		if( (this.title).startsWith("Nuevo")){
  			this.insertarPerfil();
  		}else{
  			this.updatePerfil();
  		}
  	}
  },

  mounted: function () {
  	this.listarPerfiles();
  }
};
</script>

<style>
</style>
