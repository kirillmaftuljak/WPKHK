<template>
  <div class="am-wrap">
    <div id="am-events" class="am-body">

      <!-- Page Header -->
      <page-header
          @newEventBtnClicked="showDialogNewEvent"
      >
      </page-header>

      <!-- Spinner -->
      <div class="am-spinner am-section" style="display: none">
        <img :src="$root.getUrl+'public/img/spinner.svg'"/>
      </div>

      <!-- Empty State -->
      <div class="am-empty-state am-section"
           v-if="fetched && Object.keys(eventsDay).length === 0 && !filterApplied && fetchedFiltered && options.fetched">
        <img :src="$root.getUrl+'public/img/emptystate.svg'">
        <h2>{{ $root.labels.no_events_yet }}</h2>
        <p>{{ $root.labels.click_add_events }}</p>
      </div>

      <!-- Events -->
      <div v-show="fetched && options.fetched && (Object.keys(eventsDay).length !== 0 || (Object.keys(eventsDay).length === 0 && filterApplied) || !fetchedFiltered)">

        <!-- Search & Filter -->
        <div class="am-events-filter am-section">
          <el-form  class=""  method="POST">

            <!-- Global Search & Date Picker -->
            <el-row :gutter="16">
              <el-col :md="5" class="v-calendar-column">
                <el-form-item prop="dates">
                  <v-date-picker
                      @input="changeRange"
                      v-model="params.dates"
                      :is-double-paned="false"
                      mode='range'
                      popover-visibility="focus"
                      popover-direction="bottom"
                      popover-align="left"
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
              <el-col :md="19">
                <el-popover :disabled="!$root.isLite" ref="filterSearchPop" v-bind="$root.popLiteProps"><PopLite/></el-popover>
                <div class="am-search">
                  <el-form-item>

                    <!-- Search -->
                    <el-input
                        v-popover:filterSearchPop
                        class=""
                        :placeholder="$root.labels.event_search_placeholder"
                        v-model="params.search"
                        :disabled="$root.isLite"
                    >
                    </el-input>

                  </el-form-item>
                </div>
              </el-col>
            </el-row>
          </el-form>
        </div>

        <!-- No Results -->
        <div class="am-empty-state am-section" style="display: none"
             v-show="fetched && Object.keys(eventsDay).length === 0 && filterApplied && fetchedFiltered && options.fetched">
          <img :src="$root.getUrl + 'public/img/emptystate.svg'">
          <h2>{{ $root.labels.no_results }}</h2>
        </div>

        <!-- Content Spinner -->
        <div class="am-spinner am-section" v-show="!fetchedFiltered">
          <img :src="$root.getUrl + 'public/img/spinner.svg'"/>
        </div>

        <!-- Event List -->
        <div class="am-events am-section" v-show="fetchedFiltered && options.fetched && Object.keys(eventsDay).length !== 0">

          <!-- Events List Header -->
          <div class="am-events-list-head">
            <el-row>
              <el-col :lg="11">
                <el-row :gutter="10" class="am-events-flex-row-middle-align">

                  <!-- Events List Checkbox -->
                  <el-col :lg="7">
                    <p>
                    </p>
                  </el-col>

                  <!-- Event List Name -->
                  <el-col :lg="8">
                    <p>{{ $root.labels.event_name }}</p>
                  </el-col>

                  <!-- Event List Spots -->
                  <el-col :lg="4">
                    <p>{{ $root.labels.event_capacity }}</p>
                  </el-col>

                  <!-- Event List Recurring -->
                  <el-col :lg="4">
                    <p>{{ $root.labels.event_recurring }}</p>
                  </el-col>

                </el-row>
              </el-col>

              <el-col :lg="13">
                <el-row :gutter="10" class="am-events-flex-row-middle-align">
                  <el-col :lg="0" :md="3">
                  </el-col>

                  <!-- Event List Booking Opens -->
                  <el-col :lg="6">
                    <p>{{ $root.labels.event_booking_opens }}</p>
                  </el-col>

                  <!-- Event List Booking Closes -->
                  <el-col :lg="6">
                    <p>{{ $root.labels.event_booking_closes }}</p>
                  </el-col>

                  <!--Event List Status -->
                  <el-col :lg="3">
                    <p>{{ $root.labels.status_colon }}</p>
                  </el-col>

                  <!--Event List Actions -->
                  <el-col :lg="9">
                  </el-col>
                </el-row>
              </el-col>

            </el-row>
          </div>

          <!-- Events List Content -->
          <div v-for="(evtDay, evtDateKey) in eventsDay">

            <!-- Events Day -->
            <div class="am-events-list-day-title">
              <el-row>
                <el-col :span="24">
                  <h2>

                    <!-- Events Checkbox For Day -->
                    {{ handleDateTimeFormat(evtDay.date + ' 00:00:00')[0] }}
                  </h2>
                </el-col>
              </el-row>
            </div>

            <!-- Events -->
            <div class="am-events-list">
              <div
                  v-for="(evt, index) in evtDay.events"
                  :key="index"
                  :name="evt.id"
                  class="am-event">
                <div class="am-event-data">
                  <el-row>
                    <el-col :lg="11">
                      <el-row :gutter="10" class="am-events-flex-row-middle-align">

                        <!-- Checkbox -->
                        <el-col :lg="7" :sm="7">
                          <span class="am-event-checkbox" @click.stop>
                              <!-- Time -->
                              <span class="am-event-time">
                                {{ handleDateTimeFormat(evt.periodStart)[1] }} - {{ handleDateTimeFormat(evt.periodEnd)[1] }}
                              </span>
                          </span>
                        </el-col>

                        <!-- Event Name -->
                        <el-col :lg="8" :sm="9">
                          <p class="am-col-title">{{ $root.labels.event_name }}</p>
                          <h4>{{ evt.name }}</h4>
                        </el-col>

                        <!-- Spots -->
                        <el-col :lg="4" :sm="4" :xs="12">
                          <p class="am-col-title">{{ $root.labels.event_capacity }}</p>
                          <p><span class="am-semi-strong">{{ evt.maxCapacity - evt.places }}</span> / {{ evt.maxCapacity}}</p>
                        </el-col>

                        <!-- Recurring -->
                        <el-col :lg="4" :sm="4" :xs="12">
                          <p class="am-col-title">{{ $root.labels.event_recurring }}</p>
                          <div class="am-event-recurring">
                            <p v-if="evt.recurring" class="am-recurring-label"><img :src="$root.getUrl + 'public/img/loop.svg'"> {{ $root.labels.yes }}</p>
                            <p v-else>{{ $root.labels.no }}</p>
                          </div>
                        </el-col>

                      </el-row>
                    </el-col>

                    <el-col :lg="13">
                      <el-row :gutter="10" class="am-events-flex-row-middle-align">

                        <!-- Booking Opens -->
                        <el-col :lg="6" :sm="10" :xs="12">
                          <p class="am-col-title">{{ $root.labels.event_booking_opens }}</p>
                          <el-tooltip
                              class="item"
                              effect="dark"
                              content="Open"
                              placement="top"
                              :disabled="!evt.bookingOpens"
                          >
                            <p :class="{ 'am-event-open': evt.opened && evt.status === 'approved' }">
                              <span class="am-semi-strong">{{ handleDateTimeFormat(evt.bookingOpens ? evt.bookingOpens : evt.created)[0] }}</span> @ <span class="am-semi-strong"> {{ handleDateTimeFormat(evt.bookingOpens ? evt.bookingOpens : evt.created)[1] }}</span>
                            </p>
                          </el-tooltip>
                        </el-col>

                        <!-- Booking Closes -->
                        <el-col :lg="6" :sm="10" :xs="12">
                          <p class="am-col-title">{{ $root.labels.event_booking_closes }}</p>
                          <el-tooltip
                              class="item"
                              effect="dark"
                              content="Closed"
                              placement="top"
                              :disabled="!evt.bookingCloses"
                          >
                            <p :class="{ 'am-event-closed': evt.closed && evt.status === 'approved' }" >
                              <span class="am-semi-strong">{{ handleDateTimeFormat(evt.bookingCloses ? evt.bookingCloses : evt.periodStart)[0] }}</span> @ <span class="am-semi-strong"> {{ handleDateTimeFormat(evt.bookingCloses ? evt.bookingCloses : evt.periodStart)[1] }}</span>
                            </p>
                          </el-tooltip>
                        </el-col>

                        <!-- Event Status -->
                        <el-col :lg="3" :sm="4" :xs="24">
                          <p class="am-col-title">{{ $root.labels.status_colon }}</p>
                          <span :class="'am-customer-status ' + getEventStatus(evt).class">
                            {{ getEventStatus(evt).label }}
                          </span>
                        </el-col>

                        <!-- Event Actions -->
                        <el-col :lg="9" :sm="10" :xs="24" class="am-align-right">
                          <div class="am-event-actions" @click.stop>

                            <!-- View Attendees -->
                            <el-button @click="showDialogAttendees(evt.id)" v-if="canManage()" :disabled="!canManage()">
                              {{ $root.labels.event_attendees}}
                            </el-button>

                            <!-- Edit Button -->
                            <el-button @click="showDialogEditEvent(evt.id)" v-if="canManage()" :disabled="!canManage()">
                              {{ $root.labels.edit }}
                            </el-button>
                          </div>

                        </el-col>
                      </el-row>
                    </el-col>
                  </el-row>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>

      <!-- Button New -->
      <div id="am-button-new" class="am-button-new" v-if="canManage()">
        <el-button
            id="am-plus-symbol"
            type="primary"
            icon="el-icon-plus"
            @click="showDialogNewEvent">
        </el-button>
      </div>

      <!-- Dialog Event -->
      <transition name="slide">
        <el-dialog
            class="am-side-dialog am-dialog-event"
            :show-close="false"
            :visible.sync="dialogEvent"
            v-if="dialogEvent"

        >
          <dialog-event
              :event="event"
              :employees="options.entities.employees"
              :locations="options.entities.locations"
              :tags="options.entities.tags"
              :settings="options.entities.settings"
              @closeDialog="closeDialogEvent"
              @saveCallback="saveEventCallback"
          >
          </dialog-event>
        </el-dialog>
      </transition>


      <!-- Dialog Attendees -->
      <transition name="slide">
        <el-dialog
            class="am-side-dialog am-dialog-event"
            :show-close="false"
            :visible.sync="dialogAttendees"
            v-if="dialogAttendees && event && eventBookings"
        >
          <dialog-attendees
              :eventBookings="eventBookings"
              :bookingCreatedCount="bookingCreatedCount"
              @closeDialog="closeDialogAttendees"
              @updateAttendeesCallback="updateAttendeesCallback"
              @showDialogAttendee="showDialogAttendee"
          >
          </dialog-attendees>
        </el-dialog>
      </transition>

      <DialogLite/>

      <!-- Dialog Attendee -->
      <transition name="slide">
        <el-dialog
            class="am-side-dialog am-dialog-event"
            :show-close="false"
            :visible.sync="dialogEventCustomFields"
            v-if="dialogEventCustomFields && event && eventBooking"
        >
          <dialog-attendee
              :eventBooking="eventBooking"
              :eventMaxCapacity="event.maxCapacity"
              :eventId="event.id"
              :options="options"
              :customerCreatedCount="customerCreatedCount"
              @closeDialog="closeDialogAttendee"
              @showDialogNewCustomer="showDialogNewCustomer"
              @saveCallback="saveAttendeeCallback"
          >
          </dialog-attendee>
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

      <!-- Help Button -->
      <el-col :md="6" class="">
        <a class="am-help-button" href="https://wpamelia.com/events/" target="_blank">
          <i class="el-icon-question"></i> {{ $root.labels.need_help }}?
        </a>
      </el-col>

    </div>
  </div>
