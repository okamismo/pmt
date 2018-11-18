import mainLayout from 'layouts/mainLayout'
import login from 'components/login'
import home from 'pages/home'
import perfiles from 'pages/perfiles'
import pantallas from 'pages/pantallas'
import perfilesPantallas from 'pages/perfiles-pantallas'
import personas from 'pages/personas'
import proyectos from 'pages/proyectos'
import roles from 'pages/roles'
import proyectosPersonas from 'pages/proyectos-personas'
import edt from 'pages/edt'
import categoriasDocumentos from 'pages/categorias-documentos'
import tareas from 'pages/tareas'
import avatar from 'pages/avatar'
import chat from 'pages/chat'

import store from '../store/index'

export default [
  {
    path: '/pmt/',
    component: mainLayout,
    alias:'/',
    children: 
    [
      { 
        path: '', 
        name: 'login',
        component: login,
        alias: 'login/',
        beforeEnter: (to, from, next) => {
          if(store.getters["main/getLogInState"]){
            next({path:'pmt/home'})
          }
          next();
        }
      },
      { path: 'home/', component: home, name: 'home'},
      { path: 'perfiles/', component: perfiles, name:'perfiles'},
      { path: 'pantallas/', component: pantallas, name: 'pantallas'},
      { path: 'perfiles-pantallas/', component: perfilesPantallas, name: 'perfiles-pantallas'},
      { path: 'personas/', component: personas, name: 'personas'},
      { path: 'proyectos/', component: proyectos, name: 'proyectos'},
      { path: 'roles/', component: roles, name: 'roles'},
      { path: 'proyectos-personas/', component: proyectosPersonas, name: 'proyectos-personas'},
      { path: 'edt/', component: edt, name: 'edt'},
      { path: 'categoriasDocumentos/', component: categoriasDocumentos, name: 'categoriasDocumentos'},
      { path: 'tareas/', component: tareas, name: 'tareas'},
      { path: 'avatar/', component: avatar, name: 'avatar'},
      { path: 'chat/', component: chat, name: 'chat'}
    ]
    
  },

  { // Always leave this as last one
    path: '*',
    component: () => import('pages/404')
  }
]