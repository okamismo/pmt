<template>
  <q-page padding>
    <div class="row">
      <div class="col-sm-12">
        <q-card>
          <q-card-title>
            Personalize su Avatar
          </q-card-title>
          <q-card-separator />
          <q-card-actions class="justify-center">
            
          </q-card-actions>
          <q-card-main>
            
              <template v-if="avatar=='fas fa-user-circle'">
                <div class="row">
                  <div class="col-sm-12 text-center">
                    <q-icon  :name="avatar" size="2rem" />
                  </div>
                </div>
              </template>
              
              <template v-else>
                <q-item>
                  <q-item-main class="row">
                    <q-item-tile avatar class="col-sm-12 text-center">
                      <img id="miAvatar" :src="avatar">
                    </q-item-tile>
                  </q-item-main>
                </q-item>
              </template>
            

            <div class="row">
              <div class="col-sm-12">
                
                <q-field label="Seleccione una imagen" icon="fas fa-file-image" :error="errorUploader"          :error-label="errorLabel"  helper="Extensiones: jpg, jpeg, png o gif; archivos menores a 1MB">
                  <q-uploader ref="uploader" name='archivo' :url="url" auto-expand :extensions="extensiones" clearable :hide-upload-button="true" @uploaded="fileUploaded" @fail="uploadFailed"/>
                </q-field>

              </div>
            </div>
            <div class="row">
              <div class="col-sm-12 text-right">
                <q-btn icon="fas fa-upload" color="secondary" size="sm" @click="validarSubida"/>
              </div>
            </div>
            

          </q-card-main>
        </q-card>
      </div>
    </div>
  </q-page>
</template>

<script>
import request from '../plugins/request';

import { QCard, QCardTitle, QCardMain, QCardActions,QCardSeparator, QField, Notify, QItemTile, QUploader } from 'quasar';
import varchivos from '../plugins/fileValidations';

export default {
  data() {
    return {
      errorUploader:false,
      errorLabel:"Seleccione su avatar",
      url:"index.php/PersonasController/guardarAvatar",
      extensiones: ".jpg,.jpeg,.png,.gif",
      maxSize:1
    }
  },
  computed: {
    avatar: { 
      get() { return this.$store.getters["main/getAvatar"]; },
      set(avatar) { this.$store.commit("main/setAvatar",avatar); }
    }
  },
  components:{
  	QCard, QCardTitle, QCardMain, QCardActions,QCardSeparator, QField, Notify, QItemTile, QUploader
  },
  mounted() {
    this.avatar = this.$store.getters["main/getAvatar"];
  },
  methods: {
    validarSubida() {
  		
  		this.errorUploader = false;
  		
  		
  		if(this.$refs.uploader.files.length == 0){
  			this.errorUploader = true;
  			this.errorLabel = "Debe seleccionar un archivo";
  			return false;
  		}else{
  			const allowed = this.extensiones.split(",");
  			const ext = "."+varchivos.getFileExtension(this.$refs.uploader.files[0].name);

  			if(ext == '.' || !allowed.includes(ext)){
  				this.errorUploader = true;
  				this.errorLabel = "Las extensiones permitidas son: "+this.extensiones;
  				return false;
  			}
  			if(!varchivos.isFileSizeCorrect(this.$refs.uploader.files[0].size,this.maxSize)) {
  				this.errorUploader = true;
  				this.errorLabel = "El archivo debe medir menos de "+this.maxSize+" MB";
  				return false;
  			}
  		}

  		this.$refs.uploader.upload();
  	},
    fileUploaded(file, xhr) {
      let response = JSON.parse(xhr.response)
  		if(response.error == "0"){
        this.$store.commit('main/setAvatar', response.avatar);
  			Notify.create({
          message: response.msg,
          timeout: 2000,
          type: 'info',
          position: 'top-right'
        });
        
  		}else{
        Notify.create({
          message: response.msg,
          timeout: 2000,
          type: 'negative',
          position: 'top-right'
        });
      }
  		this.$refs.uploader.reset();
    },
    uploadFailed(file, xhr){
      this.$refs.uploader.reset();
      Notify.create({
        message: "Error al intentar guardar",
        timeout: 2000,
        type: 'negative',
        position: 'top-right'
      })
    }
  }
};
</script>

<style scoped>
  .row{
    margin-top:10px;
  }
</style>
