export default {
  data () {
    return {}
  },

  methods: {
    sendAmeliaSmsApiRequest (action, onSuccessCallback, onErrorCallback) {
      this.$http.post(`${this.$root.getAjaxUrl}/notifications/sms`, {
        'action': action,
        'data': typeof this[action] !== 'undefined' ? this[action] : []
      }).then((response) => {
        if (response.data.data.status === 'OK') {
          onSuccessCallback(response.data.data)
        } else {
          onErrorCallback(response.data.data)
        }
      }).catch(e => {
        console.log(e)
      })
    },

    getFormattedMessagePrice (price) {
      return [1, 3].includes(this.$root.settings.payments.priceSeparator)
        ? Math.round(price * 100000) / 100000 : (Math.round(price * 100000) / 100000).toString().replace('.', ',')
    }
  }
}
