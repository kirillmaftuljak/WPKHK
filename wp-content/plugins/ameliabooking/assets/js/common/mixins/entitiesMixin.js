export default {

  data: () => ({}),

  methods: {
    getLocationById (id) {
      return this.options.entities.locations.find(location => location.id === id) || null
    },

    getCustomerById (id) {
      return this.options.entities.customers.find(customer => customer.id === id) || null
    },

    getProviderById (id) {
      return this.options.entities.employees.find(employee => employee.id === id) || null
    },

    getServiceById (id) {
      return this.options.entities.services.find(service => service.id === id) || null
    },

    getServiceProviders (serviceId) {
      return this.options.entities.employees.filter(employee =>
        employee.serviceList.filter(service => this.isEmployeeService(employee.id, service.id)).map(service => service.id).indexOf(serviceId) !== -1
      )
    },

    getServicesFromCategories () {
      let services = []

      this.options.entities.categories.map(category => category.serviceList).forEach(function (serviceList) {
        services = services.concat(serviceList)
      })

      return services
    },

    getCategoryServices (categoryId) {
      return this.options.entities.categories.find(category => category.id === categoryId).serviceList
    },

    getCustomerInfo (booking) {
      return 'info' in booking && booking.info ? JSON.parse(booking.info) : this.getCustomerById(booking.customerId)
    },

    isEmployeeServiceLocation (employeeId, serviceId, locationId) {
      return employeeId in this.options.entitiesRelations && serviceId in this.options.entitiesRelations[employeeId] && this.options.entitiesRelations[employeeId][serviceId].indexOf(locationId) !== -1
    },

    isEmployeeService (employeeId, serviceId) {
      return employeeId in this.options.entitiesRelations && serviceId in this.options.entitiesRelations[employeeId]
    },

    isEmployeeLocation (employeeId, locationId) {
      let employeeHasLocation = false

      if (employeeId in this.options.entitiesRelations) {
        for (let serviceId in this.options.entitiesRelations[employeeId]) {
          if (!this.options.entitiesRelations[employeeId].hasOwnProperty(serviceId)) {
            continue
          }

          if (this.options.entitiesRelations[employeeId][serviceId].indexOf(locationId) !== -1) {
            employeeHasLocation = true
          }
        }
      }

      return employeeHasLocation
    },

    getAvailableEntitiesIds (entities, entitiesIds) {
      let availableServiceIds = []
      let availableEmployeeIds = []
      let availableLocationIds = []

      let categoryServicesIds = entitiesIds.categoryId !== null ? entities.categories.find(category => category.id === entitiesIds.categoryId).serviceList.map(service => service.id) : []

      // selected category
      // selected service & employee
      // selected service & employee & location
      if (
        (entitiesIds.categoryId !== null && categoryServicesIds.length === 0) ||
        (entitiesIds.serviceId !== null && entitiesIds.employeeId !== null && !this.isEmployeeService(entitiesIds.employeeId, entitiesIds.serviceId)) ||
        (entitiesIds.serviceId !== null && entitiesIds.employeeId !== null && entitiesIds.locationId !== null && !this.isEmployeeServiceLocation(entitiesIds.employeeId, entitiesIds.serviceId, entitiesIds.locationId))
      ) {
        return {
          services: [],
          locations: [],
          employees: [],
          categories: []
        }
      }

      for (let providerKey in this.options.entitiesRelations) {
        if (!this.options.entitiesRelations.hasOwnProperty(providerKey)) {
          continue
        }

        let providerId = parseInt(providerKey)

        // selected employee
        // selected location (check if employee has at least one available service for location)
        // selected service (check if employee is available for service)
        // selected category (check if employee is available for at least one category service)
        // selected category && location (check if employee is available for at least one category service on location)
        // selected service && location (check if employee is available for service on location)
        if (
          (entitiesIds.employeeId !== null && entitiesIds.employeeId !== providerId) ||
          (entitiesIds.locationId !== null && !this.isEmployeeLocation(providerId, entitiesIds.locationId)) ||
          (entitiesIds.serviceId !== null && !this.isEmployeeService(providerId, entitiesIds.serviceId)) ||
          (entitiesIds.categoryId !== null && categoryServicesIds.filter(serviceId => this.isEmployeeService(providerId, serviceId)).length === 0) ||
          (entitiesIds.categoryId !== null && entitiesIds.locationId !== null && categoryServicesIds.filter(serviceId => this.isEmployeeServiceLocation(providerId, serviceId, entitiesIds.locationId)).length === 0) ||
          (entitiesIds.serviceId !== null && entitiesIds.locationId !== null && !this.isEmployeeServiceLocation(providerId, entitiesIds.serviceId, entitiesIds.locationId))
        ) {
          continue
        }

        if (availableEmployeeIds.indexOf(providerId) === -1) {
          availableEmployeeIds.push(providerId)
        }

        for (let serviceKey in this.options.entitiesRelations[providerId]) {
          if (!this.options.entitiesRelations[providerId].hasOwnProperty(serviceKey)) {
            continue
          }

          let serviceId = parseInt(serviceKey)

          // selected service
          // selected category (check if service belongs to category)
          // selected location (check if employee is available for service on location)
          if (
            (entitiesIds.serviceId !== null && entitiesIds.serviceId !== serviceId) ||
            (entitiesIds.categoryId !== null && categoryServicesIds.indexOf(serviceId) === -1) ||
            (entitiesIds.locationId !== null && !this.isEmployeeServiceLocation(providerId, serviceId, entitiesIds.locationId))
          ) {
            continue
          }

          if (availableServiceIds.indexOf(serviceId) === -1) {
            availableServiceIds.push(serviceId)
          }

          if (this.options.entitiesRelations[providerId][serviceId].length) {
            this.options.entitiesRelations[providerId][serviceId].forEach(function (locationId) {
              // selected location
              if ((entitiesIds.locationId !== null && entitiesIds.locationId !== locationId)) {
                return
              }

              if (availableLocationIds.indexOf(locationId) === -1) {
                availableLocationIds.push(locationId)
              }
            })
          }
        }
      }

      return {
        services: availableServiceIds,
        locations: availableLocationIds,
        employees: availableEmployeeIds,
        categories: entities.categories.filter(category => (category.serviceList.map(service => service.id)).filter(serviceId => availableServiceIds.indexOf(serviceId) !== -1).length > 0).map(category => category.id)
      }
    },

    filterEntities (entities, entitiesIds) {
      let availableEntitiesIds = this.getAvailableEntitiesIds(entities, entitiesIds)

      this.options.entities.employees = entities.employees.filter(employee =>
        availableEntitiesIds.employees.indexOf(employee.id) !== -1 &&
        employee.serviceList.filter(employeeService =>
          availableEntitiesIds.services.indexOf(employeeService.id) !== -1
        ).length > 0
      )

      this.options.entities.categories = entities.categories

      this.options.entities.services = this.getServicesFromCategories().filter(service =>
        service.show &&
        availableEntitiesIds.services.indexOf(service.id) !== -1
      )

      this.options.entities.services.forEach(function (service) {
        service.extras.forEach(function (extra) {
          extra.extraId = extra.id
        })
      })

      this.options.entities.locations = entities.locations.filter(location => availableEntitiesIds.locations.indexOf(location.id) !== -1)

      this.options.entities.customFields = entities.customFields
    },

    getShortCodeEntityIds () {
      return this.$root.shortcodeData.booking ? {
        categoryId: 'category' in this.$root.shortcodeData.booking ? this.$root.shortcodeData.booking.category : null,
        serviceId: 'service' in this.$root.shortcodeData.booking ? this.$root.shortcodeData.booking.service : null,
        employeeId: 'employee' in this.$root.shortcodeData.booking ? this.$root.shortcodeData.booking.employee : null,
        locationId: 'location' in this.$root.shortcodeData.booking ? this.$root.shortcodeData.booking.location : null
      } : {
        categoryId: null,
        serviceId: null,
        employeeId: null,
        locationId: null
      }
    },

    fetchEntities (callback, options) {
      let params = {
        types: options.types
      }

      if (options.page) {
        params.page = options.page
      }

      this.$http.get(`${this.$root.getAjaxUrl}/entities`, {params: params}).then(response => {
        this.options.isFrontEnd = options.isFrontEnd

        this.options.entitiesRelations = response.data.data.entitiesRelations

        if (this.options.isFrontEnd) {
          this.filterEntities(response.data.data, this.getShortCodeEntityIds())
        } else {
          this.options.entities.employees = response.data.data.employees
          this.options.entities.categories = response.data.data.categories
          this.options.entities.locations = response.data.data.locations
          this.options.entities.customers = response.data.data.customers
          this.options.entities.services = this.getServicesFromCategories()

          this.options.entities.services.forEach(function (service) {
            service.extras.forEach(function (extra) {
              extra.extraId = extra.id
            })
          })

          this.options.availableEntitiesIds = this.getAvailableEntitiesIds(response.data.data, {
            categoryId: null,
            serviceId: null,
            employeeId: null,
            locationId: null
          })
        }

        this.options.entities.tags = 'tags' in response.data.data ? response.data.data.tags : []

        this.options.entities.customFields = response.data.data.customFields

        let success = true

        callback(success)
      }).catch(e => {
        console.log(e)

        let success = false

        callback(success)
      })
    },

    getFilteredEntities (filteredEntitiesIds, type, parameter) {
      let savedEntityId = this.appointment && this.appointment.id && this.appointment[parameter] ? this.appointment[parameter] : null

      this.options.entities[type].forEach(function (entity) {
        entity.disabled = filteredEntitiesIds.indexOf(entity.id) === -1
      })

      return this.options.entities[type].filter(entity =>
        this.options.availableEntitiesIds[type].indexOf(entity.id) !== -1 ||
        (savedEntityId !== null ? savedEntityId === entity.id : false)
      )
    }
  },

  computed: {
    visibleLocations () {
      return this.options.entities.locations.filter(location => location.status === 'visible')
    },

    visibleEmployees () {
      return this.options.entities.employees.filter(employee => employee.status === 'visible')
    },

    visibleCustomers () {
      return this.options.entities.customers.filter(customer => customer.status === 'visible')
    },

    visibleServices () {
      return this.options.entities.services.filter(service => service.status === 'visible')
    },

    employeesFiltered () {
      let employees = this.visibleEmployees.filter(employee =>
        employee.serviceList.filter(
          service =>
            service.status === 'visible' &&
            (!this.appointment.serviceId ? true : (this.isEmployeeService(employee.id, service.id) && service.id === this.appointment.serviceId)) &&
            (!this.appointment.locationId ? true : (this.isEmployeeServiceLocation(employee.id, service.id, this.appointment.locationId))) &&
            (!this.appointment.categoryId ? true : (employee.serviceList.filter(service => service.status === 'visible' && service.categoryId === this.appointment.categoryId).length > 0))
        ).length > 0
      )

      return this.options.isFrontEnd ? employees : this.getFilteredEntities(employees.map(employee => employee.id), 'employees', 'providerId')
    },

    servicesFiltered () {
      let selectedEmployeeServicesIds = []

      if (this.appointment.providerId) {
        let selectedEmployee = this.employeesFiltered.find(employee => employee.id === this.appointment.providerId)

        selectedEmployeeServicesIds = typeof selectedEmployee !== 'undefined' ? selectedEmployee.serviceList
          .filter(employeeService => employeeService.status === 'visible')
          .map(employeeService => employeeService.id) : []
      }

      let services = this.visibleServices.filter(service =>
        (!this.appointment.providerId ? true : selectedEmployeeServicesIds.indexOf(service.id) !== -1) &&
        (!this.appointment.locationId ? true : this.employeesFiltered.filter(employee => this.isEmployeeServiceLocation(employee.id, service.id, this.appointment.locationId)).length > 0) &&
        (!this.appointment.categoryId ? true : service.categoryId === this.appointment.categoryId)
      )

      return this.options.isFrontEnd ? services : this.getFilteredEntities(services.map(service => service.id), 'services', 'serviceId')
    },

    locationsFiltered () {
      let selectedEmployeeServices = []

      if (this.appointment.providerId) {
        let selectedEmployee = this.employeesFiltered.find(employee => employee.id === this.appointment.providerId)

        selectedEmployeeServices = typeof selectedEmployee !== 'undefined' ? selectedEmployee.serviceList.filter(employeeService => employeeService.status === 'visible') : []
      }

      let selectedCategory = null

      if (this.appointment.categoryId) {
        selectedCategory = this.categoriesFiltered.find(category => category.id === this.appointment.categoryId)
      }

      let locations = this.visibleLocations.filter(location =>
        (!this.appointment.providerId ? true : selectedEmployeeServices.filter(employeeService => this.isEmployeeServiceLocation(this.appointment.providerId, employeeService.id, location.id)).length > 0) &&
        (!this.appointment.serviceId ? true : this.employeesFiltered.filter(employee => this.isEmployeeServiceLocation(employee.id, this.appointment.serviceId, location.id)).length > 0) &&
        (!this.appointment.categoryId ? true : (typeof selectedCategory !== 'undefined' ? this.employeesFiltered.filter(employee => employee.serviceList.filter(employeeService => employeeService.status === 'visible' && employeeService.categoryId === selectedCategory.id && this.isEmployeeServiceLocation(employee.id, employeeService.id, location.id)).length > 0).length > 0 : false))
      )

      return this.options.isFrontEnd ? locations : this.getFilteredEntities(locations.map(location => location.id), 'locations', 'locationId')
    },

    categoriesFiltered () {
      let selectedEmployee = null

      if (this.appointment.providerId) {
        selectedEmployee = this.employeesFiltered.find(employee => employee.id === this.appointment.providerId)
      }

      let selectedService = null

      if (this.appointment.serviceId) {
        selectedService = this.servicesFiltered.find(service => service.id === this.appointment.serviceId)
      }

      let categories = this.options.entities.categories.filter(category =>
        (!this.appointment.serviceId ? true : typeof selectedService !== 'undefined' ? selectedService.categoryId === category.id : false) &&
        (!this.appointment.locationId ? true : category.serviceList.filter(categoryService => categoryService.status === 'visible' && this.employeesFiltered.filter(employee => this.isEmployeeServiceLocation(employee.id, categoryService.id, this.appointment.locationId)).length > 0).length > 0) &&
        (!this.appointment.providerId ? true : (typeof selectedEmployee !== 'undefined' ? selectedEmployee.serviceList.filter(employeeService => employeeService.status === 'visible' && this.isEmployeeService(this.appointment.providerId, employeeService.id)).map(employeeService => employeeService.categoryId).indexOf(category.id) !== -1 : false))
      )

      return this.options.isFrontEnd ? categories : this.getFilteredEntities(categories.map(category => category.id), 'categories', 'categoryId')
    }
  }

}
