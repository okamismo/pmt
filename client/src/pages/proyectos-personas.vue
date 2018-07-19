<template>
  <q-page padding>

	<q-field icon="fas fa-briefcase" label="Seleccione un Proyecto" :error="proyectoError" 
		error-label="Debe seleccionar un Proyecto">
		<q-select clearable	:options="proyectos" v-model="proyecto" @input="listarPersonasAsociadas" 
			filter filter-placeholder="Buscar"/>
	</q-field>

	<div class="row">
		<div class="col-sm-12">&nbsp;</div>
	</div>

	<q-tabs color="secondary" glossy >

	  <q-tab default slot="title" name="tab-1" icon="fas fa-users">
	  	<q-tooltip>Integrar el equipo del proyecto</q-tooltip>
	  </q-tab>
	  <q-tab slot="title" name="tab-2" icon="fas fa-address-card">
	  	<q-tooltip>Especificación de Roles</q-tooltip>
	  </q-tab>

	  <!-- Targets -->
	  <q-tab-pane name="tab-1">

		<p class="text-info">{{proySel}}</p>

	  	<div class="row no-wrap">
			<div class="col-xs-5">
				<q-table title="Personas Disponibles" :data="pexistentes" :columns="columns" row-key="id_persona"
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
				<q-table title="Equipo del Proyecto" :data="pasociadas" :columns="columns" row-key="id_persona"
			    	selection="multiple" :selected.sync="selectedA" :filter="filterA" color="red">
			    	<template slot="top-right" slot-scope="props">
						<q-search color="red"	v-model="filterA" placeholder="Buscar" clearable/>
					</template>
			    </q-table>
			</div>
		</div>
	  </q-tab-pane>
	  
	  <q-tab-pane name="tab-2">
		
		<div class="row">
			
			<div class="col-xs-5">
				<q-table title="Equipo del Proyecto" :data="pasociadas" :columns="columns" row-key="id_persona"
			    	selection="single" :selected.sync="selectedA" :filter="filterA" color="secondary">
			    	<template slot="top-right" slot-scope="props">
						<q-search color="secondary"	v-model="filterA" placeholder="Buscar" clearable/>
					</template>
			    </q-table>
			</div>
			<div class="col-xs-2">&nbsp;</div>
			<div class="col-xs-5">
				<q-card>
				  <q-card-title>
				    {{nombrePersona}}
				  </q-card-title>
				  
				  <q-card-main>
				    <label class="text-info"> {{proySel}}</label>
				    
				    <div class="row q-mt-sm">
				    	<div class="col-xs-12">
				    		<q-field label="Rol" helper="Función que realizará dentro del proyecto">
				    			<q-select clearable	:options="roles" v-model="rol"  
				    			filter filter-placeholder="Buscar"/>
				    		</q-field>
				    	</div>
				    </div>
				  </q-card-main>
				  <q-card-actions align="end">
				    <q-btn flat dense icon="fas fa-save" label="Guardar" color="primary" @click="guardarPerfil">
				    	<q-tooltip>Guardar el Rol seleccionado</q-tooltip>
				    </q-btn>
				  </q-card-actions>
				</q-card>
			</div>
		</div>

	  </q-tab-pane>
	</q-tabs>

	
  </q-page>
</template>

<script>
import {QSelect, QField, QTable, QSearch, Notify, QTabs, QTab, QTabPane, QRouteTab, QTooltip, QCard, QCardTitle, QCardMain, QCardMedia, QCardActions} from 'quasar' 
import request from '../plugins/request';

