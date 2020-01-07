export default {
  data: () => ({}),

  methods: {
    getEmployeeActivityLabel (activity) {
      switch (activity) {
        case 'available':
          return this.$root.labels.available
        case 'away':
          return this.$root.labels.away
        case 'break':
          return this.$root.labels.break
        case 'busy':
          return this.$root.labels.busy
        case 'dayoff':
          return this.$root.labels.dayoff
      }
    },

    getParsedWeekDayPeriods (weekDayItem) {
      let periods = []

      weekDayItem.periodList.forEach(function (periodItem) {
        periods.push(
          {
            time: [
              periodItem.startTime.substring(0, periodItem.startTime.length - 3),
              periodItem.endTime.substring(0, periodItem.endTime.length - 3)
            ],
            id: periodItem.id,
            serviceIds: periodItem.periodServiceList.map(periodService => periodService.serviceId),
            periodServiceList: periodItem.periodServiceList,
            savedPeriodServiceList: JSON.parse(JSON.stringify(periodItem.periodServiceList))
          }
        )
      })

      if (!periods.length && weekDayItem.startTime && weekDayItem.endTime) {
        periods.push(
          {
            time: [
              weekDayItem.startTime.substring(0, weekDayItem.startTime.length - 3),
              weekDayItem.endTime.substring(0, weekDayItem.endTime.length - 3)
            ],
            id: null,
            serviceIds: [],
            periodServiceList: [],
            savedPeriodServiceList: []
          }
        )
      }

      return periods
    },

    getParsedWeekDayList (employee) {
      let tempList = []
      let $this = this

      let days = [
        this.$root.labels.weekday_monday,
        this.$root.labels.weekday_tuesday,
        this.$root.labels.weekday_wednesday,
        this.$root.labels.weekday_thursday,
        this.$root.labels.weekday_friday,
        this.$root.labels.weekday_saturday,
        this.$root.labels.weekday_sunday
      ]

      for (let i = 0; i < 7; i++) {
        tempList.push(
          {
            id: null,
            form: {
              type: null,
              isNew: null,
              index: null,
              show: false,
              data: {}
            },
            day: days[i],
            time: [],
            breaks: [],
            periods: []
          }
        )
      }

      if (employee) {
        employee.weekDayList.forEach(function (weekDayItem) {
          let dayIndex = weekDayItem.dayIndex - 1

          weekDayItem.timeOutList.forEach(function (timeOutItem) {
            tempList[dayIndex].breaks.push(
              {
                id: timeOutItem.id,
                time: [
                  timeOutItem.startTime.substring(0, timeOutItem.startTime.length - 3),
                  timeOutItem.endTime.substring(0, timeOutItem.endTime.length - 3)
                ]
              })
          })

          tempList[dayIndex].periods = $this.getParsedWeekDayPeriods(weekDayItem)

          tempList[dayIndex].time = [
            weekDayItem.startTime.substring(0, weekDayItem.startTime.length - 3),
            weekDayItem.endTime.substring(0, weekDayItem.endTime.length - 3)
          ]

          tempList[dayIndex].day = days[weekDayItem.dayIndex - 1]
          tempList[dayIndex].id = weekDayItem.id
        })
      }

      return tempList
    },

    getParsedCategorizedServiceList (employee, categories) {
      let categorizedServiceList = []

      categories.forEach(function (catItem) {
        let serviceList = []

        catItem.serviceList.filter(service =>
          (service.status === 'visible') ||
          (service.status === 'hidden' && employee.serviceList.map(employeeService => employeeService.id).indexOf(service.id) !== -1)
        ).forEach(function (catSerItem) {
          let employeeService = null

          if (employee) {
            employee.serviceList.forEach(function (serItem) {
              if (serItem.id === catSerItem.id) {
                employeeService = Object.assign({}, serItem)
                employeeService.state = true
              }
            })
          }

          if (employeeService) {
            serviceList.push(employeeService)
          } else {
            let service = Object.assign({}, catSerItem)
            service.state = false
            serviceList.push(service)
          }
        })

        categorizedServiceList.push(
          {
            id: catItem.id,
            name: catItem.name,
            serviceList: serviceList
          }
        )
      })

      return categorizedServiceList
    },

    getInitEmployeeObject () {
      return {
        id: 0,
        type: 'provider',
        status: 'visible',
        firstName: '',
        lastName: '',
        email: '',
        externalId: '',
        locationId: '',
        phone: '',
        googleCalendar: [],
        note: '',
        pictureFullPath: '',
        pictureThumbPath: '',
        serviceList: [],
        weekDayList: this.getSettingsSchedule(),
        specialDayList: [],
        dayOffList: []
      }
    }
  }
}
