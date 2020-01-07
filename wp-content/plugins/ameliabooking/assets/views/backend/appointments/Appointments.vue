<template>
  <div class="am-wrap">
    <div id="am-appointments" class="am-body">

      <!-- Page Header -->
      <page-header
          :appointmentsApproved="appointmentStatusCount.approved"
          :appointmentsPending="appointmentStatusCount.pending"
          @newAppointmentBtnClicked="showDialogNewAppointment"
      >
      </page-header>

      <!-- Spinner -->
      <div class="am-spinner am-section" v-show="!fetched || !options.fetched">
        <img :src="$root.getUrl+'public/img/spinner.svg'"/>
      </div>

      <!-- Empty State -->
      <div class="am-empty-state am-section"
           v-if="fetched && appointmentsDay.length === 0 && !filterApplied && fetchedFiltered && options.fetched">
        <img :src="$root.getUrl+'public/img/emptystate.svg'">
        <h2>{{ $root.labels.no_appointments_yet }}</h2>
        <p>{{ $root.labels.click_add_appointments }}</p>
      </div>

      <!-- Appointments -->
      <div
          v-show="fetched && options.fetched && (appointmentsDay.length !== 0 || (appointmentsDay.length === 0 && filterApplied) || !fetchedFiltered)"
      >

        <!-- Global Search & Filters -->
        <div class="am-appointments-filter am-section">
          <el-form :model="params" class="demo-form-inline" :action="exportAction" method="POST">

            <!-- Global Search & Export Button -->
            <el-row :gutter="16">
              <el-col :md="24">
                <el-popover :disabled="!$root.isLite" ref="filterSearchPop" v-bind="$root.popLiteProps"><PopLite/></el-popover>
                <div class="am-search">
                  <el-form-item>

                    <!-- Global Search -->
                    <el-input
                        v-popover:filterSearchPop
                        class="calc-width"
                        :placeholder="$root.labels.appointments_search_placeholder"
                        v-model="params.search"
                        :disabled="$root.isLite"
                    >
                    </el-input>

                    <!-- Toggle Filters Button -->
                    <el-button
                        class="button-filter-toggle am-button-icon"
                        title="Toggle Filters"
                        :disabled="$root.isLite"
                        @click="filterFields = !filterFields">
                      <img
                          class="svg" alt="Toggle Filters"
                          :src="$root.getUrl+'public/img/filter.svg'"
                      />
                    </el-button>

                    <!-- Export -->
                    <el-tooltip placement="top">
                      <div slot="content" v-html="$root.labels.export_tooltip_appointments"></div>
                      <el-button
                          class="button-export am-button-icon"
                          @click="dialogExport = true"
                      >
                        <img class="svg" :alt="$root.labels.export" :src="$root.getUrl+'public/img/export.svg'"/>
                      </el-button>
                    </el-tooltip>

                  </el-form-item>
                </div>
              </el-col>
            </el-row>

            <!-- Filters -->
            <transition name="fade">
              <div class="am-filter-fields" v-show="filterFields">
                <el-row :gutter="16">

                  <!-- Dates Filter -->
                  <el-col :sm="24" :md="24" :lg="5" class="v-calendar-column">
                    <el-form-item prop="dates">
                      <v-date-picker
                          @input="changeRange"
                          v-model="params.dates"
                          :is-double-paned="false"
                          mode='range'
                          popover-visibility="focus"
                          popover-direction="bottom"
                          tint-color='#1A84EE'
                          :show-day-popover=false
                          :input-props='{class: "el-input__inner"}'
                          :is-expanded=false
                          :is-required=true
                          input-class="el-input__inner"
                          :formats="vCalendarFormats"
                      >
                      </v-date-picker>
                    </el-form-item>
                  </el-col>

                  <!-- Employees Filter -->
                  <el-col :sm="6" :md="6" :lg="5" v-if="!$root.isLite">
                    <el-form-item>
                      <el-popover :disabled="!$root.isLite" ref="filterEmployeesPop" v-bind="$root.popLiteProps"><PopLite/></el-popover>
                      <el-select
                          v-popover:filterEmployeesPop
                          :disabled="$root.isLite"
                          v-model="params.providers"
                          filterable
                          clearable
                          :placeholder="$root.labels.employees"
                          @change="changeFilter"
                          multiple
                          collapse-tags
                      >
                        <el-option
                            v-for="item in visibleEmployees"
                            :key="item.id"
                            :label="item.firstName + ' ' + item.lastName"
                            :value="item.id"
                        >
                        </el-option>
                      </el-select>
                    </el-form-item>
                  </el-col>

                  <!-- Customer Filter -->
                  <el-col :sm="6" :md="6" :lg="$root.isLite ? 7 : 5">
                    <el-form-item>
                      <el-popover :disabled="!$root.isLite" ref="filterCustomersPop" v-bind="$root.popLiteProps"><PopLite/></el-popover>
                      <el-select
                          v-popover:filterCustomersPop
                          :disabled="$root.isLite"
                          v-model="params.customerId"
                          filterable
                          clearable
                          :placeholder="$root.labels.customer"
                          @change="changeFilter"
                      >
                        <el-option
                            v-for="item in visibleCustomers"
                            :key="item.id"
                            :label="item.firstName + ' ' + item.lastName"
                            :value="item.id"
                        >
                        </el-option>
                      </el-select>
                    </el-form-item>
                  </el-col>

                  <!-- Services Filter -->
                  <el-col :sm="6" :md="6" :lg="$root.isLite ? 7 : 5">
                    <el-form-item>
                      <el-popover :disabled="!$root.isLite" ref="filterServicesPop" v-bind="$root.popLiteProps"><PopLite/></el-popover>
                      <el-select
                          v-popover:filterServicesPop
                          :disabled="$root.isLite"
                          v-model="params.services"
                          multiple
                          filterable
                          :placeholder="$root.labels.services"
                          @change="changeFilter"
                          collapse-tags
                      >
                        <div
                            v-for="category in options.entities.categories"
                            :key="category.id">
                          <div
                              class="am-drop-parent"
                              @click="selectAllInCategory(category.id)"
                          >
                            <span>{{ category.name }}</span>
                          </div>
                          <el-option
                              v-for="service in category.serviceList"
                              :key="service.id"
                              :label="service.name"
                              :value="service.id"
                              class="am-drop-child"
                          >
                          </el-option>
                        </div>
                      </el-select>
                    </el-form-item>
                  </el-col>

                  <!-- Status Filter -->
                  <el-col :sm="6" :md="6" :lg="$root.isLite ? 5 : 4">
                    <el-form-item>
                      <el-popover :disabled="!$root.isLite" ref="filterStatusPop" v-bind="$root.popLiteProps"><PopLite/></el-popover>
                      <el-select
                          v-popover:filterStatusPop
                          :disabled="$root.isLite"
                          v-model="params.status"
                          filterable
                          clearable
                          :placeholder="$root.labels.status"
                          @change="changeFilter"
                      >
                        <el-option
                            v-for="item in statuses"
                            :key="item.value"
                            :label="item.label"
                            :value="item.value"
                            class="am-appointment-status-option"
                        >
                          <span class="am-appointment-status-symbol"
                                :class="item.value">
                          </span>
                          <span>{{ item.label }}</span>
                        </el-option>
                      </el-select>
                    </el-form-item>
                  </el-col>

                </el-row>
              </div>
            </transition>

            <!-- Dialog Export -->
            <transition name="slide">
              <el-dialog
                  class="am-side-dialog am-dialog-export"
                  :visible.sync="dialogExport"
                  :show-close="false"
                  v-if="dialogExport"
              >
                <dialog-export
                    :data="Object.assign(params, exportParams)"
                    :action="$root.getAjaxUrl + '/report/appointments'"
                    @updateAction="(action) => {this.exportAction = action}"
                    @closeDialogExport="dialogExport = false"
                >
                </dialog-export>
              </el-dialog>
            </transition>

          </el-form>
        </div>

        <!-- No Results -->
        <div class="am-empty-state am-section"
             v-show="fetched && appointmentsDay.length === 0 && filterApplied && fetchedFiltered && options.fetched">
          <img :src="$root.getUrl + 'public/img/emptystate.svg'">
          <h2>{{ $root.labels.no_results }}</h2>
        </div>

        <!-- Content Spinner -->
        <div class="am-spinner am-section" v-show="!fetchedFiltered">
          <img :src="$root.getUrl+'public/img/spinner.svg'"/>
        </div>

        <!-- Appointment List -->
        <div
            class="am-appointments am-section"
            v-show="fetchedFiltered && options.fetched && appointmentsDay.length !== 0"
        >

          <!-- Appointment List Header -->
          <div class="am-appointments-list-head">
            <el-row>
              <el-col :lg="15">
                <el-row :gutter="10" class="am-appointments-flex-row-middle-align">

                  <!-- Appointment List Checkbox -->
                  <el-col :lg="3" :md="3">
                    <p>
                      <el-checkbox
                          v-if="$root.settings.capabilities.canDelete === true"
                          v-model="allAppointmentsChecked"
                          @change="handleCheckAllAppointments"></el-checkbox>
                    </p>
                  </el-col>

                  <!-- Appointment List Customer Label -->
                  <el-col :lg="6" :md="6">
                    <p>{{ $root.labels.customer }}:</p>
                  </el-col>

                  <!-- Appointment List Assigned To Label -->
                  <el-col :lg="6" :md="6">
                    <p>{{ $root.labels.assigned_to }}:</p>
                  </el-col>

                  <!-- Appointment List Service Label -->
                  <el-col :lg="9" :md="9">
                    <p>{{ $root.labels.service }}:</p>
                  </el-col>

                </el-row>
              </el-col>

              <el-col :lg="9">
                <el-row :gutter="10" class="am-appointments-flex-row-middle-align">
                  <el-col :lg="0" :md="3"></el-col>

                  <!-- Appointment Duration Label -->
                  <el-col :lg="5" :md="6">
                    <p>{{ $root.labels.duration }}:</p>
                  </el-col>

                  <!-- Appointment Payment Label -->
                  <el-col :lg="7" :sm="6">
                    <p>{{ $root.labels.payment }}:</p>
                  </el-col>

                  <!-- Appointment Status Label -->
                  <el-col :lg="12" :md="6">
                    <p>{{ $root.labels.status }}:</p>
                  </el-col>

                </el-row>
              </el-col>

            </el-row>
          </div>

          <!-- Appointment List Content -->
          <div v-for="(appDay, appDateKey) in appointmentsDay"
               v-if="!$root.isLite || appDay.appointments.filter(appointment => appointment.providerId === options.entities.employees[0].id).length"
          >

            <!-- Appointment Day -->
            <div class="am-appointments-list-day-title">
              <el-row>
                <el-col :span="24">
                  <h2>

                    <!-- Appointment Checkbox For Day -->
                    <el-checkbox
                        v-if="$root.settings.capabilities.canDelete === true"
                        @change="handleCheckedDateAppointments(appDateKey)"
                        v-model="allDateAppointmentsChecked[appDateKey]"
                        :label="appDay.date">
                    </el-checkbox>
                    {{ handleDateFormat(appDay.date) }}
                  </h2>
                </el-col>
              </el-row>
            </div>

            <!-- Appointments -->
            <div class="am-appointments-list">
              <el-collapse>
                <el-collapse-item
                    v-for="app in appDay.appointments"
                    v-if="appointmentProvider = getProviderById(app.providerId)"
                    :key="app.id"
                    :name="app.id"
                    class="am-appointment">
                  <template slot="title">
                    <div class="am-appointment-data">
                      <el-row>

                        <el-col :lg="15">
                          <el-row :gutter="10" class="am-appointments-flex-row-middle-align">

                            <!-- Checkbox -->
                            <el-col :lg="3" :sm="3">
                              <span class="am-appointment-checkbox" @click.stop>
                                <el-checkbox
                                    v-if="$root.settings.capabilities.canDelete === true"
                                    @change="handleCheckedAppointment(app)"
                                    v-model="app.checked"
                                    :value="app.id"
                                    :label="app.id"
                                >
                                </el-checkbox>
                              </span>
                              <span class="am-appointment-time"
                                    :class="getBookingStatus(app.status, app.bookings[0].status)">
                                {{ getFrontedFormattedTime(getTime(app.bookingStart)) }}
                              </span>
                            </el-col>

                            <!-- Customer -->
                            <el-col :lg="6" :sm="6">
                              <p class="am-col-title">{{ $root.labels.customer }}:</p>
                              <template>
                                <el-tooltip
                                    class="item"
                                    effect="dark"
                                    placement="top"
                                    :disabled="app.bookings.length === 1"
                                    popper-class="am-align-left"
                                >
                                  <div v-if="app.bookings.length > 1"
                                       slot="content"
                                       v-html="getCustomersFromGroup(app)"></div>
                                  <h3 :class="{ grouped: app.bookings.length > 1 }">
                                    <img
                                        v-show="app.bookings.length > 1"
                                        width="16px"
                                        :src="$root.getUrl+'public/img/group.svg'"
                                        class=""
                                    />
                                    <span v-for="(booking, index) in app.bookings">
                                      {{ ((user = getCustomerInfo(booking)) !== null ? user.firstName + ' ' + user.lastName : '') }}<span
                                        v-if="app.bookings.length > 1 && index + 1  !== app.bookings.length">,</span>
                                    </span>
                                  </h3>
                                </el-tooltip>
                                <span v-if="app.bookings.length === 1" v-for="booking in app.bookings">{{ ((user = getCustomerById(booking.customerId)) !== null ? user.email : '') }}</span>
                                <span v-if="app.bookings.length > 1">{{ $root.labels.multiple_emails }}</span>
                              </template>
                            </el-col>

                            <!-- Employee -->
                            <el-col :lg="6" :sm="6">
                              <p class="am-col-title">{{ $root.labels.assigned }}:</p>
                              <div class="am-assigned">
                                <img :src="pictureLoad(appointmentProvider, true)"
                                     @error="imageLoadError(appointmentProvider, true)"
                                     v-if="options.fetched && fetchedFiltered"/>
                                <h4>
                                  {{ ((user = appointmentProvider) !== null ? user.firstName + ' ' +
                                  user.lastName : '') }}
                                </h4>
                              </div>
                            </el-col>

                            <!-- Service -->
                            <el-col :lg="9" :sm="9">
                              <p class="am-col-title">{{ $root.labels.service }}:</p>
                              <h4>
                                {{ ((service = getServiceById(app.serviceId)) !== null ? service.name : '') }}
                              </h4>
                            </el-col>

                          </el-row>
                        </el-col>

                        <el-col :lg="9">
                          <el-row :gutter="10" class="am-appointments-flex-row-middle-align">
                            <el-col :lg="0" :sm="3"></el-col>

                            <!-- Duration -->
                            <el-col :lg="5" :sm="6" :xs="12">
                              <p class="am-col-title">{{ $root.labels.duration }}:</p>
                              <h4>{{
                                momentDurationToNiceDurationWithUnit(convertDateTimeRangeDifferenceToMomentDuration(app.bookingStart,
                                app.bookingEnd)) }}</h4>
                            </el-col>

                            <!-- Payment -->
                            <el-col class="am-appointment-payment" :lg="7" :sm="6" :xs="13">
                              <p class="am-col-title">{{ $root.labels.payment }}:</p>
                              <div class="am-appointment-payment-wrap">
                                <img
                                    v-for="method in getAppointmentPaymentMethods(app.bookings)"
                                    :src="$root.getUrl + 'public/img/payments/' + method + '.svg'"
                                >
                                <h4>
                                  {{ getAppointmentPrice(app.serviceId, app.serviceId, app.bookings) }}
                                </h4>
                              </div>
                            </el-col>

                            <!-- Status -->
                            <el-col :lg="7" :sm="6" :xs="16" v-if="$root.settings.role !== 'customer'">
                              <div class="am-appointment-status" @click.stop>
                                <span class="am-appointment-status-symbol" :class="app.status"></span>
                                <el-select
                                    v-model="app.status"
                                    :placeholder="$root.labels.status"
                                    @change="updateAppointmentStatus(app, app.status, true)"
                                    :disabled="updateStatusDisabled || !canWriteAppointments()"
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
                            </el-col>

                            <!-- Edit Button -->
                            <el-col :lg="5" :sm="3" :xs="8"
                                    v-if="canWriteAppointments()">
                              <div class="am-edit-btn" @click.stop>
                                <el-button
                                    @click="showDialogEditAppointment(app.id)">
                                  {{ $root.labels.edit }}
                                </el-button>
                              </div>
                            </el-col>

                            <!-- Cancel Button For Customers -->
                            <el-col :lg="12" :sm="6" v-if="$root.settings.role === 'customer'">
                              <span
                                  :class="'am-customer-status ' + app.bookings[0].status">{{ statuses.find(status => status.value === app.bookings[0].status).label }}</span>
                              <span class="am-cancel-btn" @click.stop>
                                <el-tooltip effect="dark" :content="$root.labels.cancel_appointment" placement="top">
                                  <el-button
                                      type="danger"
                                      size="mini"
                                      @click="updateBookingStatus(app, 'canceled')"
                                      :loading="updateBookingStatusLoading && app.bookings[0].id === updateBookingStatusId"
                                      v-if="!(app.past || app.bookings[0].status === 'canceled' || app.bookings[0].status === 'rejected') && app.cancelable"
                                  >
                                    {{ $root.labels.cancel }}
                                  </el-button>
                                </el-tooltip>
                              </span>
                            </el-col>

                          </el-row>
                        </el-col>

                      </el-row>
                    </div>
                  </template>

                  <appointment-list-collapsed
                      :app="app"
                      :options="options"
                      v-if="$root.settings.role !== 'customer'"
                  >
                  </appointment-list-collapsed>
                </el-collapse-item>
              </el-collapse>
            </div>

          </div>
        </div>

        <!-- Selected Popover -->
        <transition name="slide-vertical">
          <div class="am-bottom-popover" v-if="toaster">
            <transition name="fade">
              <el-button
                  class="am-button-icon"
                  @click="showDeleteConfirmation = !showDeleteConfirmation"
                  v-show="!showDeleteConfirmation"
              >
                <img class="svg" :alt="$root.labels.delete" :src="$root.getUrl+'public/img/delete.svg'"/>
              </el-button>
            </transition>
            <transition name="slide-vertical">
              <div class="am-bottom-popover-confirmation" v-show="showDeleteConfirmation">
                <el-row type="flex" justify="start" align="middle">
                  <h3>{{ $root.labels.confirm_delete_appointment }}</h3>
                  <div class="align-left">
                    <el-button size="small" @click="showDeleteConfirmation = !showDeleteConfirmation">
                      {{ $root.labels.cancel }}
                    </el-button>
                    <el-button
                        size="small" @click="deleteAppointmentGroup"
                        type="primary"
                        :loading="deleteAppointmentGroupLoading"
                    >
                      {{ $root.labels.delete }}
                    </el-button>
                  </div>
                </el-row>

              </div>
            </transition>

          </div>
        </transition>
      </div>

      <!-- Button New -->
      <div v-if="canWriteAppointments()" id="am-button-new" class="am-button-new">
        <el-button id="am-plus-symbol" type="primary" icon="el-icon-plus"
                   @click="showDialogNewAppointment"></el-button>
      </div>

      <!-- Dialog New Appointment -->
      <transition name="slide">
        <el-dialog
            class="am-side-dialog"
            :visible.sync="dialogAppointment"
            :show-close="false"
            v-if="dialogAppointment"
        >
          <dialog-appointment
              :appointment="appointment"
              :savedAppointment="savedAppointment"
              :bookings="bookings"
              :options="options"
              :customerCreatedCount="customerCreatedCount"
              @sortBookings="sortBookings"
              @saveCallback="saveAppointmentCallback"
              @duplicateCallback="duplicateAppointmentCallback"
              @closeDialog="closeDialogAppointment"
              @showDialogNewCustomer="showDialogNewCustomer()"
              @editPayment="editPayment"
          >
          </dialog-appointment>
        </el-dialog>
      </transition>

      <!-- Dialog New Customer -->
      <transition name="slide">
        <el-dialog
            class="am-side-dialog"
            :visible.sync="dialogCustomer"
            :show-close="false"
            v-if="dialogCustomer">
          <dialog-customer
              :customer="customer"
              @closeDialog="dialogCustomer=false"
              @saveCallback="saveCustomerCallback"
          >
          </dialog-customer>
        </el-dialog>
      </transition>

      <!-- Dialog Payment -->
      <transition name="slide">
        <el-dialog
            class="am-side-dialog am-dialog-coupon"
            :visible.sync="dialogPayment"
            :show-close="false"
            v-if="dialogPayment"
        >
          <dialog-payment
              :modalData="selectedPaymentModalData"
              :bookingFetched=true
              @closeDialogPayment="dialogPayment = false"
              @updatePaymentCallback="updatePaymentCallback"
          >
          </dialog-payment>
        </el-dialog>
      </transition>

      <DialogLite/>

      <!-- Help Button -->
      <el-col :md="6" class="">
        <a class="am-help-button" href="https://wpamelia.com/appointments/" target="_blank">
          <i class="el-icon-question"></i> {{ $root.labels.need_help }}?
        </a>
      </el-col>

    </div>
  </div>
