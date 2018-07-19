import request from '../../plugins/request'
import {Notify} from 'quasar'

/*
	hacemos 'deconstruction' del context para acceder a los metodos y objetos
*/
export const cerrarSesion = ({ commit }) => {
	
	return request.postRequest( {
		ruta: "LoginController/cerrarSesion",
		data: '',
		callback: function (response){
			commit("closeLeftDrawer")
			commit("setLogInStatus",false)
			commit("setProyectoActual",null)
			commit("setProyectos",null)
			commit("setBaseUrl","")
			commit("setPerfil",undefined)
		}
	});
}

export const getMenu = (context) => {
	
	request.postRequest( {
		ruta: "LoginController/getMenu",
		data: '',
		callback: function (response){
			context.commit("setMenus",response.data.menu.menus)
			context.commit("setEnlaces",response.data.menu.enlaces)
		}

	},context);	
}

export const muestraMsg = (context,res) => {
	const _tipo = (res.data.error == "0")?"positive":"negative";
	const _titulo = (res.data.error == "0")?"Atención":"Error";
	const _icono = (res.data.error == "0")?"fas fa-info-circle":"fas fa-exclamation-triangle";

	Notify.create({
      message: _titulo+" : "+res.data.msg,
      timeout: 2000,
      type: _tipo,
      position: 'top',
      icon:_icono
    });
}

export const muestraError = (context,error) => {
	
	Notify.create({
      message: error.name+" : "+error.message,
      timeout: 5000,
      type: 'negative',
      position: 'top',
      icon:'fas fa-exclamation-triangle'
    });
}

export const getProyectos = (context) => {
	return request.postRequest( {ruta: "LoginController/getProyectos"},context).then(response=>{
		if(response.data.length > 0){
			context.commit("setProyectos",response.data)
			context.commit("setProyectoActual",response.data[0].id_proyecto)
		}else{
			context.commit("setProyectoActual",null)
			context.commit("setProyectos",null)
		}
		
	}).catch(error=>console.log(error));
}

export const getAvatar = (context) => {
	return request.postRequest( {ruta: "LoginController/getAvatar"},context).then(response=>{
		response.data.src = (response.data.src).trim();
		if(response.data){
			context.commit("setAvatar",response.data.src)
		}
	}).catch(error=>console.log(error));
}