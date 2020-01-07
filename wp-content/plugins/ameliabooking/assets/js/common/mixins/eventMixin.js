import moment from 'moment'

export default {

  data () {
    return {}
  },

  methods: {
    getImplodedPeriods (eventPeriods) {
      let lastPeriod = null

      let parsedPeriods = []

      let result = []

      eventPeriods.forEach(function (period, index) {
        let isConnectedPeriod = (lastPeriod && lastPeriod.periodEnd === period.periodStart)

        if (isConnectedPeriod) {
          parsedPeriods.pop()
        }

        lastPeriod = {
          periodStart: isConnectedPeriod ? lastPeriod.periodStart : period.periodStart,
          periodEnd: period.periodEnd,
          isConnected: isConnectedPeriod
        }

        parsedPeriods.push(lastPeriod)

        if (eventPeriods.length === index + 1 || (index + 1 in eventPeriods && eventPeriods[index + 1].periodStart !== period.periodEnd)) {
          result = result.concat(JSON.parse(JSON.stringify(parsedPeriods)))
          parsedPeriods = []
        }
      })

      return result
    },

    getExplodedPeriods (connectedPeriods) {
      let result = []

      connectedPeriods.forEach(function (period) {
        if (!period.isConnected) {
          let startDate = moment(period.periodStart.split(' ')[0], 'YYYY-MM-DD')
          let endDate = moment(period.periodEnd.split(' ')[0], 'YYYY-MM-DD')

          let startTime = period.periodStart.split(' ')[1]
          let endTime = period.periodEnd.split(' ')[1]

          if (endTime === '00:00:00') {
            endTime = '24:00:00'
            endDate.subtract(1, 'days')
          }

          let periodDates = []

          while (startDate.isSameOrBefore(endDate)) {
            periodDates.push(startDate.format('YYYY-MM-DD'))

            startDate.add(1, 'days')
          }

          periodDates.forEach(function (dateString) {
            result.push({
              periodStart: dateString + ' ' + startTime,
              periodEnd: dateString + ' ' + endTime
            })
          })
        } else {
          result.push(period)
        }
      })

      return result
    }
  }

}
