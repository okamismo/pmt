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
                  <q-uploader ref="uploader" name='archivo' :url="url" auto-expand :extensions="extensiones" clearable @uploaded="fileUploaded" @fail="uploadFailed"/>
                </q-field>

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

export default {
  data() {
    return {
      errorUploader:false,
      errorLabel:"Seleccione su avatar",
      url:"index.php/PersonasController/guardarAvatar",
      extensiones: ".jpg,.jpeg,.png,.gif"
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
    fileUploaded(file, xhr) {
      let response = JSON.parse(xhr.response)
  		if(response.error == "0"){
        this.$store.commit('main/setAvatar', response.avatar);
        this.$emit("gotAvatar");
  			Notify.create({
          message: "Archivo guardado",
          timeout: 2000,
          type: 'info',
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