export default {
  data() {
  	return{
  		proyectos: [{label:'',value:''}],
  		proyecto:null,
  		proyectoError:false,
  		columns: 
    	[
			{
				name: 'id_persona',
				required: false,
				label: 'ID',
				align: 'left',
				field: 'id_persona',
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
		],
  		pexistentes: [],
  		pasociadas:[],
  		selectedE:[],
	    filterE: '',
	    selectedA:[],
	    filterA: '',
	    roles: [],
	    rol: null
  	}
  	
  },
  components: {
  	QSelect, QField, QTable, QSearch, Notify, QTabs, QTab, QTabPane, QRouteTab, QTooltip, QCard, QCardTitle, QCardMain, QCardMedia, QCardActions
  },
  methods: {
  	agregar() {

  		if(this.proyecto === null)
  		{
  			Notify.create({
	          message: "Seleccione un Proyecto del listado",
	          timeout: 2000,
	          type: 'warning',
	          position: 'top-right',
	          icon:'fas fa-exclamation-triangle'
	        })
	      	return;
  		}

  		if(this.selectedE.length == 0) {
  			Notify.create({
	          message: "Seleccione un Registro o mas de la tabla Personas Existentes",
	          timeout: 2000,
	          type: 'warning',
	          position: 'top-right',
	          icon:'fas fa-exclamation-triangle'
	        })
	      	return;
  		}
  		const _self = this;
  		let asoc = this.pasociadas.map(val=>val.id_persona);
  		let ids = this.selectedE.map(val=>val.id_persona).filter(val=>{
  			return !asoc.includes(val)
  		});
  		
  		if(ids.length == 0){
  			Notify.create({
	          message: "Las personas seleccionadas ya estan asociadas...",
	          timeout: 2000,
	          type: 'info',
	          position: 'top-right',
	          icon:'fas fa-info-circle'
	        })
	      	return;
  		}

  		const payload = {
	        ruta: 'ProyectosPersonasController/agregar',
	        data: {ids: ids, id_proyecto:this.proyecto}
	    }
	      
	    request.postRequest(payload,this.$store).then( (res) => {
	    	if(res.data.insertado=="1"){
	    		this.listarPersonasAsociadas();
	    	}
	    	_self.$store.dispatch("main/muestraMsg",res);
	    	_self.opened=false;
	    }).catch(error =>_self.$store.dispatch("main/muestraError",error));
  		
  	},
  	quitar() {
  		if(this.proyecto === null)
  		{
  			Notify.create({
	          message: "Seleccione un Proyecto del listado",
	          timeout: 2000,
	          type: 'warning',
	          position: 'top-right',
	          icon:'fas fa-exclamation-triangle'
	        })
	      	return;
  		}

  		if(this.selectedA.length == 0) {
  			Notify.create({
	          message: "Seleccione un Registro o mas de la tabla Personas Asociadas",
	          timeout: 2000,
	          type: 'warning',
	          position: 'top-right',
	          icon:'fas fa-exclamation-triangle'
	        })
	      	return;
  		}
  		const _self = this;
  		let ids = this.selectedA.map(val=>val.id_persona);

  		const payload = {
	        ruta: 'ProyectosPersonasController/borrar',
	        data: {ids: ids, id_proyecto:this.proyecto}
	    }
	      
	    request.postRequest(payload,this.$store).then( (res) => {
	    	if(res.data.borrado=="1"){
	    		this.listarPersonasAsociadas();
	    	}
	    	_self.$store.dispatch("main/muestraMsg",res);
	    	_self.opened=false;
	    }).catch(error =>_self.$store.dispatch("main/muestraError",error));
  	},
  	listarCatProyectos() {
  		const _self = this;
  		const payload = {
	        ruta: 'ProyectosController/listarProyectosForSelect'
	    }
	      
	    request.postRequest(payload,this.$store).then( (res) => {
	     	this.proyectos = res.data;
	    }).catch(error =>_self.$store.dispatch("main/muestraError",error));
  	},
  	
  	listarRoles() {
  		const _self = this;
  		const payload = {
	        ruta: 'RolesController/listarRolesForSelect'
	    }
	      
	    request.postRequest(payload,this.$store).then( (res) => {
	     	this.roles = res.data;
	    }).catch(error =>_self.$store.dispatch("main/muestraError",error));
  	},
  	listarPersonasAsociadas() {
  		
  		if(this.proyecto !== null)
  		{
  			const _self = this;
	  		const payload = {
		        ruta: 'ProyectosPersonasController/listar',
		        data:{id_proyecto:this.proyecto}
		    }
		    
		    request.postRequest(payload,this.$store).then( (res) => {
		    	this.pasociadas = res.data;
		    }).catch(error =>_self.$store.dispatch("main/muestraError",error));
  		}else{
  			this.pasociadas = [];
  		}
  	},
  	listarPersonasExistentes() {
  		const _self = this;
  		const payload = {
	        ruta: 'PersonasController/listar'
	    }
	      
      	request.postRequest(payload,this.$store).then( (res) => {
      		res.data.map((val)=>{
      			val.nombre = val.nombre+' '+val.paterno;
      		});
      		this.pexistentes = res.data;
      	}).catch(error =>_self.$store.dispatch("main/muestraError",error));
  	},

  	guardarPerfil() {
  		if(this.proyecto === null){
  			Notify.create({
	          message: "Seleccione un Proyecto",
	          timeout: 2000,
	          type: 'warning',
	          position: 'top-right',
	          icon:'fas fa-exclamation-triangle'
	        })
	      	return;
  		}
  		if(this.selectedA.length === 0){
  			Notify.create({
	          message: "Seleccione un miembro del equipo",
	          timeout: 2000,
	          type: 'warning',
	          position: 'top-right',
	          icon:'fas fa-exclamation-triangle'
	        })
	      	return;
  		}
  		if(this.rol===null){
  			Notify.create({
	          message: "Seleccione el rol a guardar",
	          timeout: 2000,
	          type: 'warning',
	          position: 'top-right',
	          icon:'fas fa-exclamation-triangle'
	        })
	      	return;
  		}

  		const _self = this;
  		const payload = {
	        ruta: 'ProyectosPersonasController/update',
	        data: {id_proyecto:this.proyecto, id_persona:this.selectedA[0].id_persona, id_rol:this.rol}
	    }
	      
      	request.postRequest(payload,this.$store).then( (res) => {
      		_self.$store.dispatch("main/muestraMsg",res);
      	}).catch(error =>_self.$store.dispatch("main/muestraError",error));
  	},
  	getDatosActuales() {
  		if(this.selectedA.length>0)
  		{
  			const _self = this;
	  		const payload = {
		        ruta: 'ProyectosPersonasController/getDatosActuales',
		        data: {id_proyecto:this.proyecto, id_persona:this.selectedA[0].id_persona}
		    }
		      
	      	request.postRequest(payload,this.$store).then( (res) => {
	      		this.rol = res.data.id_rol;
	      	}).catch(error =>_self.$store.dispatch("main/muestraError",error));
  		}
  	}

  },
  computed: {
  	proySel() {
  		const p = this.proyectos.find(el=>{
  			return el.value == this.proyecto
  		});
  		if(p !== undefined){
  			return p.label;
  		}else{
  			this.selectedA = [];
  			this.rol = null;
  		}
  	},
  	nombrePersona() {
  		this.getDatosActuales();
  		if(this.selectedA[0] !== undefined)
  			return (this.selectedA[0].nombre);
  		else
  			return "";
  	}
  },
  mounted () {
  	this.listarCatProyectos();
  	this.listarPersonasExistentes();
  	this.listarRoles();
  }
};
</script>

<style>
</style>
