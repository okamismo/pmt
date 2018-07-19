<template>
  <q-page padding>
	<div class="row">
		<div class="col-sm-5">
			<q-card>
				<q-card-title>
					Estructura Jerarquica de Trabajo
				</q-card-title>
				<q-card-separator />
				<q-card-actions class="justify-center">
					<barra-abc @nuevo-clicked="nuevo" @editar-clicked="editar" @borrar-clicked="borrarNodo"/>
				</q-card-actions>
				<q-card-main>
					<!-- <q-input v-model="filter" stack-label="Buscar" clearable class="q-mb-sm"/> -->
					<q-tree ref="arbol" :nodes="nodos" node-key="id"  :selected.sync="selection"
						default-expand-all :expanded.sync="expanded" @lazy-load="onLazyLoad">
						
						<div slot="default-header" slot-scope="prop" class="row items-center">
							<q-icon :name="prop.node.icon" color="orange" class="q-mr-sm" >
								<q-tooltip>La suma de las ponderaciones supera el 100%, ajuste los valores</q-tooltip>
							</q-icon>
				            <div class="text-weight-bold">
				            	{{ prop.node.label }}
								<q-chip floating pointing="left" color="info" v-if="prop.node.ponderacion">
									{{prop.node.ponderacion}} %
								</q-chip>
				            </div>				          
				        </div>

					</q-tree>
				</q-card-main>
			</q-card>
		</div><!-- First Column -->
		<div class="col-sm-1">&nbsp;</div>
		<div class="col-sm-6">
			<q-card>
			  <q-card-title>
			    Definición de Paquetes de trabajo y tareas
			  </q-card-title>
			  <q-card-separator />
			  <q-card-main>
			  	<label class="q-mr-sm q-caption text-weight-medium text-primary">{{operacionLabel}}</label>
			    <q-field
				  icon="fas fa-cubes"
				  label="Nombre del paquete"
				  helper="Nombre del paquete, tarea o subtarea"
				  :error="paquete.nombreError"
				  error-label="El nombre del paquete/tarea/subtarea no puede ser vacío"
				>
				  <q-input ref="rNombre" v-model="paquete.nombre" />
				</q-field>

				<q-field
				  icon="fas fa-balance-scale"
				  label="Ponderación"
				  helper="Porcentaje dentro del proyecto completo"
				  :error="paquete.ponderacionError"
				  error-label="La ponderación es un campo obligatorio"
				>
				  <q-slider  v-model="paquete.ponderacion" snap label-always :min="minPonderacion" :max="maxPonderacion" :step=".1" :decimals="2" />
				</q-field>

				<div class="row justify-end">
					<q-btn-group rounded>
						<q-btn rounded  icon="fas fa-caret-left" color="secondary" @click="stepDown" size="xs"/>
						<q-btn rounded  icon="fas fa-caret-right" color="secondary" @click="stepUp" size="xs"/>	
					</q-btn-group>
				</div>

				<q-collapsible label="Detalles">
					<template slot="header">
						<q-item-main label="" />
					    <q-item-side right>
					    	<q-chip color="info" class="q-mr-sm" icon="fas fa-info-circle" >
						      Detallar
						      <q-tooltip anchor="bottom middle" self="top middle">Complementar la información sobre el paquete de trabajo</q-tooltip>
						    </q-chip>
					      
					    </q-item-side>
					</template>

					<q-field
					  icon="fas fa-bullseye"
					  label="Detalles"
					  helper="Detalles de las tareas/actividades del paquete">
					  	<q-input type="textarea" v-model="paquete.objetivo" />
					</q-field>

					<div class="row">
						<div class="col-sm-12">&nbsp;</div>
					</div>

					<div class="row">
						<div class="col-sm-5">
							<q-field icon="fas fa-calendar-alt" helper="Fecha en la que iniciará a resolver el paquete">
								<q-datetime v-model="paquete.finicio" type="date" format="YYYY-MM-DD" placeholder="Fecha de inicio" />
							</q-field>
						</div>
						<div class="col-sm-2">&nbsp;</div>
						<div class="col-sm-5 justify-end">
							<q-field icon="fas fa-calendar-alt" helper="Fecha de termino estimada para la resolución del paquete">
								<q-datetime v-model="paquete.ffin"  format="YYYY-MM-DD" type="date" placeholder="Fecha de Fin" />
							</q-field>
						</div>
					</div>
					
					<div class="row">
						<div class="col-sm-12">&nbsp;</div>
					</div>

					<div class="row">
						<div class="col-sm-5">
							<q-field icon="fas fa-clock" helper="Duración en horas para resolver el paquete (se recomienda no mas de 40 por semana por paquete)">
								<q-slider v-model="paquete.duracion" color="secondary" label-always :label-value="`${paquete.duracion}hrs`"
								 :min="0"  :max="100"	/>
							</q-field>
						</div>
						<div class="col-sm-2">&nbsp;</div>
						<div class="col-sm-5 ">
							<q-field icon="fas fa-user-secret" helper="El usuario debe tener el rol especifico requerido por el paquete/tarea/subtarea">
								<q-select v-model="paquete.ids_personas" :options="personas" multiple/>
							</q-field>
						</div>
					</div>

					<div class="row">
						<div class="col-sm-12">&nbsp;</div>
					</div>

					<div class="row">
						<div class="col-sm-12">
							<q-field icon="fas fa-tasks" label="Porcentaje de avance">
								<q-knob v-model="paquete.avance"  color="secondary" class="text-right">
									{{paquete.avance}} %
								</q-knob>
							</q-field>
						</div>
					</div>


				</q-collapsible>
				
			  </q-card-main>
			  <q-card-separator />

			  <q-card-actions class="justify-end">
			  	<label class="q-mr-sm q-caption text-weight-medium text-primary">{{operacionLabel}}</label>
			  	<q-btn flat round dense icon="fas fa-save" color="secondary" @click="guardarNodo">
					<q-tooltip anchor="center left" self="center right">
						Guardar
					</q-tooltip>
				</q-btn>
				
			  </q-card-actions>
			</q-card>
		</div><!-- Second Column -->
	</div>
  	

  </q-page>
