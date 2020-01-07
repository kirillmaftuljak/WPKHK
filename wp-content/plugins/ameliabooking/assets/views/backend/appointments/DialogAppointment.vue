<template>
  <div>

    <!-- Dialog Loader -->
    <div class="am-dialog-loader" v-show="dialogLoading">
      <div class="am-dialog-loader-content">
        <img :src="$root.getUrl+'public/img/spinner.svg'" class=""/>
        <p>{{ $root.labels.loader_message }}</p>
      </div>
    </div>

    <!-- Dialog Content -->
    <div class="am-dialog-scrollable" :class="{'am-edit':appointment.id !== 0}" v-if="appointment && !dialogLoading">

      <!-- Dialog Header -->
      <div class="am-dialog-header">
        <el-row>
          <el-col :span="18">
            <h2 v-if="appointment.id !== 0">{{ $root.labels.edit_appointment }}</h2>
            <h2 v-else>{{ $root.labels.new_appointment }}</h2>
          </el-col>
          <el-col :span="6" class="align-right">
            <el-button @click="closeDialog" class="am-dialog-close" size="small" icon="el-icon-close"></el-button>
          </el-col>
        </el-row>
      </div>

      <!-- Form -->
      <el-form v-if="mounted" :model="appointment" ref="appointment" :rules="rules" label-position="top">
        <el-tabs v-model="newAppointmentTabs">

          <!-- Schedule -->
          <el-tab-pane :label="$root.labels.schedule" name="schedule">

            <!-- Customer -->
            <el-form-item :label="$root.labels.customers_singular_plural + ':'" prop="bookings" v-if="this.$root.settings.role !== 'customer'">
              <el-select
                  v-model="appointment.bookings"
                  value-key="customer.id"
                  multiple
                  :multiple-limit="customersMaxLimit"
                  :placeholder="$root.labels.select_customers"
                  class="no-tags"
                  @change="handleCustomerChange"
                  collapse-tags
                  filterable
              >
                <div class="am-drop">
                  <div class="am-drop-create-item" @click="showDialogNewCustomer" v-if="this.$root.settings.role !== 'provider'">
                    {{ $root.labels.create_new }}
                  </div>
                  <el-option
                      v-for="item in bookings"
                      :key="item.customer.id"
                      :label="(user = getCustomerInfo(item)) !== null ? user.firstName + ' ' + user.lastName : ''"
                      :value="item"
                      class="am-has-option-meta"
                  >
                    <span class="am-drop-item-name">{{ item.customer.firstName }} {{ item.customer.lastName }}</span>
                    <span class="am-drop-item-meta">{{ item.customer.email }}</span>
                  </el-option>
                  <el-option
                      v-if="bookings.length === 0"
                      v-for="item in [{customer: {id: 0, firstName: '', lastName: '', email: '', info: JSON.stringify({firstName: '', lastName: '', email: '', phone: ''})}}]"
                      :key="item.customer.id"
                      :label="(user = getCustomerInfo(item)) !== null ? user.firstName + ' ' + user.lastName : ''"
                      :value="item"
                      class="am-has-option-meta"
                      :style="{'display': 'none'}"
                  >
                  </el-option>
                </div>
              </el-select>
            </el-form-item>

            <transition name="fade">
              <div v-if="appointment.bookings.length > 0 && this.$root.settings.role !== 'customer'" class="am-selected-dropdown-items">
                <el-form-item :label="selectedCustomersMessage"></el-form-item>

                <!-- Selected Customers -->
                <div v-for="(booking, index) in appointment.bookings" :key="index"
                     class="am-selected-dropdown-item">
                  <el-row align="middle" :gutter="4" justify="left">

                    <!-- Selected Customer Name & Email -->
                    <el-col :sm="14">
                      <h3>{{ (user = getCustomerInfo(booking)) !== null ? user.firstName + ' ' + user.lastName : ''
                        }}</h3>
                      <span>{{ booking.customer.email }}</span>
                    </el-col>

                    <!-- Selected Customer Status -->
                    <el-col :sm="9" class="am-align-right">
                      <div class="am-appointment-status small">
                        <span class="am-appointment-status-symbol" :class="booking.status"></span>
                        <el-select
                            v-model="booking.status"
                            @change="handleBookingChange"
                        >
                          <el-option
                              v-for="item in statuses"
                              :key="item.value"
                              :value="item.value">
                            <span class="am-appointment-status-symbol" :class="item.value"></span>
                          </el-option>
                        </el-select>
                      </div>

                      <!-- Selected Customer Number Of Persons -->
                      <div class="am-appointment-persons small">
                        <img slot="prefix" width="16px"
                             :src="$root.getUrl+'public/img/group.svg'" class="svg"/>
                        <el-select
                            v-model="booking.persons"
                            @change="handleBookingChange"
                            class="small-status"
                            :no-data-text="$root.labels.choose_a_group_service"
                        >
                          <el-option
                              v-for="n in appointment.providerServiceMaxCapacity"
                              :key="n"
                              :value="n"
                          >
                          </el-option>
                        </el-select>
                        <el-tooltip placement="top">
                          <div slot="content" v-html="$root.labels.customers_tooltip"></div>
                          <i class="el-icon-question am-tooltip-icon"></i>
                        </el-tooltip>
                      </div>
                    </el-col>

                    <!-- Selected Customer Remove -->
                    <i class="el-icon-close remove" @click="handleCustomerRemove(index)"></i>

                  </el-row>
                </div>

                <!-- Change Group Status -->
                <div v-if="appointment.bookings.length > 1" class="group-status-change">
                  <el-row :gutter="4">

                    <!-- Change Group Status Label -->
                    <el-col :sm="14">
                      <h3>{{ $root.labels.change_group_status }}</h3>
                    </el-col>

                    <!-- Change Group Status Selectbox -->
                    <el-col :sm="10">
                      <el-form-item>
                        <div class="am-appointment-status">
                          <span class="am-appointment-status-symbol" :class="appointment.status"></span>
                          <el-select
                              v-model="appointment.status"
                              @change="handleGroupStatusChange"
                              @visible-change="handleSelected"
                          >
                            <el-option
                                v-for="opt in statuses"
                                :key="opt.value"
                                :label="opt.label"
                                :value="opt.value"
                                class="am-appointment-status-option"
                            >
                              <span class="am-appointment-status-symbol" :class="opt.value">{{ opt.label }}</span>
                            </el-option>
                          </el-select>
                        </div>
                      </el-form-item>
                    </el-col>

                  </el-row>
                </div>

              </div>
            </transition>

            <!-- Service Category -->
            <el-form-item :label="$root.labels.service_category + ':'" :class="{active:categorySpinnerActive}">
              <el-select
                  v-model="appointment.categoryId"
                  filterable
                  clearable
                  :placeholder="$root.labels.select_service_category"
                  @change="handleCategoryChange"
                  :disabled="$root.settings.role === 'customer'"
              >
                <el-option
                    v-for="item in categoriesFiltered"
                    :disabled="item.disabled"
                    :key="item.id"
                    :label="item.name"
                    :value="item.id">
                </el-option>
              </el-select>
              <img :src="$root.getUrl+'public/img/oval-spinner.svg'" class="svg is-spinner"/>
            </el-form-item>

            <!-- Service -->
            <el-form-item :label="$root.labels.service + ':'" :class="{active:serviceSpinnerActive}" prop="serviceId">
              <el-select
                  v-model="appointment.serviceId"
                  filterable
                  clearable
                  :placeholder="$root.labels.select_service + ':'"
                  @change="handleServiceChange"
                  :disabled="$root.settings.role === 'customer'"
              >
                <el-option
                    v-for="item in servicesFiltered"
                    :disabled="item.disabled"
                    :key="item.id"
                    :label="item.name"
                    :value="item.id">
                </el-option>
              </el-select>
              <img :src="$root.getUrl+'public/img/oval-spinner.svg'" class="svg is-spinner"/>
            </el-form-item>

            <!-- Location -->
            <el-form-item
                :label="$root.labels.location + ':'"
                :class="{active:locationSpinnerActive}"
                v-if="locationsFiltered.length"
                :disabled="$root.settings.role === 'customer'"
            >
              <el-select
                  v-model="appointment.locationId"
                  filterable
                  clearable
                  :placeholder="$root.labels.select_location"
                  @change="handleLocationChange"

              >
                <el-option
                    v-for="item in locationsFiltered"
                    :disabled="item.disabled"
                    :key="item.id"
                    :label="item.name"
                    :value="item.id">
                </el-option>
              </el-select>
              <img :src="$root.getUrl+'public/img/oval-spinner.svg'" class="svg is-spinner"/>
            </el-form-item>

            <!-- Employee -->
            <el-form-item :label="$root.labels.employee + ':'" :class="{active:employeeSpinnerActive}"
                          prop="providerId" v-if="!$root.isLite">
              <el-select
                  v-model="appointment.providerId"
                  filterable
                  clearable
                  :placeholder="$root.labels.select_employee"
                  @change="handleEmployeeChange"
                  :disabled="$root.settings.role === 'customer'"
              >
                <el-option
                    v-for="item in employeesFiltered"
                    :disabled="item.disabled"
                    :key="item.id"
                    :label="item.firstName + ' ' + item.lastName"
                    :value="item.id">
                </el-option>
              </el-select>
              <img :src="$root.getUrl+'public/img/oval-spinner.svg'" class="svg is-spinner"/>
            </el-form-item>

            <!-- Date & Time -->
            <el-row :gutter="20">

              <!-- Date -->
              <el-col :span="12" class="v-calendar-column">
                <el-form-item
                    :label="$root.labels.date + ':'"
                    prop="selectedDate"
                    :class="{active: loadingTimeSlots}"
                >
                  <v-date-picker
                      v-show="!loadingTimeSlots"
                      @input="dateChange"
                      v-model="appointment.selectedDate"
                      mode='single'
                      popover-visibility="focus"
                      popover-direction="top"
                      tint-color='#1A84EE'
                      :show-day-popover=false
                      :input-props='{class: "el-input__inner"}'
                      :available-dates="availableDates"
                      :is-expanded=false
                      :is-required=true
                      :disabled-dates='disabledWeekdays'
                      :disabled=false
                      :formats="vCalendarFormats"
                  >
                  </v-date-picker>
                  <el-input
                      v-show="loadingTimeSlots"
                      :placeholder="appointment.selectedDate ? getFrontedFormattedDate(appointment.selectedDate) : this.momentDateFormat"
                      :disabled=true
                  >
                  </el-input>
                  <img :src="$root.getUrl+'public/img/oval-spinner.svg'" class="svg is-spinner is-spinner-right"/>
                </el-form-item>
              </el-col>

              <!-- Time -->
              <el-col :span="12">
                <el-form-item
                    :label="$root.labels.time + ':'"
                    prop="selectedPeriod.time"
                    :class="{active: loadingTimeSlots}"
                >
                  <el-select
                      v-model="appointment.selectedPeriod"
                      value-key="time"
                      filterable
                      :placeholder="$root.labels.select_time"
                      @change="selectedTime()"
                      :disabled="loadingTimeSlots"
                  >
                    <el-option
                        v-for="item in appointment.dateTimeSlots"
                        :key="item.time"
                        :label="getFrontedFormattedTime(item.time + ':00')"
                        :value="item">
                    </el-option>
                  </el-select>
                  <img :src="$root.getUrl+'public/img/oval-spinner.svg'" class="svg is-spinner"/>
                </el-form-item>
              </el-col>
            </el-row>

            <!-- Notify Participants -->
            <el-form-item v-if="this.$root.settings.role !== 'customer'">
              <el-checkbox
                  v-model="appointment.notifyParticipants"
                  @change="clearValidation()"
              >
                {{ $root.labels.notify_customers }}
                <el-tooltip placement="top">
                  <div slot="content" v-html="$root.labels.notify_customers_tooltip"></div>
                  <i class="el-icon-question am-tooltip-icon"></i>
                </el-tooltip>
              </el-checkbox>
            </el-form-item>

            <!-- Note -->
            <div class="am-divider" v-if="this.$root.settings.role !== 'customer'"></div>
            <el-form-item :label="$root.labels.note_internal + ':'" v-if="this.$root.settings.role !== 'customer'">
              <el-input
                  type="textarea"
                  :autosize="{ minRows: 4, maxRows: 6}"
                  placeholder=""
                  v-model="appointment.internalNotes"
                  @input="clearValidation()"
              >
              </el-input>
            </el-form-item>
          </el-tab-pane>

          <!-- Extras -->
          <el-tab-pane name="extras" v-if="this.$root.settings.role !== 'customer'">
            <BlockLite/>

            <span slot="label">{{ $root.labels.extras }}
              <el-badge
                  v-if="appointment.serviceId && appointment.bookings.length > 0 && appointment.extrasSelectedCount > 0 && !$root.isLite"
                  class="mark" :value="appointment.extrasSelectedCount"
              >
              </el-badge>
            </span>
            <div class="am-dialog-table" :class="{'am-lite-container-disabled': $root.isLite}">
              <div
                  v-if="appointment.providerId && appointment.serviceId && appointment.extrasCount > 0 && appointment.bookings.length > 0 && !$root.isLite">
                <div v-for="(booking, index) in appointment.bookings" :key="index" class="am-customer-extras"
                     v-if="['approved', 'pending'].includes(booking.status)">
                  <el-row class="am-customer-extras-data">
                    <el-col>
                      <h3>{{ booking.customer.firstName }} {{ booking.customer.lastName }}</h3>
                      <span>{{ booking.customer.email }}</span>
                    </el-col>
                  </el-row>
                  <el-row :gutter="10" v-for="item in booking.extras" :key="item.extraId">
                    <el-col :span="2">
                      <el-checkbox
                          v-model="item.selected"
                          @change="handleExtrasSelectionChange(item)"
                      >
                      </el-checkbox>
                    </el-col>
                    <el-col :span="10"><span>{{ item.name }}</span></el-col>
                    <el-col :span="7">
                      <el-input-number
                          type="number"
                          v-model="item.quantity"
                          :value="item.quantity"
                          :disabled="!item.selected"
                          @change="handleExtrasSelectionChange(item)"
                          :min="1"
                          :max="item.maxQuantity"
                          size="small"
                      >

                      </el-input-number>
                    </el-col>
                    <el-col :span="5" class="align-right">{{ getFormattedPrice(item.price) }}</el-col>
                  </el-row>
                  <el-row :gutter="10" class="subtotal">
                    <el-col :span="14" class="align-right">
                      {{ $root.labels.subtotal }}:
                    </el-col>
                    <el-col :span="10" class="align-right">
                      {{ getFormattedPrice(booking.extrasTotalPrice) }}
                    </el-col>
                  </el-row>
                </div>
                <div class="total">
                  <el-row :gutter="10">
                    <el-col :span="14" class="align-right">{{ $root.labels.service_price }}:</el-col>
                    <el-col :span="10" class="align-right ">{{ getFormattedPrice(appointment.serviceTotalPrice) }}
                    </el-col>
                  </el-row>
                  <el-row :gutter="10">
                    <el-col :span="14" class="align-right">{{ $root.labels.extras }}:</el-col>
                    <el-col :span="10" class="align-right ">{{ getFormattedPrice(appointment.extrasTotalPrice) }}
                    </el-col>
                  </el-row>
                  <el-row class="am-strong" :gutter="10">
                    <el-col :span="14" class="align-right">{{ $root.labels.total }}:</el-col>
                    <el-col :span="10" class="align-right ">{{ getAppointmentPrice(savedAppointment ? savedAppointment.serviceId : appointment.serviceId, appointment.serviceId, appointment.bookings) }}
                    </el-col>
                  </el-row>
                </div>
              </div>
              <div
                  v-else-if="appointment.serviceId && appointment.providerId && appointment.serviceId && appointment.extrasCount === 0">
                <p align="center">{{ $root.labels.service_no_extras }}</p>
              </div>
              <div v-else>
                <p align="center">{{ $root.labels.no_selected_extras_requirements }}</p>
              </div>
            </div>

          </el-tab-pane>

          <!-- Payment -->
          <el-tab-pane :label="$root.labels.payment" name="payment" v-if="appointment.id !== 0 && this.$root.settings.role !== 'customer'">
            <dialog-appointment-payment :appointment="appointment"
                                        @editPayment="editPayment"></dialog-appointment-payment>
          </el-tab-pane>

          <!-- Custom Fields -->
          <el-tab-pane :label="$root.labels.custom_fields" name="customFields" v-if="showCustomFieldsTab() && this.$root.settings.role !== 'customer'">
            <dialog-custom-fields
                :appointment="appointment"
                :entityId="appointment.serviceId"
                entityType="appointment"
                :customFields="this.options.entities.customFields"
                :showCustomerInfo="true"
                @clearValidation="clearValidation"
            >
            </dialog-custom-fields>
          </el-tab-pane>

        </el-tabs>

      </el-form>
    </div>

    <!-- Dialog Actions -->
    <dialog-actions
        v-if="appointment && !dialogLoading && this.$root.settings.role !== 'customer'"
        formName="appointment"
        urlName="appointments"
        :isNew="appointment.id === 0"
        :entity="appointment"
        :getParsedEntity="getParsedEntity"
        @errorCallback="errorCallback"
        @validationBookingsFailCallback="validationBookingsFailCallback"
        :hasIcons="true"

        :status="{
          on: 'visible',
          off: 'hidden'
        }"

        :action="{
          haveAdd: true,
          haveEdit: true,
          haveStatus: false,
          haveRemove: $root.settings.capabilities.canDelete === true,
          haveRemoveEffect: false,
          haveDuplicate: true
        }"

        :message="{
          success: {
            save: $root.labels.appointment_saved,
            remove: $root.labels.appointment_deleted,
            show: '',
            hide: ''
          },
          confirm: {
            remove: $root.labels.confirm_delete_appointment,
            show: '',
            hide: '',
            duplicate: $root.labels.confirm_duplicate_appointment
          }
        }"
    >
    </dialog-actions>

    <div>
      <div class="am-dialog-footer" v-if="this.$root.settings.role === 'customer'">
        <div class="am-dialog-footer-actions">
          <el-row>
            <el-col :sm="24" class="align-right">
              <el-button type="primary" @click="updateByCustomer" class="am-dialog-create">
                {{ $root.labels.save }}
              </el-button>
            </el-col>
          </el-row>
        </div>
      </div>

      <!-- Dialog Loader -->
      <div class="am-dialog-loader" v-show="dialogLoading">
        <div class="am-dialog-loader-content">
          <img :src="$root.getUrl+'public/img/spinner.svg'" class="">
          <p>{{ $root.labels.loader_message }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import DialogActions from '../parts/DialogActions.vue'
  import imageMixin from '../../../js/common/mixins/imageMixin'
  import dateMixin from '../../../js/common/mixins/dateMixin'
  import notifyMixin from '../../../js/backend/mixins/notifyMixin'
  import entitiesMixin from '../../../js/common/mixins/entitiesMixin'
  import appointmentPriceMixin from '../../../js/backend/mixins/appointmentPriceMixin'
  import priceMixin from '../../../js/common/mixins/priceMixin'
  import DialogAppointmentPayment from './DialogAppointmentPayment'
  import DialogCustomFields from '../parts/DialogCustomFields'
  import customFieldMixin from '../../../js/common/mixins/customFieldMixin'
  import moment from 'moment'

  export default {

    mixins: [entitiesMixin, imageMixin, dateMixin, notifyMixin, priceMixin, customFieldMixin, appointmentPriceMixin],

    props: {
      appointment: null,
      savedAppointment: null,
      bookings: null,
      options: null,
      customerCreatedCount: 0
    },

    data () {
      let validateServiceCapacity = (rule, bookings, callback) => {
        // this.$set(rule, 'type', 'array')
        if (this.appointment.serviceId && this.appointment.providerId) {
          if (this.isProviderService && this.getApprovedPersonsCount() > this.appointment.providerServiceMaxCapacity) {
            this.newAppointmentTabs = 'schedule'
            callback(new Error(this.$root.labels.select_max_customer_count_warning + ' ' + this.appointment.providerServiceMaxCapacity))
          } else {
            callback()
          }
        } else {
          callback()
        }
      }

      let validateScheduleEmpty = (rule, value, callback) => {
        if (!value) {
          this.newAppointmentTabs = 'schedule'
        }

        callback()
      }

      return {
        isProviderService: true,
        availableDates: [],
        categorySpinnerActive: false,
        dialogLoading: true,
        disabledWeekdays: null,
        employeeSpinnerActive: false,
        executeUpdate: true,
        filter: null,
        locationSpinnerActive: false,
        mounted: false,
        newAppointmentTabs: 'schedule',
        serviceSpinnerActive: false,
        statusMessage: '',
        loadingTimeSlots: false,
        payment: {
          amount: 0,
          gateway: 'onSite'
        },
        rulesInit: {
          bookings: [
            {required: true, message: this.$root.labels.select_customer_warning, trigger: 'submit', type: 'array'},
            {validator: validateServiceCapacity, trigger: 'submit'}
          ],
          serviceId: [
            {required: true, message: this.$root.labels.select_service_warning, trigger: 'submit', type: 'number'}
          ],
          providerId: [
            {required: true, message: this.$root.labels.select_employee_warning, trigger: 'submit', type: 'number'}
          ],
          selectedDate: [
            {validator: validateScheduleEmpty, trigger: 'submit'},
            {required: true, message: this.$root.labels.select_date_warning, trigger: 'submit', type: 'date'}
          ],
          'selectedPeriod.time': [
            {validator: validateScheduleEmpty, trigger: 'submit'},
            {required: true, message: this.$root.labels.select_time_warning, trigger: 'submit'}
          ]
        },
        rules: {},
        statuses: [
          {
            id: 1,
            value: 'approved',
            label: this.$root.labels.approved

          }, {
            id: 0,
            value: 'pending',
            label: this.$root.labels.pending

          },
          {
            id: 2,
            value: 'canceled',
            label: this.$root.labels.canceled

          },
          {
            id: 3,
            value: 'rejected',
            label: this.$root.labels.rejected

          }
        ]
      }
    },

    mounted () {
      this.rules = this.rulesInit
      this.instantiateDialog()
    },

    updated () {
      this.instantiateDialog()
    },

    methods: {
      selectedTime () {
        let $this = this

        let selectedDateString = this.getStringFromDate(this.appointment.selectedDate)

        if (selectedDateString in this.appointment.calendarTimeSlots && this.appointment.selectedPeriod.time in this.appointment.calendarTimeSlots[selectedDateString]) {
          this.appointment.calendarTimeSlots[selectedDateString][this.appointment.selectedPeriod.time].forEach(function (employeeLocation) {
            if (employeeLocation[0] === $this.appointment.providerId) {
              $this.appointment.locationId = employeeLocation[1]
            }
          })
        }

        this.clearValidation()
      },

      updateByCustomer () {
        this.dialogLoading = true

        let bookingStart = this.getStringFromDate(this.appointment.selectedDate) + ' ' + this.appointment.selectedPeriod.time

        this.$http.post(`${this.$root.getAjaxUrl}/appointments/time/${this.appointment.id}`, {
          'bookingStart': bookingStart
        })
          .then(response => {
            this.$emit('saveCallback', response)

            setTimeout(() => {
              this.dialogLoading = false
              this.$emit('closeDialog')
            }, 300)
            this.notify(this.$root.labels.success, this.$root.labels.appointment_rescheduled, 'success')
          })
          .catch(e => {
            if (e.response) {
              this.dialogLoading = false

              let $this = this

              setTimeout(function () {
                if ('timeSlotUnavailable' in e.response.data.data && e.response.data.data.timeSlotUnavailable === true) {
                  $this.notify($this.$root.labels.error, $this.$root.labels.time_slot_unavailable, 'error')
                }

                if ('rescheduleBookingUnavailable' in e.response.data.data && e.response.data.data.rescheduleBookingUnavailable === true) {
                  $this.notify($this.$root.labels.error, $this.$root.labels.booking_reschedule_exception, 'error')
                }
              }, 200)
            }
          })
      },

      editPayment (obj) {
        this.$emit('editPayment', obj)
      },

      instantiateDialog () {
        if ((this.appointment !== null || (this.appointment !== null && this.appointment.id === 0)) && this.executeUpdate === true) {
          if (this.appointment.id !== 0) {
            this.setCategory()
            this.setLocation()
            this.handleCustomerChange()

            this.getTimeSlots(function (timeSlots) {
              let $this = this

              let selectedTimeSlot = $this.appointment.bookingStart.split(' ')
              let selectedDate = selectedTimeSlot[0]
              let selectedTime = selectedTimeSlot[1].slice(0, -3)

              if (!(selectedDate in timeSlots)) {
                timeSlots[selectedDate] = {}
                timeSlots[selectedDate][selectedTime] = [[this.appointment.providerId, this.appointment.locationId]]
              } else if (!(selectedTime in timeSlots[selectedDate])) {
                let keys = Object.keys(timeSlots[selectedDate])

                keys.push(selectedTime)

                let sortedDateTimeSlots = {}

                keys.sort().forEach(function (timeKey) {
                  if (timeKey === selectedTime) {
                    sortedDateTimeSlots[timeKey] = [$this.appointment.providerId]
                  } else {
                    sortedDateTimeSlots[timeKey] = timeSlots[selectedDate][timeKey]
                  }
                })

                timeSlots[selectedDate] = sortedDateTimeSlots
              }

              this.appointment.selectedDate = moment(selectedDate).toDate()
              this.appointment.selectedPeriod = {
                time: selectedTime,
                employee: $this.appointment.providerId
              }

              this.updateCalendar(timeSlots)
            }.bind(this))
          } else if (this.appointment.id === 0 && this.appointment.serviceId !== '') {
            this.setCategory()
            this.setLocation()
            this.getTimeSlots(this.updateCalendar)
          }

          this.mounted = true
          this.executeUpdate = false

          if (this.appointment !== null && this.appointment.id === 0) {
            this.dialogLoading = false
          }
        }
      },

      setCategory () {
        this.appointment.categoryId = this.options.entities.services.filter(
          service => service.id === this.appointment.serviceId
        )[0].categoryId
      },

      setLocation () {
        this.appointment.locationId = this.appointment.locationId ? this.appointment.locationId : this.options.entities.employees.filter(employee => this.appointment.providerId === employee.id)[0].locationId
      },

      closeDialog () {
        this.$emit('closeDialog')
      },

      getParsedEntity () {
        let bookings = []

        this.appointment.bookings.forEach(function (bookItem) {
          let extras = []

          bookItem.extras.forEach(function (extItem) {
            if (extItem.selected) {
              extras.push({
                id: extItem.id,
                customerBookingId: bookItem.id,
                extraId: extItem.extraId,
                quantity: extItem.quantity,
                price: extItem.price
              })
            }
          })

          bookings.push({
            id: bookItem.id,
            customerId: bookItem.customer.id,
            status: bookItem.status,
            persons: bookItem.persons,
            extras: extras,
            customFields: JSON.stringify(bookItem.customFields),
            coupon: bookItem.coupon
          })
        })

        return {
          'serviceId': this.appointment.serviceId,
          'providerId': this.appointment.providerId,
          'locationId': this.appointment.locationId,
          'bookings': bookings,
          'bookingStart': this.getStringFromDate(this.appointment.selectedDate) + ' ' + this.appointment.selectedPeriod.time,
          'notifyParticipants': this.appointment.notifyParticipants ? 1 : 0,
          'internalNotes': this.appointment.internalNotes,
          'id': this.appointment.id,
          'payment': this.payment
        }
      },

      showDialogNewCustomer () {
        this.$emit('showDialogNewCustomer')
      },

      handleCustomerChange () {
        this.setSelectedExtrasCount()
        this.setServiceExtrasForCustomers(false)
        this.setPrice()
        this.setServiceCapacityForProvider()
        this.setBookingCustomFields()
        this.addCustomFieldsValidationRules()

        this.$emit('sortBookings', this.appointment.bookings)
      },

      handleCustomerRemove (index) {
        this.clearValidation()
        this.appointment.bookings.splice(index, 1)
        this.setPrice()
      },

      setServiceExtrasForCustomers (resetExtras) {
        let $this = this

        let extras = null

        if ($this.appointment.serviceId) {
          $this.options.entities.services.forEach(function (serItem) {
            if (serItem.id === $this.appointment.serviceId) {
              extras = serItem.extras
              $this.appointment.extrasCount = extras.length
            }
          })

          $this.appointment.bookings.forEach(function (bookItem) {
            if (resetExtras || (!bookItem.id && !bookItem.added)) {
              bookItem.extras = JSON.parse(JSON.stringify(extras))
              bookItem.extras.forEach(function (extItem) {
                extItem.selected = false
                extItem.id = 0
                extItem.customerBookingId = 0
              })
            }

            bookItem.added = true
          })

          this.setSelectedExtrasCount()
        }
      },

      handleSelected () {
        let $this = this
        let selected = document.querySelectorAll('.am-appointment-status-option.selected')
        for (let i = 0; i < selected.length; i++) {
          selected[i].addEventListener('click', function (e) {
            $this.handleGroupStatusChange()
          })
        }
      },

      handleGroupStatusChange () {
        this.clearValidation()
        let $this = this

        this.appointment.bookings.forEach(function (bookItem) {
          bookItem.status = $this.appointment.status
        })
      },

      handleEmployeeChange () {
        this.serviceSpinnerActive = true
        this.locationSpinnerActive = true
        this.categorySpinnerActive = true

        this.setServiceExtrasForCustomers(true)
        this.setServiceCapacityForProvider()
        this.setPrice()

        this.getTimeSlots(this.updateCalendar)

        setTimeout(() => {
          this.serviceSpinnerActive = false
          this.locationSpinnerActive = false
          this.categorySpinnerActive = false
        }, 300)
      },

      handleLocationChange () {
        this.clearValidation()
        this.serviceSpinnerActive = true
        this.employeeSpinnerActive = true
        this.categorySpinnerActive = true

        this.getTimeSlots(this.updateCalendar)

        setTimeout(() => {
          this.serviceSpinnerActive = false
          this.employeeSpinnerActive = false
          this.categorySpinnerActive = false
        }, 300)
      },

      handleServiceChange () {
        this.locationSpinnerActive = true
        this.employeeSpinnerActive = true
        this.categorySpinnerActive = true

        this.setServiceCapacityForProvider()
        this.setPrice()
        this.setServiceExtrasForCustomers(true)
        this.addCustomFieldsValidationRules()

        this.getTimeSlots(this.updateCalendar)

        setTimeout(() => {
          this.locationSpinnerActive = false
          this.employeeSpinnerActive = false
          this.categorySpinnerActive = false
        }, 300)
      },

      handleCategoryChange () {
        this.clearValidation()
        this.locationSpinnerActive = true
        this.employeeSpinnerActive = true
        this.serviceSpinnerActive = true

        setTimeout(() => {
          this.locationSpinnerActive = false
          this.employeeSpinnerActive = false
          this.serviceSpinnerActive = false
        }, 300)
      },

      getProviderService () {
        let $this = this
        let serviceProvider = null

        if (this.appointment.providerId && this.appointment.serviceId) {
          this.options.entities.employees.forEach(function (proItem) {
            if (proItem.id === $this.appointment.providerId) {
              proItem.serviceList.forEach(function (proSerItem) {
                if (proSerItem.id === $this.appointment.serviceId) {
                  serviceProvider = proSerItem
                }
              })
            }
          })
        }

        if (this.appointment.id && this.appointment.serviceId && !serviceProvider) {
          this.isProviderService = false
          return this.getServiceById(this.appointment.serviceId)
        }

        return serviceProvider
      },

      setServiceCapacityForProvider () {
        let providerService = this.getProviderService()

        this.appointment.providerServiceMaxCapacity = providerService ? providerService.maxCapacity : 0
        this.appointment.providerServiceMinCapacity = providerService ? providerService.minCapacity : 0

        this.setStatusMessage()
      },

      setStatusMessage () {
        this.statusMessage = this.getApprovedPersonsCount() < this.appointment.providerServiceMinCapacity ? '(minimum ' + this.appointment.providerServiceMinCapacity + ')' : ''
      },

      handleBookingChange () {
        this.setPrice()
        this.setStatusMessage()
      },

      getApprovedPersonsCount () {
        let customersCount = 0

        this.appointment.bookings.forEach(function (bookItem) {
          if (bookItem.status === 'approved') {
            customersCount += bookItem.persons
          }
        })

        return customersCount
      },

      setPrice () {
        this.clearValidation()
        let $this = this

        let isChangedServicePrice = this.appointment && this.savedAppointment ? this.appointment.serviceId !== parseInt(this.savedAppointment.serviceId) : false

        $this.$nextTick(() => {
          if ($this.appointment.serviceId && $this.appointment.providerId && $this.appointment.bookings) {
            let providerService = $this.getProviderService()
            let service = this.getServiceById($this.appointment.serviceId)

            let serviceTotalPrice = 0
            let extrasTotalPrice = 0
            let discountTotalPrice = 0

            $this.appointment.bookings.forEach(function (booking) {
              if (['approved', 'pending'].includes(booking.status)) {
                let bookingExtrasTotalPrice = 0

                let aggregatedPrice = booking.id ? booking.aggregatedPrice : service.aggregatedPrice

                booking.extras.forEach(function (extItem) {
                  if (extItem.selected) {
                    let serviceExtra = service.extras.filter(extra => extra.id === extItem.extraId)

                    let extraPrice = booking.id ? extItem.price : (serviceExtra.length ? serviceExtra[0].price : 0)

                    let aggregatedExtraPrice = extItem.aggregatedPrice === null ? (booking.id ? booking.aggregatedPrice : service.aggregatedPrice) : (booking.id ? extItem.aggregatedPrice : serviceExtra.aggregatedPrice)

                    bookingExtrasTotalPrice += (aggregatedExtraPrice ? booking.persons : 1) * (extItem.quantity ? extItem.quantity : 0) * extraPrice
                  }
                })

                let servicePricePrice = booking.id ? (isChangedServicePrice ? providerService.price : booking.price) : providerService.price

                booking.extrasTotalPrice = bookingExtrasTotalPrice
                booking.serviceTotalPrice = servicePricePrice * (aggregatedPrice ? booking.persons : 1)
                booking.discountTotalPrice = (booking.serviceTotalPrice + booking.extrasTotalPrice) / 100 * (booking.coupon ? booking.coupon.discount : 0) + (booking.coupon ? booking.coupon.deduction : 0)

                serviceTotalPrice += booking.serviceTotalPrice
                extrasTotalPrice += booking.extrasTotalPrice
                discountTotalPrice += booking.discountTotalPrice
              }
            })

            $this.appointment.serviceTotalPrice = serviceTotalPrice
            $this.appointment.extrasTotalPrice = extrasTotalPrice
            $this.appointment.discountTotalPrice = discountTotalPrice
          }
        })
      },

      handleExtrasSelectionChange (item) {
        if (typeof item.quantity === 'undefined') {
          item.quantity = 1
        }

        this.setPrice()
        this.setSelectedExtrasCount()

        if (item.duration > 0) {
          this.getTimeSlots(this.updateCalendar)
        }
      },

      getStringFromDate (date) {
        let year = date.getFullYear()
        let month = ('0'.concat(date.getMonth() + 1)).slice(-2)
        let day = ('0'.concat(date.getDate())).slice(-2)

        return year + '-' + month + '-' + day
      },

      updateCalendar (timeSlots) {
        let $this = this
        this.appointment.calendarTimeSlots = timeSlots

        let availableDates = []

        Object.keys(this.appointment.calendarTimeSlots).forEach(function (dateString) {
          availableDates.push($this.getDate(dateString))
        })

        this.availableDates = availableDates

        this.disabledWeekdays = {weekdays: []}

        this.disabledWeekdays = (this.availableDates.length === 0) ? {weekdays: [1, 2, 3, 4, 5, 6, 7]} : null

        this.dateChange()
      },

      getTimeSlots (callback) {
        let appointment = this.appointment

        let extras = []

        if (appointment.serviceId) {
          this.loadingTimeSlots = true

          this.appointment.bookings.forEach(function (bookItem) {
            bookItem.extras.forEach(function (extItem) {
              if (extItem.selected) {
                extras.push({
                  id: extItem.extraId,
                  quantity: extItem.quantity
                })
              }
            })
          })

          this.$http.get(`${this.$root.getAjaxUrl}/slots`, {
            params: {
              serviceId: appointment.serviceId,
              locationId: appointment.locationId,
              providerIds: appointment.providerId ? [appointment.providerId] : [],
              extras: JSON.stringify(extras),
              excludeAppointmentId: appointment.id,
              group: 0
            }
          })
            .then(response => {
              callback(response.data.data.slots)
              this.dialogLoading = false
              this.loadingTimeSlots = false
            })
            .catch(e => {
              console.log(e.message)
              this.loadingTimeSlots = false
            })
        }
      },

      dateChange () {
        this.clearValidation()
        let $this = this
        let timeSlots = []
        let dateTimeSlots = null
        let selectedPeriodExists = false

        if (this.appointment.selectedDate &&
          this.appointment.calendarTimeSlots &&
          (dateTimeSlots = this.appointment.calendarTimeSlots[this.getStringFromDate(this.appointment.selectedDate)])
        ) {
          Object.keys(dateTimeSlots).forEach(function (key) {
            if ($this.appointment.selectedPeriod && $this.appointment.selectedPeriod.hasOwnProperty('time') && $this.appointment.selectedPeriod.time === key) {
              selectedPeriodExists = true
            }

            timeSlots.push({
              'time': key,
              'employees': dateTimeSlots[key]
            })
          })

          if (!selectedPeriodExists) {
            this.appointment.selectedPeriod = ''
          }
        } else {
          this.appointment.selectedDate = null
          this.appointment.selectedPeriod = ''
        }

        this.appointment.dateTimeSlots = timeSlots
      },

      setSelectedExtrasCount () {
        let extrasSelectedCount = 0

        this.appointment.bookings.forEach(function (bookItem) {
          bookItem.extras.forEach(function (extItem) {
            if (extItem.selected) {
              extrasSelectedCount++
            }
          })
        })

        this.appointment.extrasSelectedCount = extrasSelectedCount
      },

      clearValidation () {
        if (typeof this.$refs.appointment !== 'undefined') {
          this.$refs.appointment.clearValidate()
        }
      },

      errorCallback (responseData) {
        let $this = this

        setTimeout(function () {
          if ('timeSlotUnavailable' in responseData.data && responseData.data.timeSlotUnavailable === true) {
            $this.notify($this.$root.labels.error, $this.$root.labels.time_slot_unavailable, 'error')
            $this.getTimeSlots($this.updateCalendar)
          }
        }, 200)
      },

      addCustomFieldsValidationRules () {
        if (this.appointment.serviceId && this.appointment.bookings.length > 0) {
          this.rules = this.rulesInit

          // Go through all bookings
          for (let i = 0; i < this.appointment.bookings.length; i++) {
            // Go through all custom fields
            for (let j = 0; j < this.options.entities.customFields.length; j++) {
              // Check if custom fields is assigned to selected service
              if (this.isCustomFieldVisible(this.options.entities.customFields[j], 'appointment', this.appointment.serviceId)) {
                if (typeof this.rules.bookings[i] === 'undefined') {
                  this.$set(this.rules.bookings, i, {type: 'array'})
                }

                if (typeof this.rules.bookings[i].customFields === 'undefined') {
                  this.$set(this.rules.bookings[i], 'customFields', {})
                }

                this.rules.bookings[i].customFields[this.options.entities.customFields[j].id] = {
                  value: [
                    {required: true, message: this.$root.labels.required_field, trigger: 'submit'}
                  ]
                }
              }
            }
          }
        }
      },

      showCustomFieldsTab () {
        let servicesIdsWithCustomField = Array.prototype.concat.apply(
          [], this.options.entities.customFields.map(customField => customField.services.map(service => service.id))
        )

        return this.options.entities.customFields.length > 0 &&
          this.appointment.bookings.length > 0 &&
          this.appointment.serviceId &&
          servicesIdsWithCustomField.includes(this.appointment.serviceId)
      },

      validationBookingsFailCallback () {
        this.newAppointmentTabs = 'customFields'
      }
    },

    computed: {
      selectedCustomersMessage () {
        return this.statusMessage !== '' ? this.$root.labels.selected_customers + ' ' + this.statusMessage + ':' : this.$root.labels.selected_customers + ':'
      },

      customersMaxLimit () {
        if (this.appointment.serviceId && this.appointment.providerId && this.getProviderService()) {
          return this.getProviderService().maxCapacity
        }

        if (this.appointment.serviceId && !this.appointment.providerId) {
          return this.getServiceById(this.appointment.serviceId).maxCapacity
        }

        return 0
      }
    },

    watch: {
      'customerCreatedCount' () {
        this.addCustomFieldsValidationRules()
      }
    },

    components: {
      DialogAppointmentPayment,
      DialogCustomFields,
      DialogActions
    }

  }
</script>
