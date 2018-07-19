
export const setLogInStatus = (state,status) => { 
	if(status === undefined || status === null)
		return false;
	state.logIn = status
}

export const toggleLeftDrawer = (state) => { 
	state.leftDrawer = ! state.leftDrawer
}

export const closeLeftDrawer = (state) => {
	state.leftDrawer = false
}

export const setMenus = (state,menus) => { 
	state.menus = menus
}

export const setEnlaces = (state,enlaces) => { 
	state.enlaces = enlaces
}

export const setProyectos = (state,proyectos) => { 
	state.proyectos = proyectos
}

export const setProyectoActual = (state,proyecto) => { 
	if(proyecto === null || proyecto === undefined){
		state.proyectoActual = null;
	}else{
		const found = state.proyectos.find((p)=>{
			return (p.id_proyecto == proyecto)
		})
		state.proyectoActual = found;
	}
	
}

export const setAvatar = (state,src) => { 
	state.avatar = src;//(src == "")?src:"~"+src;
}

export const setBaseUrl = (state,url) => { 
	state.baseUrl = url;
}

export const setPerfil = (state,perfil) => { 
	state.perfil = parseInt(perfil);
}