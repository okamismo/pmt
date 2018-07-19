<template>
  <div>
  	<q-card>
  		<q-scroll-area style="height: 30vw;">
  		<q-card-main>
  			
  				
					<q-list highlight no-border>

					  	<template v-for="comentario in comentarios">
						  <q-list-header>
						  	{{comentario.nombre}} {{comentario.paterno}} - {{comentario.factualizado}}
						  </q-list-header>
						  <q-item>
						    <q-item-side>
						      <q-item-tile avatar>
						      	<q-icon v-if="comentario.avatar=='fas fa-user-circle'" :name="comentario.avatar" size="2rem" />
						        <template v-else>
						        	<img :src="comentario.avatar">
						        </template>
						      </q-item-tile>
						    </q-item-side>
						    <q-item-main :label="comentario.nota" class="text-justify" />
						    <q-item-side right class="self-start">
						    	<q-btn-group flat>
							      <q-btn icon="fas fa-pencil-alt" flat size="sm" color="secondary" @click="editar(comentario.id_nota)"/>
							      
							      <q-btn icon="far fa-trash-alt" flat size="sm" color="negative" @click="confirmarBorrado(comentario.id_nota)"/>
							    </q-btn-group>
						    </q-item-side>
						  </q-item>
						  <q-item-separator inset />
						</template>

					</q-list>
				
			
		</q-card-main>
		</q-scroll-area>
	</q-card>

	<div class="row">
		<div class="col-sm-12">
			&nbsp;
		</div>
	</div>
	<div class="row">
		<div class="col-sm-10">
			<q-field   label="Comentario"  helper="Agregue un comentario"  :error="comentarioError"  error-label="Escriba su comentario">
				<q-input v-model="comment" type="textarea" :max-height="30" rows="3" />
			</q-field>
		</div>
		<div class="col-sm-2 text-right">
			<q-btn :icon="icono" color="secondary" @click="guardar" size="sm"/>
			<q-btn v-show="id_nota>0" icon="fas fa-undo" color="secondary" @click="cancelarEdicion" size="sm"/>
		</div>
	</div>
	

  </div>
</template>

<script>

import {QList, QListHeader, QItem, QItemMain, QItemSide, QBtnGroup, QItemTile, QItemSeparator, QCard, QCardMain, QScrollArea, QInput, QField, Dialog } from 'quasar';
import request from '../plugins/request';

export default {
  // name: 'ComponentName',
  data () {
    return {
    	comentarios: [],
    	comment:'',
    	comentarioError: false,
    	id_nota: 0,
    	icono: 'fas fa-comment-dots'
    }
  },
  components: {
  	QList, QListHeader, QItem, QItemMain, QItemSide, QBtnGroup, QItemTile, QItemSeparator, QCard, QCardMain, QScrollArea, QInput, QField, Dialog
  },
  props:{
  	paquete:0
  },
  
  methods: {
  	listar() {
  		const _self = this;
  		const payload = {
	        ruta: 'ComentariosController/listar',
	        data: {id_paquete: this.paquete}
	    }
	      
      	request.postRequest(payload,this.$store).then( (res) => {
	      	if(res.data.error == "0" && res.data.comentarios){
	      		this.comentarios = res.data.comentarios;
	      	}else{
	      		this.comentarios = [];
	      	}
	      	
	    }).catch(error =>_self.$store.dispatch("main/muestraError",error));
  	},
  	validar() {
  		if((this.comment).trim() == '') {
  			this.comentarioError = true;
  			return false;
  		}
  		return true;
  	},

  	guardar() {
  		if(this.id_nota == 0){
  			this.agregaComentario();
  		}else{
  			this.actualizar();
  		}
  	},

  	agregaComentario() {

  		const _self = this;
  		const payload = {
	        ruta: 'ComentariosController/insertar',
	        data: {id_paquete: this.paquete, nota:this.comment}
	    }

	    this.comentarioError = false;
	    if(this.validar())
	    {
	    	request.postRequest(payload,this.$store).then( (res) => {
		      	if(res.data.error == "0"){
		      		_self.listar();
		      		_self.comment = '';
		      		_self.$store.dispatch("main/muestraMsg",res);
		      	}
	      	
	      	}).catch(error =>_self.$store.dispatch("main/muestraError",error));
	    }
	      
  	},
  	editar(id_comentario) {
  		const _self = this;
  		const payload = {
	        ruta: 'ComentariosController/buscarNotaById',
	        data: {id_nota: id_comentario}
	    }

	    this.icono = 'fas fa-pencil-alt';
    	request.postRequest(payload,this.$store).then( (res) => {
	      	if(res.data.error == "0" && res.data.comentario !== null && res.data.comentario !== undefined){
	      		_self.comment = res.data.comentario;
	      		_self.id_nota = id_comentario;
	      	}
      	
      	}).catch(error =>_self.$store.dispatch("main/muestraError",error));
	    
  	},
  	actualizar() {
  		const _self = this;
  		const payload = {
	        ruta: 'ComentariosController/actualizar',
	        data: {id_nota: this.id_nota, nota:this.comment}
	    }

	   
	    	request.postRequest(payload,this.$store).then( (res) => {
		      	if(res.data.error == "0"){
		      		_self.listar();
		      	}
		      	_self.comment = '';
		      	_self.id_nota = 0;
		      	_self.icono = 'fas fa-comment-dots';
	      		_self.$store.dispatch("main/muestraMsg",res);
	      	}).catch(error =>{
	      		_self.id_nota = 0;
	      		_self.comment = '';
	      		_self.icono = 'fas fa-comment-dots';
	      		_self.$store.dispatch("main/muestraError",error)
	      	});
	    
  	},
  	cancelarEdicion() {
  		this.id_nota = 0;
  		this.comment = '';
  		this.icono = 'fas fa-comment-dots';
  	},
  	confirmarBorrado(id_comentario) {
  		const _self = this;
  		const payload = {
	        ruta: 'ComentariosController/borrar',
	        data: {id_nota: id_comentario}
	    }
  		Dialog.create({
			title: 'Confirme su Acción',
			message: '¿Esta seguro de borrar el Comentario?',
			color: 'primary',
			ok:'Si, proceder',
			cancel: 'Cancelar'
		}).then( ()=>{
			request.postRequest(payload,this.$store).then( function (res) {
				_self.$store.dispatch("main/muestraMsg",res);
		    	_self.listar();
			}).catch(error => _self.$store.dispatch("main/muestraError",error));
		}).catch(()=>{});
  	}
  },
  watch: {
  	paquete: function () {
  		this.listar();
  		this.comment = '';
  	}
  },
};

</script>

<style scoped>
q-scroll-area {
	
}
</style>
