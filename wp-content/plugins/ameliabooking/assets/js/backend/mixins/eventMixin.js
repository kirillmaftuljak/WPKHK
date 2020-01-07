import moment from 'moment'
import settingsMixin from '../../../js/common/mixins/settingsMixin'

export default {

  mixins: [settingsMixin],

  data () {
    return {
      event: null,
      bookingCreatedCount: 0,
      attributes: [
        {
          highlight: {
            backgroundColor: '#ddd'
          },
          dates: []
        }
      ],
      selectedRecurringDate: null,
      recurringDates: null,
      updateStatusDisabled: false,
      recurringPeriods: [
        // {
        //   label: this.$root.labels.event_recurring_type_daily,
        //   value: 'daily'
        // },
        {
          label: this.$root.labels.event_recurring_type_weekly,
          value: 'weekly'
        },
        {
          label: this.$root.labels.event_recurring_type_monthly,
          value: 'monthly'
        },
        {
          label: this.$root.labels.event_recurring_type_yearly,
          value: 'yearly'
        }
      ],
      colors: [
        '#1788FB',
        '#4BBEC6',
        '#FBC22D',
        '#FA3C52',
        '#D696B8',
        '#689BCA',
        '#26CC2B',
        '#FD7E35',
        '#E38587',
        '#774DFB'
      ],
      options: {
        fetched: false,
        entities: {
          settings: {
            payments: {
              wc: null
            }
          },
          employees: [],
          locations: [],
          tags: []
        }
      }
    }
  },

  methods: {
    showDialogEditEvent (id) {
      this.dialogEvent = true
      this.event = null
      this.getEvent(id)
    },

    closeDialogEvent () {
      this.dialogEvent = false
    },

    getInitEventObject () {
      return {
        id: 0,
        parentId: null,
        name: '',
        periods: [
          {
            id: null,
            eventId: null,
            range: null,
            startTime: null,
            endTime: null,
            bookings: []
          }
        ],
        bookingStartsNow: true,
        bookingStartsDate: null,
        bookingStartsTime: null,
        bookingEndsAfter: true,
        bookingEndsDate: null,
        bookingEndsTime: null,
        isRecurring: false,
        recurring: {
          cycle: null,
          order: null,
          until: null
        },
        maxCapacity: null,
        price: 0,
        locationId: null,
        customLocation: null,
        providers: [],
        tags: [],
        description: null,
        gallery: [],
        colorType: 2,
        selectedColor: '#1788FB',
        customColor: '#1788FB',
        show: true,
        places: 0,
        addToCalendarData: null,
        showAddToCalendar: false,
        showEventDetails: false,
        showEventBooking: false,
        canceling: false,
        deleting: false,
        bookable: true,
        settings: this.getInitEntitySettings()
      }
    },

    showDialogNewEvent () {
      this.event = this.getInitEventObject()

      this.dialogEvent = true
    },

    getEvent (id) {
      this.$http.get(`${this.$root.getAjaxUrl}/events/` + id)
        .then(response => {
          let event = Object.assign(this.getInitEventObject(), response.data.data.event)

          let eventPeriods = []

          response.data.data.event.periods.forEach(function (period) {
            let startDate = moment(period.periodStart, 'YYYY-MM-DD HH:mm:ss')
            let endDate = moment(period.periodEnd, 'YYYY-MM-DD HH:mm:ss')

            let startTime = period.periodStart.split(' ')[1].substring(0, 5)
            let endTime = period.periodEnd.split(' ')[1].substring(0, 5)

            if (endTime === '00:00') {
              endDate.subtract(1, 'days')
              endTime = '24:00'
            }

            eventPeriods.push(
              {
                id: period.id ? period.id : null,
                eventId: period.eventId ? period.eventId : null,
                range: {
                  start: startDate.toDate(),
                  end: endDate.toDate()
                },
                startTime: startTime,
                endTime: endTime,
                bookings: period.bookings
              }
            )
          })

          event.periods = eventPeriods

          let eventTags = []

          event.tags.forEach(function (eventTag) {
            eventTags.push(eventTag.name)
          })

          event.tags = eventTags

          if (event.recurring === null) {
            event.recurring = {
              cycle: null,
              until: null,
              order: null
            }
          } else {
            event.isRecurring = true

            event.recurring.until = response.data.data.event.recurring.until ? moment(response.data.data.event.recurring.until).toDate() : null
          }

          event.bookingStartsNow = response.data.data.event.bookingOpens === null
          event.bookingStartsDate = response.data.data.event.bookingOpens ? moment(response.data.data.event.bookingOpens).toDate() : null
          event.bookingStartsTime = response.data.data.event.bookingOpens ? response.data.data.event.bookingOpens.split(' ')[1].substring(0, 5) : null

          event.bookingEndsAfter = response.data.data.event.bookingCloses === null
          event.bookingEndsDate = response.data.data.event.bookingCloses ? moment(response.data.data.event.bookingCloses).toDate() : null
          event.bookingEndsTime = response.data.data.event.bookingCloses ? response.data.data.event.bookingCloses.split(' ')[1].substring(0, 5) : null

          let eventBookings = event.bookings

          eventBookings.forEach(function (booking) {
            let bookingInfo = JSON.parse(booking.info)

            booking.customer.firstName = bookingInfo.firstName
            booking.customer.lastName = bookingInfo.lastName
            booking.customer.phone = bookingInfo.phone

            booking.show = true
            booking.removing = false
            booking.checked = false

            if (booking.customFields === '[]' || booking.customFields === null) {
              booking.customFields = '{}'
            }

            booking.customFields = JSON.parse(booking.customFields)
          })

          let isCustomColor = false

          this.colors.forEach(function (color) {
            if (color === event.color) {
              event.colorType = 1
              event.selectedColor = color
              event.customColor = color
              isCustomColor = true
            }
          })

          if (!isCustomColor) {
            event.colorType = 2
            event.selectedColor = null
            event.customColor = event.color
          }

          event.gallery = event.gallery.sort((a, b) => (a.position > b.position) ? 1 : -1)

          this.eventBookings = eventBookings

          this.setEntitySettings(event)

          this.event = event

          this.bookingCreatedCount++
        })
        .catch(e => {
          console.log(e.message)
        })
    }
  },

  watch: {
  }
}
