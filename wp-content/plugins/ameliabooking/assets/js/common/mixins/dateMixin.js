import moment from 'moment'
import VueCookies from 'vue-cookies'

export default {

  data: () => ({
    formatPHPtoMomentMap: {
      d: 'DD',
      D: 'ddd',
      j: 'D',
      l: 'dddd',
      N: 'E',
      w: 'd',
      W: 'W',
      F: 'MMMM',
      m: 'MM',
      M: 'MMM',
      n: 'M',
      o: 'GGGG',
      Y: 'YYYY',
      y: 'YY',
      a: 'a',
      A: 'A',
      g: 'h',
      G: 'H',
      h: 'hh',
      H: 'HH',
      i: 'mm',
      s: 'ss',
      O: 'ZZ',
      P: 'Z',
      c: 'YYYY-MM-DD[T]HH:mm:ssZ',
      r: 'ddd, DD MMM YYYY HH:mm:ss ZZ',
      U: 'X',
      S: 'o'
    },

    formatPHPtoDatePickerMap: {
      d: 'dd',
      j: 'd',
      M: 'MMM',
      F: 'MMMM',
      m: 'MM',
      n: 'M',
      y: 'yy',
      Y: 'yyyy',
      g: 'HH',
      H: 'HH',
      i: 'mm',
      a: 'A',
      A: 'A',
      s: 'ss'
    },

    formatEx: /[dDjlNwWFmMntoYyaAgGhHisOPcrUS]/g
  }),

  methods: {
    reformatArabicNumbers () {
      let symbolMap = {
        '1': '1',
        '2': '2',
        '3': '3',
        '4': '4',
        '5': '5',
        '6': '6',
        '7': '7',
        '8': '8',
        '9': '9',
        '0': '0'
      }

      let numberMap = {
        '١': '1',
        '٢': '2',
        '٣': '3',
        '٤': '4',
        '٥': '5',
        '٦': '6',
        '٧': '7',
        '٨': '8',
        '٩': '9',
        '٠': '0'
      }

      moment.updateLocale('ar', {
        preparse: function (string) {
          return string.replace(/\u200f/g, '').replace(/[١٢٣٤٥٦٧٨٩٠]/g, function (match) {
            return numberMap[match]
          }).replace(/،/g, ',')
        },
        postformat: function (string) {
          return string.replace(/\d/g, function (match) {
            return symbolMap[match]
          }).replace(/,/g, '،')
        }
      })
    },

    getNowDate () {
      return moment().toDate()
    },

    getDate (date) {
      return moment(date, 'YYYY-MM-DD').toDate()
    },

    getDatabaseFormattedDate (date) {
      return moment(date, 'YYYY-MM-DD').format('YYYY-MM-DD')
    },

    getFrontedFormattedDate (date) {
      return moment(date, 'YYYY-MM-DD').format(this.momentDateFormat)
    },

    getEventFrontedFormattedDate (date) {
      return moment(date, 'YYYY-MM-DD').format('MMM DD')
    },

    getDateString (date) {
      return moment(date).format('YYYY-MM-DD')
    },

    getTimeString (date) {
      return moment(date).format('HH:mm')
    },

    getFrontedFormattedDateTime (datetime) {
      return moment(datetime, 'YYYY-MM-DD HH:mm:ss').format(
        this.momentDateFormat + ' ' + this.momentTimeFormat
      )
    },

    getFrontedFormattedTime (time) {
      return moment(time, 'HH:mm:ss').format(this.momentTimeFormat)
    },

    getFrontedFormattedTimeFromDateTimeString (datetime) {
      return moment(datetime, 'YYYY-MM-DD HH:mm:ss').format(this.momentTimeFormat)
    },

    getDatePickerFirstDayOfWeek () {
      // Sunday index on WordPress is 0 and in DatePicker is 1
      return this.$root.settings.wordpress.startOfWeek + 1
    },

    getWordPressFirstDayOfWeek () {
      return this.$root.settings.wordpress.startOfWeek
    },

    getTimeSlotLength () {
      return this.$root.settings.general.timeSlotLength
    },

    getDatePickerInitRange () {
      let ameliaRangePast = VueCookies.get('ameliaRangePast')
      let ameliaRangeFuture = VueCookies.get('ameliaRangeFuture')

      if (ameliaRangePast !== null && ameliaRangeFuture !== null) {
        return {
          start: moment().subtract(ameliaRangePast, 'days').toDate(),
          end: moment().add(ameliaRangeFuture, 'days').toDate()
        }
      }

      return {
        start: moment().toDate(),
        end: moment().add(6, 'days').toDate()
      }
    },

    setDatePickerSelectedDaysCount (start, end) {
      let currentDate = moment().format('YYYY-MM-DD')

      VueCookies.set('ameliaRangePast', moment(currentDate, 'YYYY-MM-DD').diff(moment(start, 'YYYY-MM-DD'), 'days'))
      VueCookies.set('ameliaRangeFuture', moment(end, 'YYYY-MM-DD').diff(moment(currentDate, 'YYYY-MM-DD'), 'days'))
    },

    getDatePickerNegativeInitRange () {
      return {
        start: moment().subtract(6, 'days').toDate(),
        end: moment().toDate()
      }
    },

    getFrontedFormattedTimeIncreased (time, seconds) {
      return moment(time, 'HH:mm:ss').add(seconds, 'seconds').format(this.momentTimeFormat)
    },

    getTime (datetime) {
      return moment(datetime, 'YYYY-MM-DD HH:mm:ss').format('HH:mm:ss')
    },

    getClientUtcOffset (dateTimeString) {
      return dateTimeString ? moment(dateTimeString, 'YYYY-MM-DD HH:mm:ss').utcOffset() : moment().utcOffset()
    },

    getMinutesToDays (minutes) {
      let d = Math.floor(minutes / 1440)
      let h = Math.floor((minutes - (d * 1440)) / 60)
      let m = Math.round(minutes % 60)

      return (d > 0 ? d + 'd ' : '') + (h > 0 ? h + 'h ' : '') + (m > 0 ? m + 'm ' : '')
    }
  },

  computed: {
    momentTimeFormat () {
      let that = this

      // Fix for French "G \h i \m\i\n" format
      if (this.$root.settings.wordpress.timeFormat === 'G \\h i \\m\\i\\n') {
        return 'HH:mm'
      }

      return this.$root.settings.wordpress.timeFormat.replace(this.formatEx, function (phpStr) {
        return that.formatPHPtoMomentMap[phpStr]
      })
    },

    momentDateFormat () {
      let that = this
      return this.$root.settings.wordpress.dateFormat.replace(this.formatEx, function (phpStr) {
        return that.formatPHPtoMomentMap[phpStr]
      })
    },

    vCalendarFormats () {
      if (window.localeLanguage === 'ar') {
        return {
          input: [this.momentDateFormat, 'YYYY-MM-DD', 'YYYY/MM/DD'],
          weekdays: 'WWWW'
        }
      }

      return {
        input: [this.momentDateFormat, 'YYYY-MM-DD', 'YYYY/MM/DD']
      }
    },

    timePickerFormat () {
      return 'HH:mm'
    }
  }

}
