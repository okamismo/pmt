<template>
  <q-page padding>
    <monitor-tareas :proyecto="id_proyecto"/>
  </q-page>
</template>

<script>
import monitorTareas from '../components/monitor-tareas';

export default {
   data () {
    return {
    	id_proyecto:0
    }  
  },
  mounted() {
  	this.id_proyecto = this.$store.getters['main/getProyectoActual'].id_proyecto;
  },
  components: {  
    monitorTareas
  },
  methods: {
    
  }
};

</script>

<style>
</style>
