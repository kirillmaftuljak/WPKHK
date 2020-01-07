export default {
  methods: {
    notify (title, message, type) {
      setTimeout(() => {
        this.$notify({
          title: title,
          message: message,
          type: type,
          offset: 50
        })
      }, 700)
    }
  }
}
