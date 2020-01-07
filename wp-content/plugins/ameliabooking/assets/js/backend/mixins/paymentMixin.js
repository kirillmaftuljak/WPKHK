export default {

  data: () => ({}),

  methods: {
    getPaymentData (paymentId, appointment, event) {
      let $this = this
      let selectedPaymentModalData = {}

      selectedPaymentModalData.paymentId = paymentId

      if (appointment) {
        selectedPaymentModalData.bookableType = 'appointment'
        selectedPaymentModalData.bookings = appointment.bookings
        selectedPaymentModalData.bookingStart = appointment.bookingStart
        selectedPaymentModalData.bookableName = this.getServiceById(appointment.serviceId).name

        let provider = this.getProviderById(appointment.providerId)
        provider.fullName = provider.firstName + ' ' + provider.lastName

        selectedPaymentModalData.providers = [provider]

        appointment.bookings.forEach(function (bookItem) {
          bookItem.payments.forEach(function (payItem) {
            if (payItem.id === paymentId) {
              selectedPaymentModalData.customer = $this.getCustomerById(bookItem.customerId)
            }
          })
        })
      }

      if (event) {
        selectedPaymentModalData.bookableType = 'event'
        selectedPaymentModalData.bookings = event.bookings
        selectedPaymentModalData.bookingStart = event.periods[0].periodStart
        selectedPaymentModalData.bookableName = event.name
        selectedPaymentModalData.providers = event.providers

        event.bookings.forEach(function (bookItem) {
          bookItem.payments.forEach(function (payItem) {
            if (payItem.id === paymentId) {
              selectedPaymentModalData.customer = $this.getCustomerById(bookItem.customerId)
            }
          })
        })
      }

      return selectedPaymentModalData
    },

    getPaymentGatewayNiceName (payment) {
      if (payment.gateway === 'onSite') {
        return this.$root.labels.on_site
      }

      if (payment.gateway === 'wc') {
        return payment.gatewayTitle
      }

      if (payment.gateway) {
        return payment.gateway.charAt(0).toUpperCase() + payment.gateway.slice(1)
      }
    }
  }

}
