<template>
  <div>
    <q-table
          :data="tableData"
          :columns="columns"
          row-key="id_grupo"
          selection="single"
          :selected.sync="selected"
          :filter="filter"
          color="secondary">

          <template slot="top-left" slot-scope="props">
            <q-search color="secondary" v-model="filter" clearable />
          </template>

          <template slot="top-right" slot-scope="props">
            <barra-abc @nuevo-clicked="nuevoGrupo" @editar-clicked="editarGrupo" @borrar-clicked="borrarGrupo"/>
          </template>

	      </q-table>

        <div class="row">
          <div class="col-sm-12">
            <q-field label="Seleccione el avatar" icon="fas fa-user-circle" :error="errorUploader" :error-label="errorLabel">
              <q-uploader ref="uploader" name='archivo' :url="url" auto-expand :additional-fields="additionalFields" :extensions="extensiones" clearable :hide-upload-button = "true" @uploaded="fileUploaded" @fail="uploadFailed"/>
            </q-field>
          </div>
        </div>

        <div class="row">
          <div class="col-sm-12">
            <q-field icon="fas fa-users" label="Nombre del Grupo" :error="errorNGrupo" error-label="Escriba el nombre del Grupo">
              <q-input v-model="ngrupo" />
            </q-field>
          </div>
        </div>

        <div class="row">
          <div class="col-sm-12">
            <q-field icon="fas fa-user-secret" label="Integrantes" :error="errorIntegrantes" error-label="Seleccione al menos un integrante para el grupo">
              <q-select v-model="integrantes" :options="equipo" chips multiple clearable />
            </q-field>
          </div>
        </div>

        <div class="row">
          <div class="col-sm-12 text-right">
            <q-btn :label="btnLabel" @click="guardar" />
          </div>
        </div>
      
  </div>
</template>

<script>
import {Notify, QField, QInput, QScrollArea,QToolbar,QModal,QModalLayout,QTable,QCheckbox,QUploader,QSearch,QSelect} from 'quasar'
import request from '../plugins/request';
import barraAbc from '../components/barra-abc';


export default {
  // name: 'ComponentName',
  data () {
    return {
      columns: 
    	[
        {
          name: 'avatar',
          required: true,
          label: 'AVATAR',
          align: 'left',
          field: 'avatar',
          sortable: true
        },
        {
          name: 'nombre',
          required: true,
          label: 'NOMBRE',
          align: 'left',
          field: 'nombre',
          sortable: true
        }
      ],
      tableData: [],
	    selected:[],
      filter: '',
      errorUploader: false,
      errorLabel: '',
      url: "index.php/ChatGruposController/guardarGrupo",
      extensiones: ".png, .jpg, .jpeg, .ico, .gif",
      ngrupo:'',
      errorNGrupo: false,
      integrantes: [],//seleccionados
      errorIntegrantes: false,
      equipo: [], //todos
      btnLabel: 'Agregar Grupo',
      accion: 'agregar'
    }
  },
  components: {
    Notify, QField, QInput, QScrollArea,QToolbar,QModal,QModalLayout,QTable,QCheckbox,QUploader,QSearch,barraAbc,QSelect
  },
  mounted() {
    this.listarPersonas();
  },
  methods: {
    listarPersonas() {
      const _self = this;
	  	const payload = {
        ruta: 'ChatGruposController/getPersonasDeProyecto',
        data: {id_proyecto:this.$store.getters['main/getProyectoActual'].id_proyecto}
	  	}

	  	request.postRequest(payload,this.$store).then(res=>{
        res.data.map(function (current,index,all) { 
          if(current.avatar == "fas fa-user-circle") {
            current.icon = current.avatar;
          }
        });
        this.equipo = res.data;

	  	}).catch(error=>{_self.$store.dispatch("main/muestraError",error)});
    },
    nuevoGrupo() {
      this.$refs.uploader.reset();
      this.ngrupo = '';
      this.integrantes = [];
    },
    validar() {
      this.errorIntegrantes = false;
      this.errorNGrupo = false;
      if(this.ngrupo.trim() == "") {
        this.errorNGrupo = true;
        return false;
      }
      if(this.integrantes.length == 0) {
        this.errorIntegrantes = true;
        return false;
      }
      return true;
    },
    guardar() {
      if(this.validar()) {
        if(this.$refs.uploader.files.length == 0) {

          const _self = this;
          const payload = {
            ruta: 'ChatGruposController/guardarGrupo',
            data: {
              id_proyecto: this.$store.getters['main/getProyectoActual'].id_proyecto,
              nombre: this.ngrupo,
              integrantes : this.integrantes
            }
          }

          request.postRequest(payload,this.$store).then(res=>{
            console.log(res.data);
          }).catch(error=>{_self.$store.dispatch("main/muestraError",error)});

        } else {
          this.$refs.uploader.upload();
        }
        
      }
    },
    editarGrupo() {

    },
    borrarGrupo() {

    },
    fileUploaded(file, xhr) {
      
      let response = JSON.parse(xhr.response);
      console.log(response);

      this.$refs.uploader.reset();
    },
    uploadFailed(file, xhr){
      this.$refs.uploader.reset();
    }
  },
  computed: {
    additionalFields () {
  		return [
        { 'name': 'id_proyecto', 'value': this.$store.getters['main/getProyectoActual'].id_proyecto },
  			{ 'name':'nombre','value':this.ngrupo	},
  			{ 'name':'integrantes','value':this.integrantes }
  		]
	}
  }
};
</script>

<style scoped>
.row {
  margin-top: 10px;
}
</style>
