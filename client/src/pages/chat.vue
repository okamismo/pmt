<template>
  <q-page padding class="container">
    <div class="row">
      <q-toolbar color="green-5" >
        <div class="col-sm-4">
          <q-search v-model="busqueda" placeholder="Buscar o agregar chat nuevo" inverted color="green-5"  clearable />
        </div>
        <div class="col-sm-7">
          <q-item>
                <q-item-side icon="fas fa-user-circle" />
                <span class="text-white">Nombre Usuario</span>
          </q-item>
        </div>
        <div class="col-sm-1">
          <q-btn flat round dense icon="fas fa-users" @click="agregarGrupo" >
            <q-tooltip anchor="center left" self="top left">
              Agregar Grupo
            </q-tooltip>
          </q-btn>
        </div>
      </q-toolbar>
        
    </div>
    <div class="row">
      <div class="col-sm-4 borde" style="height:62vh; overflow-y:auto;">
        <!-- <q-scroll-area style="height: 65vh;"> -->
          <q-list highlight link separator>
            
            <q-item v-for="chat in chats" :key="chat.id_persona">
              <template v-if="chat.avatar=='fas fa-user-circle'">
                <q-item-side>
                  <q-icon :name="chat.avatar" size="2rem" />
                </q-item-side>
              </template>
              <template v-else>
                <q-item-side :avatar="chat.avatar" />
              </template>
              <q-item-main>{{chat.paterno}} {{chat.nombre}}</q-item-main>
              <q-item-side right stamp="10/12/2018" />
            </q-item>

          </q-list>
          <!-- </q-scroll-area> -->
      </div><!-- lista de chats -->
      <div class="col-sm-8">
        <q-list>
          <q-scroll-area style="height: 60vh; width:90">
            <div class="q-pr-md">
            <q-chat-message
            name="Isaac"
            avatar="statics/avatars/0735258001532384033_WxkNYTcFDb.png"
            :text="['Hello man!']"
            stamp="4 minutes ago"
            bg-color="purple-11"
            sent
          />

          <q-chat-message
            name="Peter"
            avatar="statics/avatars/peter.jpg"
            :text="['hey, if you type in your pw', 'it will show as stars']"
            stamp="7 minutes ago"
            bg-color="orange"
          />
          <q-chat-message
            name="Isaac"
            avatar="statics/avatars/0735258001532384033_WxkNYTcFDb.png"
            :text="['Hello man!']"
            stamp="4 minutes ago"
            sent
          />

          <q-chat-message
            name="Peter"
            avatar="statics/avatars/peter.jpg"
            :text="['hey, if you type in your pw', 'it will show as stars']"
            stamp="7 minutes ago"
          />
          <q-chat-message
            name="Isaac"
            avatar="statics/avatars/0735258001532384033_WxkNYTcFDb.png"
            :text="['Hello man!']"
            stamp="4 minutes ago"
            sent
          />

          <q-chat-message
            name="Peter"
            avatar="statics/avatars/peter.jpg"
            :text="['hey, if you type in your pw', 'it will show as stars']"
            stamp="7 minutes ago"
          />
          </div>
          </q-scroll-area> 
        </q-list>
      </div><!-- area de mensajes -->
    </div>
    <div class="row">
      <div class="col-sm-12">
        <q-field :count="255">
          <q-input v-model="msg" placeholder="Escriba aqui su mensaje" inverted color="green-5" clearable :after="afterBoton" @keyup.enter="enviarMsg"/>
        </q-field>
      </div>
    </div>

  <q-modal ref="modal" v-model="opened" :content-css="{minWidth: '80vw', minHeight: '80vh'}">
	  <q-modal-layout>
	    
	    <q-toolbar slot="header">
	      <q-btn flat round dense v-close-overlay icon="keyboard_arrow_left"/>
	      <q-toolbar-title>
	        Grupos
	      </q-toolbar-title>
	    </q-toolbar>

	    <div class="layout-padding">
        <chat-grupos />
	    </div>	
        

      <q-toolbar slot="footer" class="justify-end">
	      <q-btn class="q-mr-sm" glossy color="secondary" rounded label="Cerrar" @click="opened=false"/>
	    </q-toolbar>
    </q-modal-layout>
  </q-modal>

  </q-page>
</template>

<script>
import {Notify, QField, QInput, QSearch, QChatMessage, QList,QItem,QItemSide,QItemMain, QItemTile, QItemSeparator, QScrollArea,QToolbar,QModal,QModalLayout,QTable,QCheckbox,QUploader} from 'quasar'
import request from '../plugins/request';
import chatGrupos from '../components/chat-grupos';

export default {
  data() {
    return {
      msg:'',
      busqueda:'',
      afterBoton:[
        {
          icon:'fas fa-chevron-circle-right',
          handler: this.enviarMsg
        }
      ],
      chats: [],
      opened:false
    }
  },
  components:{
    Notify,  QField, QInput, QSearch, QChatMessage,QList,QItem,QItemSide,QItemMain,QItemTile,QItemSeparator, QScrollArea,QToolbar,QModal,QModalLayout,QTable,QCheckbox,chatGrupos
  },
  mounted() {
    this.listarChats();
  },
  methods: {
    enviarMsg() {
      if(this.msg.trim() == "") {
        return;
      }
      this.msg = '';
    },
    listarChats() {
      const _self = this;
	  	const payload = {
        ruta: 'ChatController/listarChats',
        data: {id_proyecto:this.$store.getters['main/getProyectoActual'].id_proyecto}
	  	}

	  	request.postRequest(payload,this.$store).then(res=>{
	  		this.chats = res.data;
	  	}).catch(error=>{_self.$store.dispatch("main/muestraError",error)});
    },
    agregarGrupo() {
      this.opened = true;
    }
    
  }
}
</script>

<style scoped>
 .fondo {
   background-color: #26a69a;
   color: white;
 }
 .borde {
   border: 1px solid lightgrey;
 }
</style>
