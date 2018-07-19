<template>
  <div>
  	<q-btn-group rounded>
	  <q-btn rounded label="Nuevo" icon="fas fa-plus-circle" color="secondary" @click="nuevoClicked"/>
	  <q-btn rounded label="Editar" icon="fas fa-pencil-alt" color="secondary" @click="editarClicked"/>
	  <q-btn rounded label="Borrar" icon="far fa-trash-alt" color="secondary" @click="borrarClicked"/>
	</q-btn-group>
  </div>
</template>

<script>
import {QBtnGroup} from 'quasar'

export default {
  // name: 'ComponentName',
  data () {
    return {}
  },

  methods: {
  	nuevoClicked () {
  		this.$emit("nuevo-clicked")
  	},
  	editarClicked () {
  		this.$emit("editar-clicked")
  	},
  	borrarClicked () {
  		this.$emit("borrar-clicked")
  	}
  },

  components: {
  	QBtnGroup
  }
};
</script>

<style>
  .q-card-actions-horiz .q-btn + .q-btn {
      margin-left: 0px;
  }
</style>
