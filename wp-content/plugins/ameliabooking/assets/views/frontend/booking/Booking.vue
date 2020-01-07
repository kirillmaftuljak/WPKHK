<template>

  <div class="am-wrap">
    <!-- Spinner -->
    <div class="am-spinner am-section" v-show="!fetched">
      <img class="svg-booking am-spin" :src="$root.getUrl + 'public/img/oval-spinner.svg'">
      <img class="svg-booking am-hourglass" :src="$root.getUrl + 'public/img/hourglass.svg'">
    </div>

    <div :id="id" class="am-step-booking-catalog" v-show="fetched">
      <!-- Select Service -->
      <div class="am-select-service">
        <p class="am-select-service-title" v-show="showServices">
          {{ $root.labels.please_select + ' ' + $root.labels.service }}:
        </p>
        <p class="am-select-service-title" v-show="!showServices">
          {{ $root.labels.book_appointment }}
        </p>

        <!-- Booking Form -->
        <el-form :model="appointment" ref="booking" :rules="rules" label-position="top">

          <!-- Service -->
          <el-form-item
              v-if="showServices"
              :label="capitalizeFirstLetter($root.labels.service) + ':'"
              prop="serviceId"
              class="am-select-service-option"
          >
            <el-select
                v-model="appointment.serviceId"
                @change="changeService"
                placeholder=""
                :loading=!fetched
            >
              <el-option
                  v-for="service in servicesFiltered"
                  :key="service.id"
                  :label="service.name"
                  :value="service.id"
              >
              </el-option>
            </el-select>
          </el-form-item>

          <!-- Location -->
          <el-form-item
              :label="$root.labels.location_colon"
              v-if="showLocations"
              class="am-select-location-option"
          >
            <el-select
                v-model="appointment.locationId"
                @change="changeLocation"
                placeholder=""
                clearable
                :loading=!fetched
            >
              <el-option
                  v-for="location in locationsFiltered"
                  :disabled="location.disabled"
                  :key="location.id"
                  :label="location.name"
                  :value="location.id">
              </el-option>
            </el-select>
          </el-form-item>

          <!-- Employee -->
          <el-form-item
              :label="capitalizeFirstLetter($root.labels.employee) + ':'"
              v-if="showEmployees"
              class="am-select-employee-option"
          >
            <el-select
                v-model="appointment.providerId"
                @change="changeEmployee"
                @clear="appointment.providerId = 0"
                placeholder=""
                :clearable="appointment.providerId !== 0"
                :loading=!fetched
            >
              <el-option
                  :key="0"
                  :label="$root.settings.labels.enabled ? $root.labels.any + ' ' + $root.labels.employee : $root.labels.any_employee"
                  :value="0"
                  class="am-select-any-employee-option"
              >
              </el-option>
              <el-option
                  v-for="employee in employeesFiltered"
                  :key="employee.id"
                  :label="employee.firstName + ' ' + employee.lastName"
                  :value="employee.id"
              >
              </el-option>
            </el-select>
          </el-form-item>

          <!-- Bringing anyone with you -->
          <el-form-item label="" v-show="group.allowed">
            <el-row>
              <el-col :span="18">
                <span>{{ $root.labels.bringing_anyone_with_you }}</span>
              </el-col>
              <el-col :span="6" class="am-align-right">
                <el-switch v-model="group.enabled" @change="enableGrouping"></el-switch>
              </el-col>
            </el-row>
          </el-form-item>

          <!-- Number of persons -->
          <transition name="fade">
            <div class="am-grouped" v-show="group.enabled">
              <el-form-item :label="$root.labels.number_of_additional_persons">
                <el-select placeholder="" v-model="appointment.bookings[0].persons" @change="changeNumberOfPersons">
                  <el-option
                      v-for="item in group.options"
                      :key="item.value"
                      :label="item.label"
                      :value="item.value"
                  >
                  </el-option>
                </el-select>
              </el-form-item>
            </div>
          </transition>

          <!-- Extra Block -->
          <transition-group name="fade" class="am-extras-add">
            <div class="am-extras"
                 v-if="appointment.serviceId && getServiceById(appointment.serviceId).extras.length > 0"
                 v-for="(selectedExtra, key) in selectedExtras"
                 :key="key + 1"
            >
              <el-row :gutter="16" class="am-flex-row-middle-align-mobile">

                <!-- Extra Type -->
                <el-col :span="14">
                  <el-form-item :label="$root.labels.extra_colon">
                    <el-select
                        v-model="selectedExtra.id"
                        @change="changeSelectedExtra(selectedExtra)"
                        placeholder=""
                    >
                      <el-option
                          v-for="extra in getAvailableExtras(selectedExtra)"
                          :key="extra.id"
                          :label="extra.name"
                          :value="extra.id">
                      </el-option>
                    </el-select>
                  </el-form-item>
                </el-col>

                <!-- Extra Quantity -->
                <el-col :span="7">
                  <el-form-item :label="$root.labels.qty_colon">
                    <el-select
                        v-model="selectedExtra.quantity"
                        :disabled="selectedExtra.id === null"
                        @change="changeSelectedExtra(selectedExtra)"
                        placeholder=""
                    >
                      <el-option
                          v-for="i in getSelectedExtraMaxQuantity(selectedExtra)"
                          :key="i"
                          :label="i"
                          :value="i"
                      >
                      </el-option>
                    </el-select>
                  </el-form-item>
                </el-col>

                <!-- Remove Extra -->
                <el-col :span="3" class="am-align-right">
                  <div class="am-delete-element" @click="deleteExtra(key)">
                    <i class="el-icon-minus"></i>
                  </div>
                </el-col>

              </el-row>

              <!-- Extra Duration & Price-->
              <el-row
                  :gutter="16" class="am-flex-row-middle-align-mobile"
                  v-if="selectedExtra.duration || selectedExtra.price"
              >

                <!-- Extra Duration -->
                <el-col :span="14">
                  <el-form-item :label="$root.labels.duration_colon">
                    <span>
                      {{  selectedExtra.duration ? secondsToNiceDuration(selectedExtra.duration) : '/'  }}</span>
                  </el-form-item>
                </el-col>

                <!-- Extra Price -->
                <el-col :span="10">
                  <el-form-item :label="$root.labels.price_colon">
                    <span>
                      {{ getFormattedPrice(selectedExtra.price) }}</span>
                  </el-form-item>
                </el-col>

              </el-row>

            </div>
          </transition-group>

          <!-- Add extra -->
          <div class="am-add-element"
               v-show="appointment.serviceId && selectedExtras.length < getServiceById(appointment.serviceId).extras.length"
               @click="addExtra"
          >
            <i class="el-icon-plus"></i> <span>{{ $root.labels.add_extra }}</span>
          </div>

          <!-- Continue -->
          <div class="am-button-wrapper">
            <el-button
                :loading="loadingTimeSlots"
                type="primary"
                @click="getTimeSlots"
            >
              {{ $root.labels.continue }}
            </el-button>
          </div>

        </el-form>
      </div>

      <!-- Pick Date & Time -->
      <div class="am-payment-error" v-if="showEmptyCalendar">
        <el-alert
            :title="$root.labels.unavailable_booking_error"
            type="warning"
            show-icon
        >
        </el-alert>
      </div>
      <div :id="this.id + '-calendar'" class="am-select-date am-select-service-date-transition">
        <p class="am-select-date-title">{{ $root.labels.pick_date_and_time_colon }}</p>
        <v-date-picker
            v-model="selectedDate"
            mode="single"
            id="am-calendar-picker"
            class='am-calendar-picker'
            @dayclick="selectDate"
            @input="setTimeSlots"
            :available-dates="availableDates"
            :disabled-dates='disabledWeekdays'
            :show-day-popover=false
            :is-expanded=true
            :is-inline=true
            :disabled-attribute="disabledAttribute"
            :formats="vCalendarFormats"
            @update:fromPage="changeMonth"
        >
        </v-date-picker>

        <!-- Time Slots -->
        <transition name="fade">
          <div :id="calendarId" v-show="showTimes">
            <div class="am-appointment-times am-scroll">
              <el-radio-group v-model="appointment.bookingStartTime" size="medium" @change="selectTime">
                <el-radio-button
                    v-for="(slot, index) in availableTimeSlots"
                    :label="slot"
                    :key="index + 1"
                >
                  {{ getFormattedTimeSlot(slot, appointment.duration) }}
                </el-radio-button>
              </el-radio-group>
            </div>
          </div>
        </transition>

        <!-- Back & Continue Buttons -->
        <div id="am-button-wrapper" class="am-button-wrapper">

          <!-- Back Button -->
          <transition name="fade">
            <el-button
                id="am-back-button"
                @click="togglePicker()"
                v-if="showCalendarBackButton"
            >
              {{ $root.labels.back }}
            </el-button>
          </transition>

          <!-- Continue Button -->
          <transition name="fade">
            <el-button
                id="am-continue-button"
                v-show="showCalendarContinueButton"
                @click="showConfirmBookingDialog"
            >
              {{ $root.labels.continue }}
            </el-button>
          </transition>

        </div>

      </div>

      <!-- Confirm Booking -->
      <confirm-booking
          v-if="activeConfirm"
          dialogClass="am-confirm-booking am-scroll"
          bookableType="appointment"
          :bookable="getBookableData()"
          :appointment="appointment"
          :provider="getProviderById(appointment.providerId)"
          :location="getLocationById(appointment.locationId)"
          :customFields="options.entities.customFields"
          @confirmedBooking="confirmedBooking"
          @cancelBooking="cancelBooking"
      >
      </confirm-booking>

      <!-- Add To Calendar -->
      <transition name="fade">
        <add-to-calendar
            v-if="showAddToCalendar"
            :addToCalendarData="addToCalendarData"
            @closeDialogAddToCalendar="closeDialogAddToCalendar"
        ></add-to-calendar>
      </transition>

    </div>
  </div>
