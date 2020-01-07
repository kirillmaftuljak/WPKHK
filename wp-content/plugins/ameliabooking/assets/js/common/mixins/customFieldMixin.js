export default {
  data: () => ({}),

  methods: {
    isCustomFieldVisible (customField, type, bookableId) {
      switch (type) {
        case 'appointment':
          return customField.services.map(service => service.id).indexOf(bookableId) !== -1
        case 'event':
          return customField.events.map(event => event.id).indexOf(bookableId) !== -1
      }

      return false
    },

    setBookingCustomFields () {
      if (this.appointment && this.appointment.bookings && this.appointment.bookings.length) {
        // Go through all bookings
        for (let i = 0; i < this.appointment.bookings.length; i++) {
          // Go through all custom fields
          for (let j = 0; j < this.options.entities.customFields.length; j++) {
            // Add custom fields as empty object for backward compatibility
            if (this.appointment.bookings[i].customFields === null) {
              this.appointment.bookings[i].customFields = {}
            }

            if (typeof this.appointment.bookings[i].customFields[this.options.entities.customFields[j].id] !== 'undefined') {
              this.appointment.bookings[i].customFields[this.options.entities.customFields[j].id].type = this.options.entities.customFields[j].type
            }

            // If custom field is not content and if custom field is not already set, add it in booking
            if (this.options.entities.customFields[j].type !== 'content' &&
              typeof this.appointment.bookings[i].customFields[this.options.entities.customFields[j].id] === 'undefined'
            ) {
              this.$set(
                this.appointment.bookings[i].customFields,
                this.options.entities.customFields[j].id,
                {
                  label: this.options.entities.customFields[j].label,
                  value: this.options.entities.customFields[j].type !== 'checkbox' ? '' : [],
                  type: this.options.entities.customFields[j].type
                }
              )
            }
          }
        }
      }
    },

    getCustomFieldOptions (customFieldOptions) {
      return customFieldOptions.map(option => option.label)
    }
  }
}
