<template>
  <div>
  	<q-card>
		<q-card-title>
			Monitor de Tareas
		</q-card-title>
		<q-card-separator />
		
		<q-card-main>

			<q-table :data="tareas" :columns="columnas" row-key="id_paquete" :pagination.sync="serverPagination" @request="listar" :filter="filter" :loading="loading" color="secondary" class="no-shadow" :visible-columns="visibleColumns" selection="single" :selected.sync="selected" dense>
				<template slot="top-left" slot-scope="props">
					<q-search v-model="filter" placeholder="Buscar Actividad"/>
					<q-select ref="listaPersonas" id="listaPersonas" v-if="mostrarFiltroPersona" v-model="tareasPersona" :options="opcionesPersonas" clearable @input="refrescarTareas" :before="[{icon: 'fas fa-address-card', handler () { muestraLista() }}]" />
					
			    </template>
				<template slot="top-right" slot-scope="props">
					<q-btn-group rounded>
						<q-btn icon="fas fa-comment-dots" color="secondary" size="md" @click="abreModal('comentarios')">
							<q-tooltip anchor="center left" :offset="[10, 10]">Notas/Comentarios</q-tooltip>
						</q-btn>
						<q-btn icon="fas fa-file-pdf" color="secondary" size="md" @click="abreModal('documentos')">
							<q-tooltip anchor="center left" :offset="[10, 10]">Archivos asociados</q-tooltip>
						</q-btn>
					</q-btn-group>

			        <q-table-columns color="secondary" class="q-mr-sm q-ml-lg" v-model="visibleColumns" :columns="columnas"/>
			    </template>
			    
			    <q-td slot="body-cell-semaforo" slot-scope="props" :props="props">
					
					<q-chip :color="props.row.color" class="shadow-2">
			    		{{ (props.row.semaforo === '')?"¿?" : props.row.semaforo}} días
			    	</q-chip>
					<q-btn v-if="props.row.nota" @click="mostrarComponente(props.row,'comentarios')" class="q-ml-sm" flat icon="fas fa-comment-dots" round dense color="secondary" >
						<q-chip floating color="red">{{props.row.nota}}</q-chip>
					</q-btn>
					<q-btn v-if="props.row.docto" @click="mostrarComponente(props.row,'documentos')" class="q-ml-sm" flat icon="fas fa-file-pdf" color="secondary" round dense>
						<q-chip floating color="red">{{props.row.docto}}</q-chip>
					</q-btn>
			    </q-td>
			    
			    <q-td slot="body-cell-actividad" slot-scope="props" :props="props">
		    		{{props.row.actividad}}

		    		<q-tooltip v-if="props.row.objetivo" anchor="center middle" :offset="[10, 10]">
		    			{{props.row.objetivo}}
		    		</q-tooltip>
			    </q-td>

				<!-- slot name syntax: body-cell-<column_name> -->
				<q-td slot="body-cell-avance" slot-scope="props" :props="props">
			     <q-knob v-model="props.row.avance" :min="0" :max="100" @input="avanceModificado(props.row.avance,props.row)"
			     	color="secondary" class="text-right" size="50px">
					{{props.row.avance}} %
				</q-knob>
			
			
			  </q-td>
		
			
			</q-table>
		</q-card-main>
	</q-card>

	<q-modal ref="modal" v-model="opened" :content-css="{minWidth: '80vw', minHeight: '83vh'}">
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
	      <!-- <q-btn  glossy color="secondary" rounded label="Guardar" @click="guardarModalEvent"/> -->
	    </q-toolbar>

	    <div class="layout-padding">
	    	<!--contenido-->
	    	
	    		<component ref="modalComponent" v-bind:is="componenteActual" v-bind:paquete="paquete" v-bind="options" @comentario-agregado="refrescarTareas" @comentario-borrado="refrescarTareas" @documento-agregado="refrescarTareas" @documento-borrado="refrescarTareas"></component>
	      	
	    
	    </div>
	  </q-modal-layout>
	</q-modal>

  </div>
</template>

<script>
import request from '../plugins/request';
import comentarios from '../components/comentarios';
import documentos from '../components/documentos';
import {QCard, QCardTitle, QCardMain, QCardActions, QCardSeparator, QTable, QTableColumns, QTd, QKnob, QSearch, QChip, QBtnGroup, QTooltip, QModal, QModalLayout, Notify, QSelect} from 'quasar';