</template>

<script>
  import liteMixin from '../../../js/common/mixins/liteMixin'
  import backendEventMixin from '../../../js/backend/mixins/eventMixin'
  import commonEventMixin from '../../../js/common/mixins/eventMixin'
  import dateMixin from '../../../js/common/mixins/dateMixin'
  import DialogEvent from './DialogEvent'
  import DialogAttendees from './DialogAttendees'
  import DialogAttendee from './DialogAttendee'
  import durationMixin from '../../../js/common/mixins/durationMixin'
  import entitiesMixin from '../../../js/common/mixins/entitiesMixin'
  import Form from 'form-object'
  import helperMixin from '../../../js/backend/mixins/helperMixin'
  import imageMixin from '../../../js/common/mixins/imageMixin'
  import moment from 'moment'
  import notifyMixin from '../../../js/backend/mixins/notifyMixin'
  import PageHeader from '../parts/PageHeader.vue'
  import DialogCustomer from '../customers/DialogCustomer.vue'
  import customerMixin from '../../../js/backend/mixins/customerMixin'

  export default {
    mixins: [liteMixin, entitiesMixin, imageMixin, dateMixin, durationMixin, notifyMixin, helperMixin, backendEventMixin, commonEventMixin, customerMixin],

    data () {
      return {
        event: null,
        eventBookings: null,
        eventBooking: null,
        updateStatusDisabled: false,
        allEventsChecked: false,
        allDateEventsChecked: {},
        eventsDay: {},
        customer: null,
        customerCreatedCount: 0,

        dialogEvent: false,
        dialogCustomer: false,
        dialogAttendees: false,
        dialogEventCustomFields: false,

        fetched: false,
        fetchedFiltered: false,

        form: new Form(),
        params: {
          dates: this.getDatePickerInitRange(),
          search: ''
        },
        activeTab: 'event_details',

        showDeleteConfirmation: false,
        timer: null,
        toaster: false,
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

      this.getEventOptions(true)
    },

    mounted () {

    },

    updated () {
      if (this.fetched) this.inlineSVG()
    },

    methods: {
      getEventStatus (evt) {
        switch (evt.status) {
          case ('rejected'):
          case ('canceled'):
            return {
              'label': this.$root.labels.canceled,
              'class': 'canceled'
            }

          case ('approved'):
            if (evt.places <= 0 || evt.closed) {
              return {
                'label': this.$root.labels.closed,
                'class': 'closed'
              }
            }

            if (evt.opened && evt.places > 0) {
              return {
                'label': this.$root.labels.opened,
                'class': 'opened'
              }
            }
        }

        return {
          'label': '',
          'class': ''
        }
      },

      canManage () {
        return this.$root.settings.role !== 'customer' && (this.$root.settings.role === 'admin' || this.$root.settings.role === 'manager' || (this.$root.settings.role === 'provider' && this.$root.settings.roles.allowWriteEvents))
      },

      updateAttendeesCallback () {
        this.getEvents()
      },

      saveAttendeeCallback (response) {
        this.getEvent(response.event.id)
      },

      showDialogNewCustomer () {
        this.customer = this.getInitCustomerObject()
        this.dialogCustomer = true
      },

      saveCustomerCallback (response) {
        this.options.entities.customers.push(response.user)
        this.customerCreatedCount++
      },

      saveEventCallback () {
        this.getEvents()
      },

      changeRange () {
        this.setDatePickerSelectedDaysCount(this.params.dates.start, this.params.dates.end)

        this.changeFilter()
      },

      changeFilter () {
        this.getEvents()
      },

      getEventOptions (fetchEvents) {
        this.options.fetched = false

        this.$http.get(`${this.$root.getAjaxUrl}/entities`, {
          params: {
            types: !AMELIA_LITE_VERSION ? ['locations', 'employees', 'tags', 'custom_fields', 'customers', 'settings'] : ['customers', 'settings']
          }
        })
          .then(response => {
            this.options.entities.settings.payments.wc = response.data.data.settings.payments.wc

            this.options.entities.locations = response.data.data.locations
            this.options.entities.employees = response.data.data.employees
            this.options.entities.customFields = response.data.data.customFields
            this.options.entities.customers = response.data.data.customers

            this.fetched = true
            this.options.fetched = true

            let $this = this

            response.data.data.tags.forEach(function (eventTag) {
              if ($this.options.entities.tags.indexOf(eventTag.name) === -1) {
                $this.options.entities.tags.push(eventTag.name)
              }
            })

            if (fetchEvents) {
              this.getEvents()
            }
          })
          .catch(e => {
            console.log(e.message)
            this.fetched = true
            this.options.fetched = true
          })
      },

      getEvents () {
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

        if (this.$root.settings.role === 'provider' && this.$root.settings.roles.allowWriteEvents) {
          params.providers = this.options.entities.employees.map(employee => employee.id)
        }

        this.$http.get(`${this.$root.getAjaxUrl}/events`, {
          params: params
        })
          .then(response => {
            let eventsDay = {}

            response.data.data.events.forEach(function (event) {
              event.periods.forEach(function (eventPeriod) {
                let startDate = moment(eventPeriod.periodStart, 'YYYY-MM-DD HH:mm:ss')
                let endDate = moment(eventPeriod.periodEnd, 'YYYY-MM-DD HH:mm:ss')

                while (startDate.isBefore(endDate)) {
                  let dateString = startDate.format('YYYY-MM-DD')

                  if (!(dateString in eventsDay)) {
                    eventsDay[dateString] = {
                      date: dateString,
                      events: []
                    }
                  }

                  eventsDay[dateString].events.push({
                    id: event.id,
                    name: event.name,
                    periodStart: eventPeriod.periodStart,
                    periodEnd: eventPeriod.periodEnd,
                    bookingOpens: event.bookingOpens,
                    bookingCloses: event.bookingCloses,
                    recurring: event.recurring,
                    maxCapacity: event.maxCapacity,
                    status: event.status,
                    places: event.places,
                    created: event.created,
                    opened: event.opened,
                    closed: event.closed,
                    checked: false
                  })

                  startDate.add(1, 'days')
                }
              })
            })

            this.eventsDay = eventsDay

            this.fetched = true
            this.fetchedFiltered = true
          })
          .catch(e => {
            console.log(e.message)
            this.fetched = true
            this.fetchedFiltered = true
          })
      },

      showDialogAttendees (id) {
        this.eventBookings = null
        this.dialogAttendees = true
        this.getEvent(id)
      },

      closeDialogAttendees () {
        this.dialogAttendees = false
      },

      handleDateTimeFormat (dateTime) {
        return [
          this.getFrontedFormattedDate(dateTime.split(' ')[0]),
          this.getFrontedFormattedTime(dateTime.split(' ')[1])
        ]
      },

      showDialogAttendee (eventBooking) {
        this.eventBooking = eventBooking
        this.dialogEventCustomFields = true
      },

      closeDialogAttendee () {
        this.dialogEventCustomFields = false
      }
    },

    computed: {
      filterApplied () {
        return !!this.params.search || !!this.params.dates.start || !!this.params.dates.end
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
      DialogCustomer,
      PageHeader,
      DialogEvent,
      DialogAttendees,
      DialogAttendee
    }
  }
</script>
