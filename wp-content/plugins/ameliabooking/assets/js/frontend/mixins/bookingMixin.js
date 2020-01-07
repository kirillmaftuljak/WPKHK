import moment from 'moment'

export default {

  data () {
    return {}
  },

  methods: {

    getAppointmentDuration (service, extras) {
      return service.duration + extras.filter(extra => extra.selected).map(extra => extra.duration * extra.quantity).reduce((a, b) => a + b, 0)
    },

    getCurrentUser () {
      this.$http.get(`${this.$root.getAjaxUrl}/users/current`)
        .then(response => {
          this.currentUser = response.data.data.user

          if (this.currentUser) {
            this.appointment.bookings[0].customerId = this.currentUser.id
            this.appointment.bookings[0].customer.id = this.currentUser.id
            this.appointment.bookings[0].customer.externalId = this.currentUser.externalId
            this.appointment.bookings[0].customer.email = this.currentUser.email
            this.appointment.bookings[0].customer.firstName = this.currentUser.firstName
            this.appointment.bookings[0].customer.lastName = this.currentUser.lastName
            this.appointment.bookings[0].customer.phone = this.currentUser.phone || ''
          }
        })
        .catch(e => {
          console.log('getCurrentUser fail')
        })
    },

    getFormattedTimeSlot (slot, duration) {
      return this.getFrontedFormattedTime(slot) + ' - ' + moment(slot, 'HH:mm:ss').add(duration, 'seconds').format(this.momentTimeFormat)
    },

    getConvertedTimeSlots (dateSlots) {
      let formattedSlots = {}

      for (let date in dateSlots) {
        for (let time in dateSlots[date]) {
          let clientDateAndTime = moment.utc(date + ' ' + time, 'YYYY-MM-DD HH:mm').local().format('YYYY-MM-DD HH:mm').split(' ')

          if (!(clientDateAndTime[0] in formattedSlots)) {
            formattedSlots[clientDateAndTime[0]] = {}
          }

          formattedSlots[clientDateAndTime[0]][clientDateAndTime[1]] = dateSlots[date][time]
        }
      }

      return formattedSlots
    },

    handleCapacity () {
      let $this = this
      let groupEnabled = false
      let maxCapacity = 0
      let minCapacity = 0

      if ($this.appointment.serviceId) {
        if ($this.appointment.providerId) {
          let employee = this.options.entities.employees.find(employee => employee.id === $this.appointment.providerId)
          let service = employee.serviceList.find(service => service.id === $this.appointment.serviceId)

          groupEnabled = service.maxCapacity > 1 && (service.bringingAnyone || !this.$root.settings.appointments.allowBookingIfNotMin)
          maxCapacity = service.maxCapacity
          minCapacity = this.$root.settings.appointments.allowBookingIfNotMin ? 1 : service.minCapacity
        } else {
          this.options.entities.employees.forEach(function (employee) {
            employee.serviceList.forEach(function (service) {
              if (service.id === $this.appointment.serviceId) {
                if (service.maxCapacity > 1 && service.bringingAnyone) {
                  groupEnabled = true
                }

                if (service.maxCapacity > maxCapacity) {
                  maxCapacity = service.maxCapacity
                }

                if (minCapacity < service.minCapacity) {
                  minCapacity = service.minCapacity
                }
              }
            })
          })
        }
      }

      this.group.options = []

      for (let i = minCapacity - 1; i < maxCapacity; i++) {
        if (i !== 0) {
          let persons = 'ameliaBooking' in window && 'form' in window.ameliaBooking && window.ameliaBooking.form.allPersons ? i + 1 : i

          this.group.options.push({
            label: persons === 1 ? persons + ' ' + this.$root.labels.person_upper : persons + ' ' + this.$root.labels.persons_upper,
            value: i + 1
          })
        }
      }

      if (maxCapacity !== 0 && this.appointment.bookings[0].persons > maxCapacity) {
        this.appointment.bookings[0].persons = maxCapacity
      }

      if (this.group.enabled || (groupEnabled && !this.$root.settings.appointments.allowBookingIfNotMin && minCapacity > 1)) {
        this.group.enabled = groupEnabled
      }

      if (groupEnabled && !this.$root.settings.appointments.allowBookingIfNotMin && minCapacity > 1) {
        this.appointment.bookings[0].persons = minCapacity
      }

      this.group.allowed = groupEnabled && (this.$root.settings.appointments.allowBookingIfNotMin || (!this.$root.settings.appointments.allowBookingIfNotMin && minCapacity === 1))
    }

  }

}
