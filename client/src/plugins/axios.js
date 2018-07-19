import axios from 'axios'

export default ({ Vue }) => {
	axios.defaults.baseURL = 'index.php/'; //for devserver
	//axios.defaults.baseURL = 'http://localhost/pmt/index.php/'; //for apache
	Vue.prototype.$axios = axios
}