</template>

<script>

import request from '../plugins/request';
import barraAbc from '../components/barra-abc';
import {QTree, QInput, QBtn, QCard, QCardTitle, QCardMain, QCardActions,QCardSeparator, QField, QDatetime, QSlider, QSelect, QCollapsible, QChip, Notify, Dialog, date, QKnob, QTooltip, QBtnGroup } from 'quasar';

export default {
  
  data() {
  	return {
  		paquete:{
  			nombre: '',
  			nombreError: false,
  			objetivo: '',
  			finicio: undefined,
  			ffin: undefined,
  			ids_personas: [],
  			duracion: 0,
  			avance:0,
			ponderacion: 0.1,
			ponderacionError: false
  		},
		minPonderacion: 0.1,
		maxPonderacion: 100,
  		operacion:'',
  		personas:[],
  		nodoActual: '',
  		selection:'',
  		filter:'',
  		expanded: [],
  		nodos:
  		[
  			{
	  			id: 'root',
	  			label: this.$store.getters['main/getProyectoActual'].nombre,
	  			lazy: true,
	  			children: [],
	  			id_padre: null,
				icon: null
		  	}
  		]
  	}
  },
  computed: {
  	operacionLabel() {
  		if(this.operacion.startsWith("Nuevo")){
  			return this.operacion+" nodo hijo de: "+this.nodoActual;
  		}else{
  			return this.operacion+" nodo: "+this.nodoActual;
  		}
  	}
  },
  components:{
  	QTree, QInput, QBtn, QCard, QCardTitle, QCardMain, QCardActions, QCardSeparator, QField, QDatetime, QSlider, QSelect, QCollapsible, QChip, Notify, Dialog, barraAbc, QKnob, QTooltip, QBtnGroup
  },

  beforeMount() {
  	this.listarPersonas();
  },
  
  watch: {
  	selection(val) {
  		this.limpiarCtrls();
  		if(val === null){
  			return;
  		}

  		if(val == "root"){
  			this.operacion = "Nuevo";
  			this.nodoActual = this.$store.getters['main/getProyectoActual'].nombre;
  			return;
  		}
  		
  		this.operacion = 'Actualizar';
  		this.getPaqueteById(val).then(data=>this.nodoActual=data).catch( ()=>console.log("error"));
  	}
  },//watch

  methods: {

  	onLazyLoad({ node, key, done, fail }) {
  		const _self = this;
			const payload = {
	        ruta: 'EdtController/buscarSubNodos',
	        data: {
	        	id_proyecto: this.$store.getters['main/getProyectoActual'].id_proyecto,
	        	id_paquete: key
	    	}
	    };


	    request.postRequest(payload,this.$store).then( (res) =>{ 
			if(res.data.length !== undefined) {
				res.data.map( function(node,index,all) {
					if(node.id_padre === null) {
						node.id_padre = "root";
					}
					node.children = [];
				})
			}
			
			done(res.data)
		}).catch(error =>{
			fail();
			_self.$store.dispatch("main/muestraError",error)
		});
  	},
  	validar() {
  		
  		if(this.selection == '' || this.selection === null){
  			Notify.create({
	          message: 'Seleccione un nodo de la estructura jerarquica',
	          timeout: 2000,
	          type: 'warning',
	          position: 'top-right',
	          icon:'fas fa-exclamation-triangle'
	        });
	        return false;
  		}

  		if( (this.paquete.nombre).trim() == ''){
  			this.paquete.nombreError = true;
  			return false;
  		}
  		
  		return true;
  	},
  	nuevo() {
  		if(this.selection == '' || this.selection === null){
  			Notify.create({
	          message: 'Seleccione un nodo de la estructura jerarquica: ¿Qué nodo será del padre?',
	          timeout: 2000,
	          type: 'warning',
	          position: 'top-right',
	          icon:'fas fa-exclamation-triangle'
	        });
	        return false;
  		}

  		this.limpiarCtrls();
  		this.$refs.rNombre.focus();
  		this.operacion = "Nuevo";
		if(this.selection !== "root") {
			let node = this.buscaNodo();
			this.maxPonderacion = parseFloat(node.ponderacion);
		}
  	},
  	editar() {
  		this.limpiarCtrls();

  		if(this.selection == '' || this.selection === null){
  			Notify.create({
	          message: 'Seleccione un nodo de la estructura jerarquica: ¿Qué nodo editará?',
	          timeout: 2000,
	          type: 'warning',
	          position: 'top-right',
	          icon:'fas fa-exclamation-triangle'
	        });
	        return false;
  		}

  		if(this.selection == "root"){
  			this.operacion = "Nuevo";
  			this.nodoActual = this.$store.getters['main/getProyectoActual'].nombre;
  			return;
  		}
  		
  		this.operacion = 'Actualizar';
  		this.getPaqueteById(this.selection).then(data=>this.nodoActual=data).catch( ()=>console.log("error"));
  	},
  	insertar() {
  		const _self = this;
		const payload = {
	        ruta: 'EdtController/insertar',
	        data: {
	        	id_proyecto: this.$store.getters['main/getProyectoActual'].id_proyecto,
	        	id_padre: this.selection, //si es root no se debe guardar
	        	actividad: this.paquete.nombre,
				ponderacion: this.paquete.ponderacion,
	        	objetivo: this.paquete.objetivo,
	        	finicio: date.formatDate(this.paquete.finicio, 'YYYY-MM-DD'),
	        	ffin: date.formatDate(this.paquete.ffin, 'YYYY-MM-DD'),
	        	ids_personas: this.paquete.ids_personas,
	        	duracion: this.paquete.duracion,
	        	avance: this.paquete.avance
	    	}
	    };

	    request.postRequest(payload,this.$store).then( (res) => {
      		_self.$store.dispatch("main/muestraMsg",res);

	    	if(res.data.error == "0")
	    	{
	    		let img = "";
	    		if(!res.data.ponderacion){
	    			img = "fas fa-exclamation-triangle fa-xs";
	    		}
	    		let nodo = _self.buscaNodo();
				
		    	if(nodo !== undefined && nodo.children !== undefined){
		    		nodo.children.push({
		    			id:res.data.id_paquete,
		    			label:_self.paquete.nombre,
		    			lazy:true,
		    			id_padre: _self.selection,
		    			icon: img,
						ponderacion: this.paquete.ponderacion,
						children:[]
		    		});
		    		_self.expanded.push(nodo.id);
		    	}
	    	}
    	
	    }).catch(error =>{_self.$store.dispatch("main/muestraError",error);console.log(error)});
  	},
  	actualizar() {
  		const _self = this;
		let nodo = this.buscaNodo();
		
		const payload = {
	        ruta: 'EdtController/actualizar',
	        data: {
	        	id_paquete: this.selection,
	        	id_proyecto: this.$store.getters['main/getProyectoActual'].id_proyecto,
	        	id_padre: nodo.id_padre, 
				ponderacion: this.paquete.ponderacion,
	        	actividad: this.paquete.nombre,
	        	objetivo: this.paquete.objetivo,
	        	finicio: date.formatDate(this.paquete.finicio, 'YYYY-MM-DD'),
	        	ffin: date.formatDate(this.paquete.ffin, 'YYYY-MM-DD'),
	        	ids_personas: this.paquete.ids_personas,
	        	duracion: this.paquete.duracion,
	        	avance: this.paquete.avance
	    	}
	    };

	    request.postRequest(payload,this.$store).then( (res) => {
      		_self.$store.dispatch("main/muestraMsg",res);
	    	
	    	if(nodo !== undefined){
	    		nodo.label = res.data.nodo.label;
				nodo.icon = res.data.nodo.icon;
				nodo.ponderacion = res.data.nodo.ponderacion;

	    		_self.expanded.map( (id,index,all)=>{
	    			if(id == nodo.id_padre){
	    			//quitamos y ponemos para causar efecto de refrescado
	    				all.splice(index,1);
	    				all.push(nodo.id_padre);
	    			}
	    		});
	    	}
	    }).catch(error =>_self.$store.dispatch("main/muestraError",error));
  	},
  	buscaNodo(where=this.nodos,id=this.selection) {
  		const len = where.length;
  		
  		for(let i=0; i<len; i++){
  			if(id == where[i].id){
  				return where[i]; //found!
  			}else if(where[i].hasOwnProperty("children") && where[i].children.length >0){
  				let isFound = this.buscaNodo(where[i].children,id);
  				if(isFound !== undefined)
  					return isFound;
  			}
  		}

  		return undefined;
  	},
  	buscaNodoPos(where=this.nodos[0].children) {
  		const id = this.selection;
  		const len = where.length;
  		
  		for(let i=0; i<len; i++){
  			if(id == where[i].id){
  				return { container: where, pos: i }; //found!
  			}else if(where[i].hasOwnProperty("children") && where[i].children.length >0){
  				let isFound = this.buscaNodoPos(where[i].children);
  				if(isFound !== undefined)
  					return isFound;
  			}
  		}

  		return undefined;
  	},
  	guardarNodo() {
  		
  		if(this.validar())
  		{
  			if((this.operacion).startsWith("Nuevo")){
  				this.insertar();
  			}else{
  				this.actualizar();
  			}
  		}
  	},//agregarNodo
  	borrarNodo() {
  		let msg = '';
  		if(this.selection == '' || this.selection === null){
  			msg = 'Seleccione un nodo de la estructura jerarquica';
  		}
  		else if(this.selection == "root"){
  			msg = 'Seleccione un nodo diferente, el proyecto completo no puede borrarse';
  		}

  		if(msg != '')
  		{
  			Notify.create({
	          message: msg,
	          timeout: 2000,
	          type: 'warning',
	          position: 'top-right',
	          icon:'fas fa-exclamation-triangle'
		    });
		    return false;
  		}

  		const _self = this;
  		const payload = {
	        ruta: 'EdtController/borrarById',
	        data: {
	        	id_proyecto: this.$store.getters['main/getProyectoActual'].id_proyecto,
	        	id_paquete: this.selection
	    	}
	    };

  		Dialog.create({
				title: 'Confirme su Acción',
				message: '¿Esta seguro de borrar el Nodo? Si el nodo tiene hijos tambien se borrarán',
				color: 'primary',
				ok:'Si, proceder',
				cancel: 'Cancelar'
		}).then( ()=>{
			request.postRequest(payload,this.$store).then( function (res) {
				let nodo = _self.buscaNodoPos();
				//quitamos del array los elementos expandidos
				_self.expanded.map((item,index,all)=>{
	    			if(item == _self.selection || item == nodo.container[nodo.pos].id_padre){
	    				all.splice(index,1);
	    			}
	    			
	    		});
	    		
	    		//borramos del arreglo el nodo
		  		if(nodo !== undefined){
		  			nodo.container.splice(nodo.pos,1);
		  			_self.limpiarCtrls();
		  		}

	    		_self.$store.dispatch("main/muestraMsg",res);
			}).catch(error => _self.$store.dispatch("main/muestraError",error));
		}).catch( ()=>{
			
		});
  	},//borrarNodo
  	
  	limpiarCtrls() {
  		this.paquete.nombre = '';
  		this.paquete.objetivo = '';
    	this.paquete.finicio = '';
    	this.paquete.ffin = '';
    	this.paquete.ids_personas = [];
    	this.paquete.duracion = 0;
    	this.paquete.avance = 0;
		this.paquete.ponderacion = 0.1;
  	},
  	listarPersonas() {
  		const _self = this;
		const payload = {
	        ruta: 'ProyectosPersonasController/listarForSelect',
	        data: {
	        	id_proyecto: this.$store.getters['main/getProyectoActual'].id_proyecto
	    	}
	    };

	    request.postRequest(payload,this.$store).then( (res) => {
      		this.personas = res.data;
	    }).catch(error =>_self.$store.dispatch("main/muestraError",error));
  	},
	stepDown() {
		this.paquete.ponderacion -= 0.1;
		this.paquete.ponderacion = parseFloat(this.paquete.ponderacion.toFixed(2));
	},
	stepUp() {
		this.paquete.ponderacion += 0.1;
		this.paquete.ponderacion = parseFloat(this.paquete.ponderacion.toFixed(2));
	},
  	getPaqueteById (val) {
  		const _self = this;
  		const payload = {
	        ruta: 'EdtController/getPaqueteById',
	        data: {
	        	id_proyecto: this.$store.getters['main/getProyectoActual'].id_proyecto,
	        	id_paquete: val
	    	}
	    };
	    
	    return request.postRequest(payload,this.$store).then( (res) => {
	    	this.paquete.nombre = res.data.actividad;
			this.paquete.ponderacion = (res.data.ponderacion !== null)?parseFloat(res.data.ponderacion):0.1;
	    	this.paquete.objetivo = res.data.objetivo;
	    	this.paquete.finicio = res.data.finicio+" 00:00";//date.formatDate(res.data.finicio, 'YYYY-MM-DD');
	    	this.paquete.ffin = res.data.ffin+" 00:00";//date.formatDate(res.data.ffin, 'YYYY-MM-DD');
	    	this.paquete.ids_personas = res.data.ids_personas;
	    	this.paquete.duracion = (res.data.duracion !== null)?parseInt(res.data.duracion):0;
	    	this.paquete.avance = parseInt(res.data.avance);

			if(res.data.id_padre !== null && res.data.id_padre != "root") {
				let node = this.buscaNodo(this.nodos,res.data.id_padre);
				if(node !== undefined) {
					this.maxPonderacion = parseFloat(node.ponderacion);
				}
			}else{
				this.maxPonderacion = 100;
			}
			

	    	return new Promise( (resolve,reject)=>{
	    		resolve(this.paquete.nombre);
	    	});
	    	
	    }).catch(error =>_self.$store.dispatch("main/muestraError",error));
  	}

  }//methods

};//export

</script>

<style scoped>

	
</style>