</template>

<script>
  import moment from 'moment'
  import imageMixin from '../../../js/common/mixins/imageMixin'
  import dateMixin from '../../../js/common/mixins/dateMixin'
  import entitiesMixin from '../../../js/common/mixins/entitiesMixin'
  import PhoneInput from '../../parts/PhoneInput.vue'
  import ConfirmBooking from '../parts/ConfirmBooking.vue'
  import AddToCalendar from '../parts/AddToCalendar.vue'
  import bookingMixin from '../../../js/frontend/mixins/bookingMixin'
  import helperMixin from '../../../js/backend/mixins/helperMixin'
  import durationMixin from '../../../js/common/mixins/durationMixin'
  import priceMixin from '../../../js/common/mixins/priceMixin'
  import customFieldMixin from '../../../js/common/mixins/customFieldMixin'

  export default {

    mixins: [imageMixin, dateMixin, entitiesMixin, bookingMixin, helperMixin, durationMixin, priceMixin, customFieldMixin],

    props: {
      id: {
        default: 'am-step-booking'
      },
      showService: {
        default: true,
        type: Boolean
      },
      passedService: {
        default: () => {},
        type: Object
      },
      passedEntities: {
        default: () => {},
        type: Object
      },
      passedEntitiesRelations: {
        default: () => {},
        type: Object
      }
    },

    data () {
      return {
        showEmptyCalendar: false,
        selectedMonth: moment().format('YYYY-MM'),
        isServiceChanged: true,
        calendarId: '',
        activeConfirm: false,
        activePicker: false,
        addToCalendarData: null,
        appointment: {
          bookingStart: '',
          bookingStartTime: '',
          bookings: [{
            customer: {
              email: '',
              externalId: null,
              firstName: '',
              id: null,
              lastName: '',
              phone: ''
            },
            customFields: {},
            customerId: 0,
            extras: [],
            persons: 1,
            status: this.$root.settings.general.defaultAppointmentStatus
          }],
          duration: 0,
          group: false,
          notifyParticipants: this.$root.settings.notifications.notifyCustomers,
          payment: {
            amount: 0,
            gateway: '',
            data: {}
          },
          categoryId: null,
          providerId: 0,
          serviceId: null,
          locationId: null
        },
        availableDates: [],
        availableTimeSlots: [],
        calendar: '',
        calendarTimeSlots: {},
        calendarVisible: false,
        customer: {
          name: '',
          email: '',
          phone: '',
          paymentMethod: ''
        },
        customerRules: {
          name: [
            {required: true, message: 'Please input name', trigger: 'submit'},
            {min: 3, max: 50, message: 'Length should be 3 to 50', trigger: 'submit'}],
          email: [
            {required: true, message: 'Please input name', trigger: 'submit'},
            {min: 3, max: 5, message: 'Length should be 3 to 5', trigger: 'submit'}],
          phone: '',
          paymentMethod: ''
        },
        disabledAttribute: {
          contentStyle: {
            color: '#ccc',
            opacity: 0.4,
            textDecoration: 'line-through'
          }
        },
        disabledWeekdays: null,
        fetched: false,
        fetchedSlots: false,
        group: {
          allowed: false,
          enabled: false,
          count: 1,
          options: []
        },
        loadingTimeSlots: false,
        options: {
          availableEntitiesIds: {
            categories: [],
            employees: [],
            locations: [],
            services: []
          },
          entitiesRelations: {},
          entities: {
            services: [],
            employees: [],
            locations: [],
            customFields: []
          }
        },
        rules: {
          serviceId: [
            {
              required: true,
              message: this.$root.labels.please_select + ' ' + this.$root.labels.service,
              trigger: 'blur',
              type: 'number'
            }
          ]
        },
        selectedExtras: [],
        selectedDate: null,
        showAddToCalendar: false,
        showExtras: false,
        showFilters: false,
        showTimes: false,
        showServices: false,
        showEmployees: false,
        showLocations: false,
        showCalendarBackButton: false,
        showCalendarContinueButton: false,
        times: ''
      }
    },

    created () {
      this.calendarId = 'am-appointment-times' + this.$root.shortcodeData.counter
      window.addEventListener('resize', this.handleResize)
    },

    mounted () {
      if (!this.$root.shortcodeData.hasBookingShortcode || !this.$root.shortcodeData.hasCategoryShortcode) {
        this.inlineBookingSVG()
      }

      // Customization hook
      if ('beforeBookingLoaded' in window) {
        window.beforeBookingLoaded()
      }

      if (this.passedEntities) {
        this.options.isFrontEnd = true
        this.options.entitiesRelations = Object.assign({}, this.passedEntitiesRelations)

        let shortCodeEntitiesIds = this.getShortCodeEntityIds()

        this.filterEntities(this.passedEntities, {
          categoryId: this.passedService.categoryId,
          serviceId: this.passedService.id,
          employeeId: shortCodeEntitiesIds.employeeId,
          locationId: shortCodeEntitiesIds.locationId
        })

        this.fetchedEntities()
      } else {
        let $this = this

        this.fetchEntities(function (success) {
          if (success) {
            $this.fetchedEntities()
          }
        }, {
          types: ['categories', 'employees'],
          isFrontEnd: true
        })
      }

      this.getCurrentUser()
      this.times = document.getElementById(this.calendarId)
    },

    updated () {
      this.handleResize()
    },

    methods: {
      getBookableData () {
        let bookingService = this.getProviderById(this.appointment.providerId).serviceList.find(service => service.id === this.appointment.serviceId)

        return {
          id: bookingService.id,
          name: bookingService.name,
          price: bookingService.price,
          maxCapacity: bookingService.maxCapacity,
          pictureThumbPath: bookingService.pictureThumbPath,
          aggregatedPrice: bookingService.aggregatedPrice,
          bookingStart: this.appointment.bookingStart,
          bookingStartTime: this.appointment.bookingStartTime
        }
      },

      changeMonth (page) {
        this.selectedMonth = page ? moment().year(page.year).month(page.month - 1).date(1).format('YYYY-MM') : null
      },

      showCalendarOnly (initCall) {
        let providerService = []

        if (this.appointment.serviceId && this.appointment.providerId) {
          providerService = this.getProviderById(this.appointment.providerId).serviceList.find(
            service => service.id === this.appointment.serviceId
          )
        }

        return initCall &&
          !this.showServices &&
          !this.showEmployees &&
          !this.showLocations &&
          providerService &&
          (typeof providerService !== 'undefined') &&
          (providerService.maxCapacity === 1 || providerService.bringingAnyone === false) &&
          this.getServiceById(this.appointment.serviceId).extras.length === 0
      },

      changeService () {
        this.isServiceChanged = true

        this.clearValidation()

        this.appointment.bookings[0].extras = []

        this.handleCapacity()

        if (this.calendarVisible) {
          this.getTimeSlots()
        }
      },

      changeEmployee () {
        this.clearValidation()

        this.handleCapacity()

        if (this.calendarVisible) {
          this.getTimeSlots()
        }
      },

      changeLocation () {
        this.clearValidation()

        if (this.calendarVisible) {
          this.getTimeSlots()
        }
      },

      enableGrouping () {
        this.handleCapacity()

        this.group.enabled === true ? this.appointment.bookings[0].persons += 1 : this.appointment.bookings[0].persons = 1

        if (this.calendarVisible) {
          this.getTimeSlots()
        }
      },

      changeNumberOfPersons () {
        if (this.calendarVisible) { this.getTimeSlots() }
      },

      getSelectedExtraMaxQuantity (selectedExtra) {
        let extra = this.getServiceById(this.appointment.serviceId).extras.find(extra => extra.id === selectedExtra.id)

        return typeof extra !== 'undefined' ? extra.maxQuantity : ''
      },

      getAvailableExtras (selectedExtra) {
        let selectedExtras = []
        let availableExtras = []

        this.selectedExtras.forEach(function (extra) {
          if (extra.id) {
            selectedExtras.push(extra.id)
          }
        })

        this.getServiceById(this.appointment.serviceId).extras.forEach(function (extra) {
          if (selectedExtras.indexOf(extra.id) === -1 || selectedExtra.id === extra.id) {
            availableExtras.push(extra)
          }
        })

        return availableExtras
      },

      addExtra () {
        this.selectedExtras.push({
          id: null,
          extraId: null,
          quantity: '',
          duration: 0,
          name: ''
        })
      },

      deleteExtra (key) {
        if (this.calendarVisible && !!this.selectedExtras[key].duration) {
          this.selectedExtras.splice(key, 1)
          this.getTimeSlots()
        } else {
          this.selectedExtras.splice(key, 1)
        }
      },

      changeSelectedExtra (selectedExtra) {
        selectedExtra.quantity = (selectedExtra.quantity === '') ? 1 : selectedExtra.quantity

        let extra = this.getServiceById(this.appointment.serviceId).extras.find(extra => extra.id === selectedExtra.id)

        selectedExtra.extraId = extra.id
        selectedExtra.duration = extra.duration
        selectedExtra.name = extra.name
        selectedExtra.price = extra.price
        selectedExtra.selected = true

        if (selectedExtra.quantity > extra.maxQuantity) {
          selectedExtra.quantity = extra.maxQuantity
        }

        if (this.calendarVisible && !!extra.duration) { this.getTimeSlots() }
      },

      fetchedEntities () {
        this.setBookingCustomFields()

        if (this.employeesFiltered.length === 1) {
          this.appointment.providerId = this.employeesFiltered[0].id
        } else if (this.employeesFiltered.length > 1) {
          this.showEmployees = true
        } else {
          this.setUnavailableBooking()
          return
        }

        if (this.locationsFiltered.length === 1) {
          this.appointment.locationId = this.locationsFiltered[0].id
        } else if (this.locationsFiltered.length > 1) {
          this.showLocations = true
        }

        if (this.servicesFiltered.length === 1) {
          this.appointment.serviceId = this.servicesFiltered[0].id
        } else if (this.servicesFiltered.length > 1) {
          this.showServices = true
        } else {
          this.setUnavailableBooking()
          return
        }

        if (!this.appointment.providerId && !this.appointment.serviceId && this.appointment.locationId) {
          this.appointment.locationId = null
        }

        this.handleCapacity()

        if (this.showCalendarOnly(true)) {
          document.getElementById(this.id + '-calendar').classList.remove('am-select-service-date-transition')
          this.getTimeSlots()
        } else {
          this.fetched = true
        }
      },

      getTimeSlots () {
        this.$refs.booking.validate((valid) => {
          if (!valid) {
            return false
          }
        })

        if (this.appointment.serviceId) {
          this.loadingTimeSlots = true
          let extras = []

          this.selectedExtras.forEach(function (extra) {
            if (extra.id) {
              extras.push({
                id: extra.id,
                quantity: extra.quantity
              })
            }
          })

          let providerIds = []
          let $this = this

          // If Employee is not selected, select ones that can provide the service
          if (!this.appointment.providerId) {
            // If grouping is enabled check employees capacity for selected service
            if ($this.group.enabled) {
              this.employeesFiltered.forEach(function (employee) {
                if (typeof (employee.serviceList.find(service => service.id === $this.appointment.serviceId && service.maxCapacity >= $this.appointment.bookings[0].persons)) !== 'undefined') {
                  providerIds.push(employee.id)
                }
              })
            } else {
              this.employeesFiltered.forEach(function (employee) {
                if (typeof (employee.serviceList.find(service => service.id === $this.appointment.serviceId)) !== 'undefined') {
                  providerIds.push(employee.id)
                }
              })
            }
          }

          // Customization hook
          if ('afterBookingSelectService' in window) {
            window.afterBookingSelectService(this.appointment, this.getServiceById(this.appointment.serviceId), this.getProviderById(this.appointment.providerId), this.getLocationById(this.appointment.locationId))
          }

          this.$http.get(`${this.$root.getAjaxUrl}/slots`, {
            params: {
              locationId: this.appointment.locationId,
              serviceId: this.appointment.serviceId,
              providerIds: this.appointment.providerId ? [this.appointment.providerId] : providerIds,
              extras: JSON.stringify(extras),
              group: 1,
              persons: this.appointment.bookings[0].persons
            }
          }).then(response => {
            let dateSlots = this.$root.settings.general.showClientTimeZone ? this.getConvertedTimeSlots(response.data.data.slots) : response.data.data.slots

            if (!this.calendarVisible) {
              this.activePicker = !this.activePicker
              document.getElementById(this.id).classList.toggle('am-active-picker', this.activePicker)
            }

            let availableDates = []

            let minDate = null

            Object.keys(dateSlots).forEach(function (dateString) {
              if (minDate === null) {
                minDate = dateString
              }

              availableDates.push(moment(dateString).toDate())
            })

            this.showFirstEventMonth(minDate)

            this.calendarTimeSlots = dateSlots
            this.disabledWeekdays = {weekdays: []}
            this.disabledWeekdays = (availableDates.length === 0) ? {weekdays: [1, 2, 3, 4, 5, 6, 7]} : null
            this.availableDates = availableDates
            this.calendarVisible = true

            if (this.availableDates.length) {
              this.setTimeSlots()
            }

            if (!this.availableDates.length || !this.isSelectedDateAvailable()) {
              this.showTimes = false
              let amContainer = document.getElementById(this.id)
              amContainer.classList.remove('am-show-times')
            }

            let dateIsNotAvailable = !this.availableDates.length || !this.isSelectedDateAvailable()
            let timeIsNotAvailable = (this.appointment.bookingStartTime && this.availableTimeSlots.indexOf(this.appointment.bookingStartTime) === -1)

            if (dateIsNotAvailable || timeIsNotAvailable) {
              if (dateIsNotAvailable) {
                this.selectedDate = null
              }

              this.unSelectTime()
            }

            this.fetchedSlots = true
            this.fetched = true
            this.loadingTimeSlots = false
          }).catch(e => {
            console.log(e.message)
            this.fetchedSlots = true
            this.fetched = true
          })
        }
      },

      showFirstEventMonth (minDate) {
        if (this.isServiceChanged && (
          (this.selectedDate === null && moment(this.selectedMonth).format('YYYY-MM') !== moment(minDate, 'YYYY-MM-DD').format('YYYY-MM')) ||
          (this.selectedDate !== null && moment(this.selectedDate).format('YYYY-MM') !== moment(minDate, 'YYYY-MM-DD').format('YYYY-MM'))
        )) {
          this.selectedDate = moment(minDate).toDate()

          let $this = this

          setTimeout(function () {
            $this.selectedDate = null
          }, 100)
        }

        this.isServiceChanged = false
      },

      selectDate (dayInfo) {
        this.unSelectTime()
        let isDisabled = false

        dayInfo.attributes.forEach(function (attrItem) {
          if (attrItem.hasOwnProperty('key') && attrItem['key'] === 'disabled') {
            isDisabled = true
          }
        })

        if (isDisabled) {
          return
        }

        this.showTimes = false

        let amContainer = document.getElementById(this.id)
        amContainer.classList.remove('am-show-times')

        let weekRow = dayInfo.event.target.parentNode.parentNode.parentNode.parentNode.parentNode
        if (!weekRow.classList.contains('c-week')) {
          weekRow = dayInfo.event.target.parentNode.parentNode.parentNode.parentNode
        }

        if (!weekRow.classList.contains('c-week')) {
          weekRow = dayInfo.event.target.parentNode.parentNode.parentNode
        }

        weekRow.parentNode.insertBefore(this.times, weekRow.nextSibling)

        setTimeout(() => {
          if (this.availableTimeSlots.length && this.selectedDate) {
            this.showTimes = true
            amContainer.classList.add('am-show-times')
          }
        }, 200)
      },

      isSelectedDateAvailable () {
        let momentDate = moment(this.selectedDate)
        return this.calendarTimeSlots.hasOwnProperty(momentDate.format('YYYY-MM-DD'))
      },

      setTimeSlots () {
        let momentDate = moment(this.selectedDate)
        let dateString = momentDate.format('YYYY-MM-DD')

        if (this.isSelectedDateAvailable()) {
          this.availableTimeSlots = Object.keys(this.calendarTimeSlots[dateString])
          this.appointment.duration = this.getAppointmentDuration(this.getServiceById(this.appointment.serviceId), this.selectedExtras)
        }
      },

      togglePicker () {
        this.calendarVisible = false
        this.activePicker = !this.activePicker
        let amContainer = document.getElementById(this.id)
        amContainer.classList.toggle('am-active-picker', this.activePicker)
      },

      selectTime () {
        this.appointment.bookingStart = moment(this.selectedDate).format('YYYY-MM-DD') + ' ' + this.appointment.bookingStartTime
        this.showCalendarContinueButton = true

        if ('ameliaBooking' in window && 'disableScrollView' in window.ameliaBooking && window.ameliaBooking.disableScrollView === true) {
          return
        }

        this.scrollView('am-button-wrapper')
      },

      unSelectTime () {
        this.appointment.bookingStartTime = null
        this.showCalendarContinueButton = false
      },

      refreshCalendar () {
        let calendarTimeSlots = []
        let availableDates = []

        for (let dateKey in this.calendarTimeSlots) {
          for (let timeKey in this.calendarTimeSlots[dateKey]) {
            for (let slotInfoKey in this.calendarTimeSlots[dateKey][timeKey]) {
              if (this.appointment.providerId && this.calendarTimeSlots[dateKey][timeKey][slotInfoKey][0] === this.appointment.providerId) {
                if (!(dateKey in calendarTimeSlots)) {
                  availableDates.push(moment(dateKey).toDate())
                  calendarTimeSlots[dateKey] = {}
                }

                if (!(timeKey in calendarTimeSlots[dateKey])) {
                  calendarTimeSlots[dateKey][timeKey] = []
                }

                calendarTimeSlots[dateKey][timeKey].push(this.calendarTimeSlots[dateKey][timeKey][slotInfoKey])
              }
            }
          }
        }

        this.calendarTimeSlots = calendarTimeSlots
        this.disabledWeekdays = {weekdays: []}
        this.disabledWeekdays = (availableDates.length === 0) ? {weekdays: [1, 2, 3, 4, 5, 6, 7]} : null
        this.availableDates = availableDates
        this.availableTimeSlots = Object.keys(calendarTimeSlots[moment(this.selectedDate).format('YYYY-MM-DD')])
      },

      showConfirmBookingDialog () {
        let freeSlotEmployees = this.calendarTimeSlots[moment(this.selectedDate).format('YYYY-MM-DD')][this.appointment.bookingStartTime]

        let randomlySelectedEmployeeIndex = Math.floor(Math.random() * (freeSlotEmployees.length) + 1)

        if (!this.appointment.providerId) {
          this.appointment.providerId = this.calendarTimeSlots[moment(this.selectedDate).format('YYYY-MM-DD')][this.appointment.bookingStartTime][randomlySelectedEmployeeIndex - 1][0]
        }

        if (!this.appointment.locationId) {
          this.appointment.locationId = this.calendarTimeSlots[moment(this.selectedDate).format('YYYY-MM-DD')][this.appointment.bookingStartTime][randomlySelectedEmployeeIndex - 1][1]
        }

        this.refreshCalendar()

        this.appointment.bookings[0].extras = this.selectedExtras

        // Customization hook
        if ('afterBookingSelectDateAndTime' in window) {
          window.afterBookingSelectDateAndTime(this.appointment, this.getServiceById(this.appointment.serviceId), this.getProviderById(this.appointment.providerId), this.getLocationById(this.appointment.locationId))
        }

        this.activeConfirm = true
        let amContainer = document.getElementById(this.id)
        setTimeout(() => {
          amContainer.classList.toggle('am-active-confirm', this.activeConfirm)
        }, 1000)
      },

      cancelBooking () {
        this.activeConfirm = false
        let amContainer = document.getElementById(this.id)
        amContainer.classList.toggle('am-active-confirm', this.activeConfirm)
        if (this.showCalendarOnly(true)) {
          amContainer.classList.add('am-mobile-collapsed')
          amContainer.classList.remove('am-desktop')
        }
      },

      inlineBookingSVG () {
        let inlineSVG = require('inline-svg')
        inlineSVG.init({
          svgSelector: 'img.svg-booking',
          initClass: 'js-inlinesvg'
        })
      },

      setUnavailableBooking () {
        this.showEmptyCalendar = true
        this.appointment.locationId = null
        this.group.options = []
        this.group.enabled = false
        this.group.allowed = false
        this.activePicker = !this.activePicker
        this.calendarTimeSlots = {}
        this.disabledWeekdays = {weekdays: [1, 2, 3, 4, 5, 6, 7]}
        this.availableDates = []
        this.calendarVisible = true
        this.fetchedSlots = true
        this.fetched = true
        this.loadingTimeSlots = false
        document.getElementById(this.id + '-calendar').classList.remove('am-select-service-date-transition')
        let amContainer = document.getElementById(this.id)
        amContainer.classList.toggle('am-active-picker', true)
        amContainer.classList.toggle('am-active-confirm', false)
        this.showCalendarBackButton = false
        this.showEmptyCalendar = true
      },

      handleResize () {
        let amContainer = document.getElementById(this.id)

        if (this.showEmptyCalendar || this.showCalendarOnly(true)) {
          amContainer.classList.add('am-mobile-collapsed')
          amContainer.classList.remove('am-desktop')
          this.showCalendarBackButton = false

          return
        }

        if (amContainer) {
          let amContainerWidth = amContainer.offsetWidth

          if (this.showCalendarOnly(false)) {
            amContainer.classList.add('am-mobile-collapsed')
            amContainer.classList.remove('am-desktop')
            this.showCalendarBackButton = false
          } else {
            if (amContainerWidth < 670) {
              amContainer.classList.add('am-mobile-collapsed')
              amContainer.classList.remove('am-desktop')
              this.showCalendarBackButton = true
            } else {
              amContainer.classList.add('am-desktop')
              amContainer.classList.remove('am-mobile-collapsed')
              this.showCalendarBackButton = false
            }
          }
        }
      },

      confirmedBooking (responseData) {
        this.$http.post(`${this.$root.getAjaxUrl}/bookings/success/` + responseData.booking.id, {
          type: 'appointment',
          appointmentStatusChanged: responseData.appointmentStatusChanged
        }).then(response => {
        }).catch(e => {
        })

        // Customization hook
        if ('beforeConfirmedBooking' in window) {
          window.beforeConfirmedBooking()
        } else {
          let dates = []

          responseData.utcTime.forEach(function (date) {
            dates.push(
              {
                start: moment.utc(date.start.replace(/ /g, 'T')).toDate(),
                end: moment.utc(date.end.replace(/ /g, 'T')).toDate()
              }
            )
          })

          let service = this.getServiceById(this.appointment.serviceId)
          let location = this.getLocationById(this.appointment.locationId)

          this.addToCalendarData = {
            title: service.name,
            dates: dates,
            address: location !== null ? location.address : '',
            description: service.description,
            id: responseData.booking.id,
            status: responseData.appointment.bookings[0].status,
            active: this.$root.settings.general.addToCalendar,
            color: responseData.color,
            type: responseData.type
          }
          this.showAddToCalendar = true
        }
      },

      clearValidation () {
        if (typeof this.$refs.booking !== 'undefined') {
          this.$refs.booking.clearValidate()
        }
      },

      closeDialogAddToCalendar () {
        this.showAddToCalendar = false
        document.getElementsByClassName('amelia-app-booking')[0].style.display = 'none'
        window.location.reload()
      }
    },

    computed: {
    },

    components: {
      moment,
      PhoneInput,
      ConfirmBooking,
      AddToCalendar
    }

  }
</script>
