/* eslint-disable no-new */
import Vue from 'vue'
import App from './App'
import router from './router'
import store from './store'
import Api from './utils/api'
import Vant from 'vant';
import 'vant/lib/index.css';
import { Lazyload } from 'vant';
import {post, get} from './utils/Http';
import common from './utils/common';
import BackToTop from 'vue-backtotop'
window.Hls = require('hls.js');

Vue.use(Vant);
Vue.use(BackToTop);
Vue.use(Lazyload, {
    // loading: loadingImg,
    throttleWait: 200
});

Vue.config.productionTip = false
Vue.prototype.API = Api;
Vue.prototype.$post = post;
Vue.prototype.$get = get;


new Vue({
  el: '#app',
  router,
  store,
  components: { App },
  template: '<App/>'
})
