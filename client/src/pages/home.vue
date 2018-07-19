<template>
  <q-page padding>
    
  </q-page>
</template>

<script>


export default {
  data () {
    return {
    	
    }  
  },
  components: {  
    
  },
  methods: {
    
  },

  mounted: function () {
  	this.$store.dispatch("main/getMenu")
    this.$store.dispatch("main/getProyectos").then(()=>{
    }).catch(error=>console.log(error));
    this.$store.dispatch("main/getAvatar").then(()=>{
      this.$parent.$emit("got-avatar");
    }).catch(error=>console.log(error));
  }
};
</script>

<style>
</style>