</template>

<script>
  import AppointmentListCollapsed from './AppointmentListCollapsed.vue'
  import liteMixin from '../../../js/common/mixins/liteMixin'
  import appointmentMixin from '../../../js/backend/mixins/appointmentMixin'
  import appointmentPriceMixin from '../../../js/backend/mixins/appointmentPriceMixin'
  import customerMixin from '../../../js/backend/mixins/customerMixin'
  import dateMixin from '../../../js/common/mixins/dateMixin'
  import DialogAppointment from './DialogAppointment.vue'
  import DialogCustomer from '../customers/DialogCustomer.vue'
  import DialogExport from '../parts/DialogExport.vue'
  import DialogPayment from '../finance/DialogFinancePayment.vue'
  import durationMixin from '../../../js/common/mixins/durationMixin'
  import entitiesMixin from '../../../js/common/mixins/entitiesMixin'
  import Form from 'form-object'
  import helperMixin from '../../../js/backend/mixins/helperMixin'
  import imageMixin from '../../../js/common/mixins/imageMixin'
  import moment from 'moment'
  import notifyMixin from '../../../js/backend/mixins/notifyMixin'
  import PageHeader from '../parts/PageHeader.vue'
  import paymentMixin from '../../../js/backend/mixins/paymentMixin'
  import priceMixin from '../../../js/common/mixins/priceMixin'
  import customFieldMixin from '../../../js/common/mixins/customFieldMixin'

  export default {
    mixins: [liteMixin, customFieldMixin, paymentMixin, entitiesMixin, appointmentMixin, imageMixin, dateMixin, durationMixin, notifyMixin, customerMixin, priceMixin, helperMixin, appointmentPriceMixin],

    data () {
      return {
        updateStatusDisabled: false,
        allAppointmentsChecked: false,
        allDateAppointmentsChecked: {},
        appointment: null,
        appointmentsDay: [],
        appointmentStatusCount: {
          'approved': 0,
          'pending': 0
        },
        customer: null,
        deleteAppointmentGroupLoading: false,
        dialogAppointment: false,
        dialogPayment: false,
        dialogExport: false,
        fetched: false,
        fetchedFiltered: false,
        filterFields: true,
        form: new Form(),
        params: {
          dates: this.getDatePickerInitRange(),
          providers: [],
          search: '',
          status: '',
          services: [],
          customerId: ''
        },
        selectedPaymentModalData: {
          paymentId: null,
          bookingStart: null,
          bookings: null,
          service: null,
          providers: null,
          customer: null
        },
        showDeleteConfirmation: false,
        timer: null,
        toaster: false,
        updateBookingStatusId: 0,
        updateBookingStatusLoading: false,
        count: {
          success: 0,
          error: 0
        }
      }
    },

    created () {
      // Set filter params based on URL GET fields
      let urlParams = this.getUrlQueryParams(window.location.href)

      if (!('dateFrom' in urlParams) || !('dateTo' in urlParams)) {
        this.params.dates = this.getDatePickerInitRange()
      } else {
        this.params.dates = {
          start: moment(urlParams['dateFrom']).toDate(),
          end: moment(urlParams['dateTo']).toDate()
        }
      }

      if (urlParams['status']) { this.params.status = urlParams['status'] }

      if (urlParams['bookingId']) { this.params.bookingId = urlParams['bookingId'] }

      this.getAppointmentOptions(true)
    },

    mounted () {

    },

    updated () {
      if (this.fetched) this.inlineSVG()
    },

    methods: {
      saveAppointmentCallback () {
        this.getAppointmentOptions(true)
      },

      editPayment (obj) {
        this.selectedPaymentModalData = this.getPaymentData(obj.paymentId, this.savedAppointment, null)
        this.dialogPayment = true
      },

      deleteAppointmentGroup () {
        this.deleteAppointmentGroupLoading = true
        let $this = this
        let selectedAppointments = []

        Object.keys(this.appointmentsDay).forEach(function (dateKey) {
          $this.appointmentsDay[dateKey].appointments.forEach(function (app) {
            if (app.checked) {
              selectedAppointments.push(app.id)
            }
          })
        })

        Object.keys(this.appointmentsDay).forEach(function (dateKey) {
          $this.appointmentsDay[dateKey].appointments.forEach(function (app) {
            if (app.checked) {
              $this.form.post(`${$this.$root.getAjaxUrl}/appointments/delete/` + app.id)
                .then(() => {
                  $this.deleteAppointmentGroupCallback(selectedAppointments, true)
                })
                .catch(() => {
                  $this.deleteAppointmentGroupCallback(selectedAppointments, false)
                })
            }
          })
        })
      },

      deleteAppointmentGroupCallback (selectedAppointments, result) {
        selectedAppointments.pop()

        if (result) {
          this.count.success++
        } else {
          this.count.error++
        }

        if (selectedAppointments.length === 0) {
          if (this.count.success) {
            this.notify(
              this.$root.labels.success,
              this.count.success + ' ' + (this.count.success > 1 ? this.$root.labels.appointments_deleted : this.$root.labels.appointment_deleted),
              'success')
          }

          if (this.count.error) {
            this.notify(
              this.$root.labels.error,
              this.count.error + ' ' + (this.count.error > 1 ? this.$root.labels.appointments_not_deleted : this.$root.labels.appointment_not_deleted),
              'error')
          }

          this.count.success = 0
          this.count.error = 0

          let $this = this
          this.getAppointmentOptions(true)

          Object.keys(this.appointmentsDay).forEach(function (dateKey) {
            $this.allDateAppointmentsChecked[dateKey] = false
          })

          this.allAppointmentsChecked = false
          this.toaster = false
          this.deleteAppointmentGroupLoading = false
        }
      },

      updateBookingStatus (app, status) {
        this.updateBookingStatusLoading = true
        this.updateBookingStatusId = app.bookings[0].id
        this.form.post(`${this.$root.getAjaxUrl}/bookings/cancel/${app.bookings[0].id}`)
          .then(response => {
            this.updateBookingStatusLoading = false
            this.updateBookingStatusId = 0
            this.setTotalStatusCounts(app, status, response.data.status)

            this.notify(
              this.$root.labels.success,
              response.data.message,
              'success'
            )

            app.status = response.data.status
          })
          .catch(e => {
            if ('cancelBookingUnavailable' in e.response.data.data && e.response.data.data.cancelBookingUnavailable === true) {
              this.notify(this.$root.labels.error, this.$root.labels.booking_cancel_exception, 'error')
            }

            this.updateBookingStatusLoading = true
            this.updateBookingStatusId = 0
            this.errorMessage = e.message
          })
      },

      setTotalStatusCounts (app, requestStatus, responseStatus) {
        let appointmentsCount = 0

        let $this = this

        Object.keys(this.appointmentsDay).forEach(function (dateKey) {
          $this.appointmentsDay[dateKey].appointments.forEach(function (appItem) {
            appointmentsCount++
          })
        })

        app.bookings.forEach(function (bookItem) {
          bookItem.status = responseStatus
        })

        if (requestStatus === responseStatus) {
          switch (requestStatus) {
            case 'approved':
              if (this.appointmentStatusCount.approved < appointmentsCount) {
                this.appointmentStatusCount.approved++
              }

              if (this.appointmentStatusCount.pending > 0) {
                this.appointmentStatusCount.pending--
              }

              break

            case 'pending':
              if (this.appointmentStatusCount.pending < appointmentsCount) {
                this.appointmentStatusCount.pending++
              }

              if (this.appointmentStatusCount.approved > 0) {
                this.appointmentStatusCount.approved--
              }

              break
          }
        }
      },

      getAppointmentOptions (fetchAppointments) {
        this.options.fetched = false
        let $this = this

        this.fetchEntities(function (success) {
          if (success) {
            $this.filterResponseData()

            $this.setBookings(0)

            if (fetchAppointments) {
              $this.getAppointments()
            }
          }

          $this.fetched = true
          $this.options.fetched = true
        }, {
          types: ['categories', 'employees', 'customers'],
          isFrontEnd: false
        })
      },

      getAppointmentOptionsIfMissingCustomers () {
        let existingCustomersIds = this.options.entities.customers.map(customer => customer.id)

        let fetchEntities = false

        for (let appDate in this.appointmentsDay) {
          this.appointmentsDay[appDate].appointments.forEach(function (app) {
            app.bookings.forEach(function (appBooking) {
              if (existingCustomersIds.indexOf(appBooking.customerId) === -1) {
                fetchEntities = true
              }
            })
          })
        }

        if (fetchEntities) {
          this.getAppointmentOptions(false)
        }
      },

      getAppointments () {
        this.fetchedFiltered = false

        let params = JSON.parse(JSON.stringify(this.params))
        let dates = []

        if (params.dates) {
          if (params.dates.start) {
            dates.push(moment(params.dates.start).format('YYYY-MM-DD'))
          }

          if (params.dates.end) {
            dates.push(moment(params.dates.end).format('YYYY-MM-DD'))
          }

          params.dates = dates
        }

        Object.keys(params).forEach((key) => (!params[key] && params[key] !== 0) && delete params[key])

        this.$http.get(`${this.$root.getAjaxUrl}/appointments`, {
          params: params
        })
          .then(response => {
            this.appointmentStatusCount.approved = 0
            this.appointmentStatusCount.pending = 0

            let $this = this

            if (this.$root.settings.role === 'customer') {
              Object.keys(response.data.data.appointments).forEach(function (dateKey) {
                response.data.data.appointments[dateKey].appointments.forEach(function (app) {
                  if (app.bookings[0].status === 'approved') {
                    $this.appointmentStatusCount.approved++
                  }

                  if (app.bookings[0].status === 'pending') {
                    $this.appointmentStatusCount.pending++
                  }
                })
              })
            } else {
              Object.keys(response.data.data.appointments).forEach(function (dateKey) {
                response.data.data.appointments[dateKey].appointments.forEach(function (app) {
                  if (app.status === 'approved') {
                    $this.appointmentStatusCount.approved++
                  }

                  if (app.status === 'pending') {
                    $this.appointmentStatusCount.pending++
                  }

                  app.checked = false
                })

                $this.allDateAppointmentsChecked[dateKey] = false
              })
            }

            this.appointmentsDay = response.data.data.appointments

            this.getAppointmentOptionsIfMissingCustomers()

            this.fetched = true
            this.fetchedFiltered = true
          })
          .catch(e => {
            console.log(e.message)
            this.fetched = true
            this.fetchedFiltered = true
          })
      },

      changeRange () {
        this.setDatePickerSelectedDaysCount(this.params.dates.start, this.params.dates.end)

        this.changeFilter()
      },

      changeFilter () {
        this.getAppointments()
      },

      handleResize () {
        this.filterFields = window.innerWidth >= 992
      },

      handleDateFormat (date) {
        if (date === moment().format('YYYY-MM-DD')) {
          return this.$root.labels.today
        } else if (date === moment().add(1, 'days').format('YYYY-MM-DD')) {
          return this.$root.labels.tomorrow
        }

        return this.getFrontedFormattedDate(date)
      },

      handleCheckAllAppointments () {
        let $this = this

        Object.keys(this.appointmentsDay).forEach(function (dateKey) {
          $this.appointmentsDay[dateKey].appointments.forEach(function (app) {
            app.checked = $this.allAppointmentsChecked
            $this.allDateAppointmentsChecked[dateKey] = $this.allAppointmentsChecked
          })
        })

        this.toaster = $this.allAppointmentsChecked
      },

      handleCheckedDateAppointments (appDateKey) {
        let $this = this
        let checkedAllAppointmentsCount = 0
        let totalAppointmentsCount = 0

        Object.keys(this.appointmentsDay).forEach(function (dateKey) {
          $this.appointmentsDay[dateKey].appointments.forEach(function (app) {
            if (dateKey === appDateKey) {
              app.checked = $this.allDateAppointmentsChecked[appDateKey]
              checkedAllAppointmentsCount++
            }

            totalAppointmentsCount++
          })
        })

        this.allAppointmentsChecked = (checkedAllAppointmentsCount === totalAppointmentsCount)

        this.toaster = checkedAllAppointmentsCount > 0
      },

      handleCheckedAppointment () {
        let $this = this
        let checkedAllAppointmentsCount = 0
        let totalAppointmentsCount = 0

        Object.keys(this.appointmentsDay).forEach(function (dateKey) {
          let checkedDateAppointmentsCount = 0

          $this.appointmentsDay[dateKey].appointments.forEach(function (app) {
            if (app.checked) {
              checkedDateAppointmentsCount++
              checkedAllAppointmentsCount++
            }

            totalAppointmentsCount++
          })

          $this.allDateAppointmentsChecked[dateKey] = (checkedDateAppointmentsCount === $this.appointmentsDay[dateKey].appointments.length)
        })

        this.allAppointmentsChecked = (checkedAllAppointmentsCount === totalAppointmentsCount)

        this.toaster = checkedAllAppointmentsCount > 0
      },

      selectAllInCategory: function (id) {},

      getBookingStatus (appointmentStatus, bookingStatus) {
        if (this.$root.settings.role === 'customer' && appointmentStatus !== 'pending') {
          return bookingStatus
        }

        return appointmentStatus
      },

      showDialogNewCustomer () {
        this.customer = this.getInitCustomerObject()
        this.dialogCustomer = true
      },

      canWriteAppointments () {
        return this.$root.settings.capabilities.canWriteOthers === true || (this.$root.settings.role === 'provider' && this.$root.settings.roles.allowWriteAppointments)
      }
    },

    computed: {
      visibleEmployees () {
        return this.options.entities.employees.filter(employee => employee.status === 'hidden' || employee.status === 'visible')
      },

      filterApplied () {
        return !!this.params.search || !!this.params.services.length || !!this.params.providers.length || !!this.params.customerId || !!this.params.dates.start || !!this.params.dates.end || !!this.params.status
      }
    },

    watch: {
      'params.search' () {
        if (typeof this.params.search !== 'undefined') {
          this.fetchedFiltered = false
          clearTimeout(this.timer)
          this.timer = setTimeout(this.changeFilter, 500)
        }
      }
    },

    components: {
      PageHeader,
      DialogAppointment,
      DialogCustomer,
      DialogPayment,
      DialogExport,
      AppointmentListCollapsed
    }
  }
</script>
