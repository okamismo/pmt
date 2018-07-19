import axios from 'axios'
import router from '../router/index'

export default {

	postRequest(payload,state) 
	{
		const ctx = this
		if(payload.ruta === undefined || payload.ruta === null || payload.ruta === ""){
			return 'ruta is undefined'
		}
		payload.data = payload.data || ''
		
		return axios.post(payload.ruta,{data:payload.data}).then(function (response) {

    		if (response !== undefined && response !== null){

    			if(response.data.sesionTerminada !== undefined && response.data.sesionTerminada == "1"){
    				return Promise.reject("Sesion Terminada");
    			}else if(response.data.accesoNoAutorizado !== undefined && response.data.accesoNoAutorizado == "1"){
    				return Promise.reject("Acceso No Autorizado");
    			}

	          	if(typeof payload.callback === "function"){
	          		payload.callback.call(ctx,response);
	          	}

	          	return response;
	        }
	    })
        .catch((error) => {
        	
        	if(error === "Acceso No Autorizado"){
        		router.push({ path: '/pmt/home' })
        	}else if(error === "Sesion Terminada"){
        		state.dispatch("main/cerrarSesion").then( ()=>{
		        router.push({path: '/pmt/login'})
		      }).catch( ()=>console.log("No fue posible cerrar session"));
        	}else{

        	}
        	
        	throw new Error(error);
        })
		
	}

}