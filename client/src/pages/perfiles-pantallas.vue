<template>
  <q-page padding>
	<q-field icon="fas fa-user-secret" label="Seleccione un Perfil" :error="perfilError" 
		error-label="Debe seleccionar un Perfil">
		<q-select clearable	:options="perfiles"	v-model="perfil" @input="listarPantallasAsociadas" 
			filter filter-placeholder="Buscar"/>
	</q-field>

	<div class="row">
		<div class="col-sm-12">&nbsp;</div>
	</div>

	<div class="row no-wrap">
		<div class="col-xs-5">
			<q-table title="Pantallas Existentes" :data="pexistentes" :columns="columns" row-key="id_pantalla"
		    	selection="multiple" :selected.sync="selectedE" :filter="filterE" color="secondary">
			    <template slot="top-right" slot-scope="props">
					<q-search color="secondary"	v-model="filterE" placeholder="Buscar" clearable/>
				</template>
			</q-table>
		</div>
		<div class="col-xs-2 self-center">
			<div class="row justify-center">
				<div class="col-12 text-center">
					<q-btn color="secondary" icon="fas fa-plus-circle" class="q-ma-xs" @click="agregar" />
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12 text-center">
					<q-btn color="red" icon="fas fa-minus-circle" @click="quitar"/>
				</div>
			</div>
			
			
		</div>
		<div class="col-xs-5">
			<q-table title="Pantallas Asociadas" :data="pasociadas" :columns="columns" row-key="id_pantalla"
		    	selection="multiple" :selected.sync="selectedA" :filter="filterA" color="red">
		    	<template slot="top-right" slot-scope="props">
					<q-search color="red"	v-model="filterA" placeholder="Buscar" clearable/>
				</template>
		    </q-table>
		</div>
	</div>
	
	
  </q-page>
</template>

<script>
import {QSelect, QField, QTable, QSearch, Notify} from 'quasar' 
import request from '../plugins/request';

export default {
  data() {
  	return{
  		perfiles: [{label:"TST",value:"as"}],
  		perfil:null,
  		perfilError:false,
  		columns: 
    	[
			{
				name: 'id_pantalla',
				required: false,
				label: 'ID',
				align: 'left',
				field: 'id_pantalla',
				sortable: true
			},
			{
				name: 'nombre_menu',
				required: true,
				label: 'PANTALLA',
				align: 'left',
				field: 'nombre_menu',
				sortable: true
			},
		],
  		pexistentes: [],
  		pasociadas:[],
  		selectedE:[],
	    filterE: '',
	    selectedA:[],
	    filterA: '',
  	}
  	
  },
  components: {
  	QSelect, QField, QTable, QSearch, Notify
  },
  methods: {
  	agregar() {

  		if(this.perfil === null)
  		{
  			Notify.create({
	          message: "Seleccione un Perfil del listado",
	          timeout: 2000,
	          type: 'warning',
	          position: 'top-right',
	          icon:'fas fa-exclamation-triangle'
	        })
	      	return;
  		}

  		if(this.selectedE.length == 0) {
  			Notify.create({
	          message: "Seleccione un Registro o mas de la tabla Pantallas Existentes",
	          timeout: 2000,
	          type: 'warning',
	          position: 'top-right',
	          icon:'fas fa-exclamation-triangle'
	        })
	      	return;
  		}
  		const _self = this;
  		let asoc = this.pasociadas.map(val=>val.id_pantalla);
  		let ids = this.selectedE.map(val=>val.id_pantalla).filter(val=>{
  			return !asoc.includes(val)
  		});
  		
  		if(ids.length == 0){
  			Notify.create({
	          message: "Las pantallas seleccionadas ya estan asociadas...",
	          timeout: 2000,
	          type: 'info',
	          position: 'top-right',
	          icon:'fas fa-info-circle'
	        })
	      	return;
  		}

  		const payload = {
	        ruta: 'PerfilesPantallasController/agregar',
	        data: {ids: ids, id_perfil:this.perfil}
	    }
	      
	    request.postRequest(payload,this.$store).then( (res) => {
	    	if(res.data.insertado=="1"){
	    		this.listarPantallasAsociadas();
	    	}
	    	_self.$store.dispatch("main/muestraMsg",res);
	    	_self.opened=false;
	    }).catch(error =>_self.$store.dispatch("main/muestraError",error));
  		
  	},
  	quitar() {
  		if(this.perfil === null)
  		{
  			Notify.create({
	          message: "Seleccione un Perfil del listado",
	          timeout: 2000,
	          type: 'warning',
	          position: 'top-right',
	          icon:'fas fa-exclamation-triangle'
	        })
	      	return;
  		}

  		if(this.selectedA.length == 0) {
  			Notify.create({
	          message: "Seleccione un Registro o mas de la tabla Pantallas Asociadas",
	          timeout: 2000,
	          type: 'warning',
	          position: 'top-right',
	          icon:'fas fa-exclamation-triangle'
	        })
	      	return;
  		}
  		const _self = this;
  		let ids = this.selectedA.map(val=>val.id_pantalla);

  		const payload = {
	        ruta: 'PerfilesPantallasController/borrar',
	        data: {ids: ids, id_perfil:this.perfil}
	    }
	      
	    request.postRequest(payload,this.$store).then( (res) => {
	    	if(res.data.borrado=="1"){
	    		this.listarPantallasAsociadas();
	    	}
	    	_self.$store.dispatch("main/muestraMsg",res);
	    	_self.opened=false;
	    }).catch(error =>_self.$store.dispatch("main/muestraError",error));
  	},
  	listarCatPerfiles() {
  		const _self = this;
  		const payload = {
	        ruta: 'PerfilesController/listarPerfilesForSelect'
	    }
	      
	      request.postRequest(payload,this.$store).then( (res) => {
	      	this.perfiles = res.data;
	      }).catch(error =>_self.$store.dispatch("main/muestraError",error));
  	},
  	listarPantallasAsociadas() {
  		
  		if(this.perfil !== null)
  		{
  			const _self = this;
	  		const payload = {
		        ruta: 'PerfilesPantallasController/listar',
		        data:{id_perfil:this.perfil}
		    }
		    
		    request.postRequest(payload,this.$store).then( (res) => {
		    	this.pasociadas = res.data;
		    }).catch(error =>_self.$store.dispatch("main/muestraError",error));
  		}else{
  			this.pasociadas = [];
  		}
  	},
  	listarPantallasExistentes() {
  		const _self = this;
  		const payload = {
	        ruta: 'PantallasController/listar'
	    }
	      
	      request.postRequest(payload,this.$store).then( (res) => {
	      	this.pexistentes = res.data;
	      }).catch(error =>_self.$store.dispatch("main/muestraError",error));
  	}

  },
  mounted () {
  	this.listarCatPerfiles();
  	this.listarPantallasExistentes();
  }
};
</script>

<style>
</style>
