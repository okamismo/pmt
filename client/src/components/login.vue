<template>
  
  	<q-page class="centrar" padding>
			

			
	  	<!-- window-height -->
	    <div class="row justify-center items-center">
	    	<q-card class="shadow-10">
			  <q-card-title>
			    Bienvenido, por favor registre su entrada
			  </q-card-title>
			  <q-card-separator />
			  <q-card-main>
			    
					<q-field
					  icon="fas fa-user-secret"
					  label="Usuario"
					  helper="Escriba su nombre de usuario"
					  :error="usrHasError"
					  error-label="El campo usuario es obligatorio">

					  <q-input v-model="usr" />
					</q-field>

					<q-field
					  icon="fas fa-key"
					  label="Contraseña"
					  helper="Escriba su contraseña"
					  :error="passHasError"
					  error-label="El campo contraseña es obligatorio">

					  <q-input v-model="pass" type="password" @keyup.enter="validarLogin" />
					</q-field>

					<q-card-actions align="end">
						<q-btn color="secondary" label="Entrar" @click="validarLogin" />
					</q-card-actions>
					

			  </q-card-main>
			</q-card>
	    </div>
		
  	</q-page>
  
</template>

<script>
import {QCard, QCardTitle, QCardSeparator, QCardMain, QField, QInput, QCardActions, Notify} from 'quasar'
import request from '../plugins/request'
import router from '../router/index'

export default {
   name: 'login',
  data () {
    return {
    	usr: '',
    	usrHasError:false,
    	pass: '',
    	passHasError: false
    }
  },
  components: {
   		QCard, QCardTitle, QCardSeparator, QCardMain, QField, QInput, QCardActions
  },
  methods: {
  	resetCtrlErrors() {
  		this.usrHasError = false
  		this.passHasError = false
  	},

  	validarLogin() 
    {
      let _self = this
  		this.resetCtrlErrors()
  		this.usr = this.usr.trim()
  		this.pass = this.pass.trim()

  		if(this.usr == ''){
  			this.usrHasError = true
  			return
  		}

  		if(this.pass == ''){
  			this.passHasError = true
  			return
  		}

      const payload = {
        ruta: 'LoginController/validarCredenciales',
        data: {usr: this.usr, pass: this.pass} 
      }
      
      request.postRequest(payload,this.$store).then( function(response){
        if (response.data !== undefined && response.data !== null) {
            if(response.data.error == "1"){
              Notify.create({
                message: 'Ocurrio un Error, intente mas tarde...',
                timeout: 5000,
                type: 'negative',
                position: 'top-right',
                icon:'fas fa-exclamation-triangle'
              })
            }
            else if(response.data.valido == "1"){
              _self.$store.commit('main/setBaseUrl',response.data.baseUrl);
              _self.$store.commit('main/setPerfil',response.data.id_perfil);
              _self.$store.commit('main/setLogInStatus',true);
              //_self.$store.commit('main/toggleLeftDrawer');
              
              router.push('home');
            }
            else if(response.data.valido == "0"){
              Notify.create({
                message: 'El usuario y/o contraseña son incorrectos',
                timeout: 2000,
                type: 'warning',
                position: 'top-right',
                icon:'fas fa-exclamation-triangle'
              })
            }
            return;
        }
      }).catch( (error)=>console.log(error));

  	}//validarLogin
  }
}
</script>

<style scoped>
.centrar {
	display: flex;
	justify-content: center;
	align-items: center;
}
</style>