export default {
  // name: 'ComponentName',
  data () {
    return {
    	filter:'',
    	loading:false,
    	serverPagination:{
    		page: 1,
        	rowsNumber: 10 // specifying this determines pagination is server-side
    	},
    	columnas: 
    	[
    		{
		        name: 'id_paquete',
		        required: false,
		        label: 'ID',
		        align: 'left',
		        field: 'id_paquete',
		        sortable: true
		    },
		    {
		    	name: 'semaforo',
		        required: true,
		        label: 'DÍAS RESTANTES',
		        align: 'left',
		        field: 'semaforo',
		        sortable: false
		    },
		    {
		        name: 'actividad',
		        required: true,
		        label: 'ACTIVIDAD',
		        align: 'left',
		        field: 'actividad',
		        sortable: true
		    },
		    {
		        name: 'avance',
		        required: true,
		        label: 'AVANCE',
		        align: 'left',
		        field: 'avance',
		        sortable: true
		    },
		    {
		        name: 'finicio',
		        required: true,
		        label: 'DEBE INICIAR',
		        align: 'left',
		        field: 'finicio',
		        sortable: true
		    },
		    {
		        name: 'ffin',
		        required: true,
		        label: 'TERMINO ESTIMADO',
		        align: 'left',
		        field: 'ffin',
		        sortable: true
		    },
		    {
		        name: 'duracion',
		        required: false,
		        label: 'HRS ESTIMADAS',
		        align: 'left',
		        field: 'duracion',
		        sortable: true
		    }
    	],
    	visibleColumns:['actividad','avance','finicio','ffin'],
    	tareas: [],
    	selected: [],
    	opened:false,
    	title:'',
    	paquete: 0,
    	componenteActual:'comentarios',
    	tareasPersona:'',
    	opcionesPersonas:[]
    }
  },
  props: ['proyecto'],
  components: {
  	comentarios, documentos,
  	QCard, QCardTitle, QCardMain, QCardActions, QCardSeparator, QTable, QTableColumns, QTd, QKnob, QSearch, QChip, QBtnGroup, QTooltip, QModal, QModalLayout, Notify, QSelect
  },
  
  computed: {
  	
  	options () {
  		
  		if(this.componenteActual == "documentos") {
  			return {
  				['extensiones-permitidas']:'.pdf',
  				['tamanio-maximo']:'2'//en megas
  			}
  		}
  		
  	},//options
  	mostrarFiltroPersona() {
  		
  		if(this.$store.getters['main/getPerfil'] < this.$store.getters['main/getPerfiles'].PMT_USUARIO) {
  			return true;
  		}else{
  			return false;
  		}
  	}
  	
  },
  watch:{ 
  		proyecto(){
  			this.listar({pagination: this.serverPagination, filter:this.filter});
  			if( parseInt(this.proyecto)>0){
  				this.getCatPersonas();
  			}
  		}
  },
  methods: {
  	listar({pagination, filter}) {

  		if(this.proyecto == 0){
  			return;
  		}
  		this.loading = true;

  		const _self = this;
		const payload = {
	        ruta: 'EdtController/listarTareas',
	        data: {
	        	id_proyecto: this.proyecto,
	        	page: pagination.page,
	        	rowsPerPage: pagination.rowsPerPage,
	        	sortBy: pagination.sortBy,
	        	sentido: (pagination.descending)?"DESC":"ASC",
	        	filter: filter
	    	}
	    };
	    
	    if(this.$refs.listaPersonas !== undefined) {
	    	payload.data.id_persona = this.tareasPersona;
	    }
	    
	    request.postRequest(payload,this.$store).then( (res) => {
	    	
	    	// updating pagination to reflect in the UI
	    	this.serverPagination = pagination
	    	// we also set (or update) rowsNumber
	    	
        	this.serverPagination.rowsNumber = res.data.rowsNumber
	    	
	    	res.data.rows.map( (item,index,all)=>{
	    		all[index]["avance"] = parseInt(all[index]["avance"]);
	    	});
	    	

	    	// then we update the rows with the fetched ones
        	this.tareas = res.data.rows;
      		//_self.tareas = res.data;
      		// finally we tell QTable to exit the "loading" state
        	this.loading = false;
        	
	    }).catch(error =>{_self.$store.dispatch("main/muestraError",error);this.loading = false;console.log(error)});
  	},

  	avanceModificado(val,row)
  	{
  		const _self = this;
		const payload = {
	        ruta: 'EdtController/cambiarAvanceTarea',
	        data: {
	        	id_proyecto: this.proyecto,
	        	id_paquete: row.id_paquete,
	        	avance: val
	    	}
	    };
	    
	    request.postRequest(payload,this.$store).then( (res) => {
	    	if(res.error == "1" ){
	    		_self.$store.dispatch("main/muestraMsg",res);
	    	}
	    }).catch(error =>{_self.$store.dispatch("main/muestraError",error);console.log(error)});
  	},
  	getDescripcionActividad(id_paquete) {
  		let row = this.tareas.find((item)=>{
			return item.id_paquete == id_paquete;
		});
		return (row.actividad !== null && row.actividad !== undefined)?row.actividad:'';
  	},
  	abreModal(component) {
  		
  		if(this.selected.length>0){
  			this.componenteActual = component;
  			this.paquete = parseInt(this.selected[0].id_paquete);

  			this.title = this.getDescripcionActividad(this.paquete);
  			this.opened = true;
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

  	getCatPersonas()
  	{
  		const _self = this;
		const payload = {
	        ruta: 'ProyectosPersonasController/listarForSelect',
	        data: {
	        	id_proyecto: this.proyecto
	    	}
	    };
	    
	    request.postRequest(payload,this.$store).then( (res) => {
	    	if(res.error == "1" ){
	    		_self.$store.dispatch("main/muestraMsg",res);
	    	}else{
	    		_self.opcionesPersonas = res.data;
	    	}
	    	
	    }).catch(error =>{_self.$store.dispatch("main/muestraError",error);console.log(error)});
  	},

  	refrescarTareas()
  	{
  		this.listar({pagination: this.serverPagination, filter:this.filter});
  	},
  	muestraLista()
  	{
  		this.$refs.listaPersonas.show();
	},
	mostrarComponente(row,componente) {
		this.selected = [];
		this.selected.push(row);
		this.abreModal(componente);
	}

  }
};

</script>

<style scoped>
	#listaPersonas {
		margin-left:10px;
	}
</style>
