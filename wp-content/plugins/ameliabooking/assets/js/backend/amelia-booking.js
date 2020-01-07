import Vue from 'vue'
import VueRouter from 'vue-router'
import axios from 'axios'
import router from './routes'
import ElementUI from 'element-ui'
import locale from 'element-ui/lib/locale/lang/en'
import moment from 'moment'
import VueMomentJS from 'vue-momentjs'
import VCalendar from 'v-calendar'
import 'idempotent-babel-polyfill'
import DialogLite from '../../views/backend/lite/DialogLite.vue'
import BlockLite from '../../views/backend/lite/BlockLite.vue'
import PopLite from '../../views/backend/lite/PopLite.vue'
import { popLiteProps } from '../backend/props/popLiteProps'
import dateMixin from '../../js/common/mixins/dateMixin'

Vue.prototype.$http = axios
Vue.prototype.$http.defaults.headers.common = {
  'X-Requested-With': 'XMLHttpRequest'
}

// eslint-disable-next-line no-undef, camelcase
__webpack_public_path__ = window.wpAmeliaPluginURL + '/public/'

Vue.use(VueRouter)
Vue.use(VueMomentJS, moment)
Vue.use(ElementUI, {locale})
Vue.use(VCalendar, {
  firstDayOfWeek: window.wpAmeliaSettings.wordpress.startOfWeek + 1,
  locale: window.localeLanguage.replace('_', '-')
})
Vue.component('DialogLite', DialogLite)
Vue.component('PopLite', PopLite)
Vue.component('BlockLite', BlockLite)

// eslint-disable-next-line no-new
new Vue({
  el: '#amelia-app-backend',

  mixins: [dateMixin],

  router,

  data: {
    getUploadsAmeliaUrl: window.wpAmeliaUploadsAmeliaURL,
    useUploadsAmeliaPath: window.wpAmeliaUseUploadsAmeliaPath,
    getAjaxUrl: window.wpAmeliaPluginAjaxURL,
    getUrl: window.wpAmeliaPluginURL,
    getStoreUrl: window.wpAmeliaPluginStoreURL,
    getSiteUrl: window.wpAmeliaSiteURL,
    labels: window.wpAmeliaLabels,
    settings: window.wpAmeliaSettings,
    locale: window.localeLanguage,
    popLiteProps: popLiteProps,
    isLite: AMELIA_LITE_VERSION,
    dialogLite: false
  },

  mounted () {
    moment.locale(window.localeLanguage)
    if (window.localeLanguage === 'ar') {
      this.reformatArabicNumbers()
    }
  }
})

// eslint-disable-next-line no-undef
router.push({name: menuPage})

window.onpopstate = function () {
  history.back()
}
