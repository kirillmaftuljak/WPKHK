/* eslint-disable no-undef */
import Vue from 'vue'
import axios from 'axios'
import ElementUI from 'element-ui'
import locale from 'element-ui/lib/locale/lang/en'
import VCalendar from 'v-calendar'
import 'idempotent-babel-polyfill'
import moment from 'moment/moment'
import Lightbox from 'vue-pure-lightbox'
import dateMixin from '../../js/common/mixins/dateMixin'

// eslint-disable-next-line no-undef, camelcase
__webpack_public_path__ = wpAmeliaUrls.wpAmeliaPluginURL + '/public/'

let components = null

const Booking = () => import(/* webpackChunkName: "booking" */ '../../views/frontend/booking/Booking.vue')
const Events = () => import(/* webpackChunkName: "service" */ '../../views/frontend/events/Events.vue')
components = {Booking, Events}

Vue.prototype.$http = axios
Vue.prototype.$http.defaults.headers.common = {
  'X-Requested-With': 'XMLHttpRequest'
}

Vue.use(ElementUI, {locale})
Vue.use(VCalendar, {
  firstDayOfWeek: window.wpAmeliaSettings.wordpress.startOfWeek + 1,
  locale: window.localeLanguage.replace('_', '-')
})
Vue.use(Lightbox)

function ameliaLoading () {
  // eslint-disable-next-line no-new
  var ameliaContainers = document.getElementsByClassName('amelia-frontend')
  var ameliaVueInstances = []

  for (var i = 0; i < ameliaContainers.length; i++) {
    ameliaVueInstances.push(
      new Vue({
        el: ameliaContainers[i],

        mixins: [dateMixin],

        components: components,

        data: {
          getAjaxUrl: wpAmeliaUrls.wpAmeliaPluginAjaxURL,
          getUrl: wpAmeliaUrls.wpAmeliaPluginURL,
          isLite: AMELIA_LITE_VERSION,
          labels: window.wpAmeliaLabels,
          settings: window.wpAmeliaSettings,
          clonedSettings: JSON.parse(JSON.stringify(window.wpAmeliaSettings)),
          shortcodeData: {
            enabled: false,
            booking: {
              category: '',
              service: '',
              employee: '',
              location: '',
              eventId: null,
              eventTag: null,
              eventRecurring: null
            },
            search: {
              today: ''
            },
            hasCategoryShortcode: '',
            hasBookingShortcode: '',
            counter: ''
          },
          fileUploadExtensions: fileUploadExtensions
        },

        mounted () {
          moment.locale(window.localeLanguage)
          if (window.localeLanguage === 'ar') {
            this.reformatArabicNumbers()
          }

          let bookingData = typeof window.bookingEntitiesIds !== 'undefined' ? window.bookingEntitiesIds.shift() : null

          this.shortcodeData.booking = bookingData ? {
            category: 'category' in bookingData && bookingData.category ? parseInt(bookingData.category) : null,
            service: 'service' in bookingData && bookingData.service ? parseInt(bookingData.service) : null,
            employee: 'employee' in bookingData && bookingData.employee ? parseInt(bookingData.employee) : null,
            location: 'location' in bookingData && bookingData.location ? parseInt(bookingData.location) : null,
            eventId: 'eventId' in bookingData && bookingData.eventId ? parseInt(bookingData.eventId) : null,
            eventTag: 'eventTag' in bookingData ? bookingData.eventTag : null,
            eventRecurring: 'eventRecurring' in bookingData ? bookingData.eventRecurring : null
          } : null

          this.shortcodeData.searchToday = typeof window.searchToday !== 'undefined' ? window.searchToday : null

          this.shortcodeData.counter = (bookingData ? bookingData.counter : '')
          this.shortcodeData.hasCategoryShortcode = typeof window.hasCategoryShortcode !== 'undefined' ? window.hasCategoryShortcode : ''
          this.shortcodeData.hasBookingShortcode = typeof window.hasBookingShortcode !== 'undefined' ? window.hasBookingShortcode : ''
          this.shortcodeData.enabled = this.shortcodeData.booking !== null && !Object.values(this.shortcodeData.booking).every(x => (x === null || x === '' || isNaN(x)))
        }
      })
    )
  }
}

if (document.getElementsByClassName('amelia-frontend').length === 0) {
  document.addEventListener('DOMContentLoaded', function () {
    ameliaLoading()
  })
} else {
  ameliaLoading()
}
