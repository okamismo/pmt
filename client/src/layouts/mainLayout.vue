<template>
  <q-layout ref="mainLayout" view="hHh lpr fFf"> <!-- Be sure to play with the Layout demo on docs -->

    <!-- (Optional) The Header -->
    <q-layout-header>
      <q-toolbar>
        <q-btn v-if="logInStatus"
          flat
          round
          dense
          icon="fas fa-bars"
          @click="toggleLeftDrawer"
        />
        <q-toolbar-title>
          PROJECT MANAGEMENT TOOLKIT 
          <span slot="subtitle">PMT</span>
        </q-toolbar-title>
  
        <q-select v-if="logInStatus" v-model="proyectoActual" :options="proyectos" inverted class="no-shadow"/>

        <template v-if="logInStatus">
          <q-btn round flat @click="miAvatar">
            <q-icon v-if="avatar=='fas fa-user-circle'" :name="avatar" size="1.5rem" />
            <template v-else>
              <q-item dense @click="miAvatar">
                <q-item-side :avatar="avatar"></q-item-side>
              </q-item>
            </template>
          </q-btn>
        </template>

        <q-btn v-if="logInStatus" flat round dense icon="fas fa-power-off" @click="cerrarSesion">
          <q-tooltip anchor="center left" self="top left">
            Cerrar Session
          </q-tooltip>
        </q-btn>

        

      </q-toolbar>
    </q-layout-header>

    <!-- (Optional) The Footer -->
    <q-layout-footer>
      <q-toolbar>
        <q-toolbar-title class="text-right">
          Peter's Corporation
          <span slot="subtitle">All rights reserved</span>
        </q-toolbar-title>
      </q-toolbar>
      
    </q-layout-footer>

    <!-- (Optional) A Drawer; you can add one more with side="right" or change this one's side -->
    <q-layout-drawer side="left" overlay v-model="drawerStatus">
      <div class="q-mt-sm"></div>

      <q-item v-for="enlace in enlaces" :key="enlace.id_pantalla" link dense :to="enlace.uri">
        <q-item-side v-if="enlace.icono" :icon="enlace.icono"/>
        <q-item-main :label="enlace.nombre_menu" />
      </q-item>

      <q-list v-for="item in menus" :key="item.id_pantalla" separator>
        <q-collapsible :icon="item.icono" collapse-icon="fas fa-chevron-circle-down" :label="item.nombre_menu">
          <q-item v-for="sub in item.hijos" :key="sub.id_pantalla" link dense :to="sub.uri">
            <q-item-main :label="sub.nombre_menu" />
          </q-item>
        </q-collapsible>
      </q-list>

    </q-layout-drawer>

    <q-page-container v-on:got-avatar="gotAvatar">
      <!-- This is where pages get injected -->
      
        <router-view />
      

    </q-page-container>

  </q-layout>
</template>

<script>
import router from '../router/index'
import {QItemTile,QCollapsible, QSelect} from 'quasar'

export default {
  // name: 'LayoutName',
  data () {
    return {
      //avatar:''
    }
  },

  components: {
    QItemTile,QCollapsible,QSelect
  },

  mounted() {
    
  },

  computed: {
    avatar: { 
      get() { return this.$store.getters["main/getAvatar"]; },
      set(avatar) { this.$store.commit("main/setAvatar",avatar); }
    },
    logInStatus: {
      get () { return this.$store.getters['main/getLogInState']; },
      set ( status ) { this.$store.commit('main/setLogInStatus', status); }
    },
    drawerStatus: {
      get () { return this.$store.getters['main/getLeftDrawerState']; },
      set () { this.$store.commit('main/toggleLeftDrawer'); }
    },

    menus: {
      get() { return this.$store.getters['main/getMenus']; }
    },

    enlaces: {
      get() { return this.$store.getters['main/getEnlaces']; }
    },
    proyectos: {
      get() { 
        let p = this.$store.getters['main/getProyectos'];
        if(p !== null && p !== undefined){
          let opts = p.map(val=>{
            return {
              value: val.id_proyecto,
              label: val.nombre
            }
          });
          return opts;
        }else{
          return [];
        }
        
      }
    },
    proyectoActual: {
      get() { 
        let pa = this.$store.getters['main/getProyectoActual']; 
        if(pa !== null && pa !== undefined){
          return pa.id_proyecto;      
        }
      },
      set(id_proyecto) {
        this.$store.commit('main/setProyectoActual', id_proyecto);
      }
    }

  },
  methods: {
    toggleLeftDrawer () {
      this.$store.commit('main/toggleLeftDrawer');
    },

    cerrarSesion () { 
      this.$store.dispatch("main/cerrarSesion").then( ()=>{
        router.push('login')
      }).catch( ()=>console.log("No fue posible cerrar session"));
    },

    gotAvatar(){
      this.avatar = this.$store.getters["main/getAvatar"];
    },
    miAvatar() {
      router.push('avatar');
    }
  }
};
</script>

<style scoped>


</style>
