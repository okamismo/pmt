import Vue from 'vue'
import Vuex from 'vuex'
import createPersistedState from 'vuex-persistedstate'
import * as Cookies from 'js-cookie'

// we first import the module
import main from './main'

Vue.use(Vuex)

const store = new Vuex.Store({
  modules: {
    // then we reference it
    main
  },
  plugins: [
    createPersistedState({
      getState: (key) => Cookies.getJSON(key),
      setState: (key, state) => Cookies.set(key, state)
    })
  ]
  
})

// if we want some HMR magic for it, we handle
// the hot update like below. Notice we guard this
// code with "process.env.DEV" -- so this doesn't
// get into our production build (and it shouldn't).
if (process.env.DEV && module.hot) {
  module.hot.accept(['./main'], () => {
    const newShowcase = require('./main').default
    store.hotUpdate({ modules: { main: newShowcase } })
  })
}

export default store