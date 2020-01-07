export default {

  data: () => ({}),

  methods: {
    replaceExistingObjectProperties (targetSettings, sourceSetting) {
      let $this = this

      Object.keys(targetSettings).forEach(function (key) {
        if (targetSettings[key] !== null && typeof targetSettings[key] === 'object' && key in sourceSetting) {
          $this.replaceExistingObjectProperties(targetSettings[key], sourceSetting[key])
          return
        }

        if (key in sourceSetting) {
          targetSettings[key] = sourceSetting[key]
        }
      })
    },

    addMissingObjectProperties (targetSettings, sourceSetting) {
      let $this = this

      Object.keys(sourceSetting).forEach(function (key) {
        let addSettings = false

        if (!(key in targetSettings)) {
          if (typeof sourceSetting[key] === 'object') {
            targetSettings[key] = {}
            addSettings = true
          } else {
            targetSettings[key] = null
            addSettings = true
          }
        }

        if (sourceSetting[key] !== null && typeof sourceSetting[key] === 'object') {
          $this.addMissingObjectProperties(targetSettings[key], sourceSetting[key])
          return
        }

        if (addSettings) {
          targetSettings[key] = sourceSetting[key]
        }
      })
    },

    scrollView (selector) {
      if (typeof jQuery !== 'undefined' && jQuery(window).width() <= 600) {
        document.getElementById(selector).scrollIntoView({behavior: 'smooth', block: 'start', inline: 'nearest'})
      }
    },

    scrollViewInModal (selector) {
      this.$nextTick(() => {
        document.querySelectorAll('.am-dialog-scrollable')[0].scrollTop = document.getElementById(selector).offsetTop
      })
    },

    getUrlQueryParams (url) {
      let queryString = url.indexOf('#') ? url.substring(0, url.indexOf('#')).split('?')[1] : url.split('?')[1]
      let keyValuePairs = queryString.split('&')
      let keyValue = []
      let queryParams = {}
      keyValuePairs.forEach(function (pair) {
        keyValue = pair.split('=')
        queryParams[keyValue[0]] = decodeURIComponent(keyValue[1]).replace(/\+/g, ' ')
      })
      return queryParams
    },

    removeURLParameter (url, parameter) {
      let urlParts = url.split('?')
      if (urlParts.length >= 2) {
        let prefix = encodeURIComponent(parameter) + '='
        let pars = urlParts[1].split(/[&;]/g)

        for (let i = pars.length; i-- > 0;) {
          if (pars[i].lastIndexOf(prefix, 0) !== -1) {
            pars.splice(i, 1)
          }
        }

        url = urlParts[0] + (pars.length > 0 ? '?' + pars.join('&') : '')
        return url
      } else {
        return url
      }
    },

    capitalizeFirstLetter (string) {
      return string.charAt(0).toUpperCase() + string.slice(1)
    }
  }
}
