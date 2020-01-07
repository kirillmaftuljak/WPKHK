<template>
  <div class="am-wrap">
    <!-- Spinner -->
    <div class="am-spinner am-section" v-show="!fetched">
      <img class="svg-booking am-spin" :src="$root.getUrl + 'public/img/oval-spinner.svg'">
      <img class="svg-booking am-hourglass" :src="$root.getUrl + 'public/img/hourglass.svg'">
    </div>

    <div id="am-events-booking" v-show="fetched">

      <!-- Event Filter -->
      <div class="am-events-filter">
        <el-row :gutter="24">
          <el-col :sm="getColumnLength()[0]" v-show="showTags()">
            <el-select
                v-model="params.tag"
                :placeholder="$root.labels.event_type"
                clearable
                value=""
                @change="getEvents"
            >
              <el-option
                  v-for="(tag, index) in options.entities.tags.map(eventTag => eventTag.name)"
                  :key="index"
                  :label="tag"
                  :value="tag"
              >
              </el-option>
            </el-select>
          </el-col>
          <el-col :sm="getColumnLength()[1]" class="v-calendar-column" v-show="showDatePicker()">
            <v-date-picker
                :input-props="{class: 'input', placeholder: this.$root.labels.event_pick_min_date, readonly: true}"
                @input="getEvents"
                popover-visibility="focus"
                popover-direction="bottom"
                popover-align="right"
                v-model="params.date"
                mode="single"
                id="am-calendar-picker"
                class="am-calendar-picker"
                tint-color='#1A84EE'
                :show-day-popover=false
                :is-expanded=false
                :is-inline=false
                :is-required=true
                :formats="vCalendarFormats"
            >
            </v-date-picker>
          </el-col>
        </el-row>
      </div>

      <!-- Event List -->
      <div class="am-event-list" :style="{'opacity': !fetchedFiltered ? '0.3' : 1, 'pointer-events': fetchedFiltered ? 'all' : 'none'}">

        <!-- Event -->
        <div class="am-event"
             v-for="(evt, index) in events"
             :id="'am-event-' + evt.id"
             v-if="evt.show"
             :key="index"
             :class="{'am-active': evt.showEventDetails, 'inactive': events.filter(event => event.showEventDetails && event.id !== evt.id).length > 0, 'canceled': getEventAvailability(evt).class === 'canceled', 'closed': getEventAvailability(evt).class === 'closed' && !evt.showAddToCalendar}"
             :style="{'pointer-events': evt.showAddToCalendar ? 'all' : (getEventAvailability(evt).class === 'closed' || getEventAvailability(evt).class === 'canceled' ? 'none' : 'all')}"
             >
          <div class="am-event-data" @click="getEventAvailability(evt).class === 'closed' || getEventAvailability(evt).class === 'canceled' ? function () {} : toggleEventDetails(evt)">

            <!-- Event Dates -->
            <div v-if="isEventInSameDay(evt)" class="am-event-date">
               <div class="am-event-date-month" :style="getBookableColor(evt, false)">{{ getEventFrontedFormattedDate(evt.periods[0].periodStart).split(' ')[0] }}</div>
               <div class="am-event-date-day">{{ getEventFrontedFormattedDate(evt.periods[0].periodStart).split(' ')[1] }}</div>
            </div>

            <div v-else class="am-event-dates">
              <div>
                <div class="am-event-date-month" :style="getBookableColor(evt, false)">{{ getEventFrontedFormattedDate(evt.periods[0].periodStart).split(' ')[0] }}</div>
                <div class="am-event-date-day">{{ getEventFrontedFormattedDate(evt.periods[0].periodStart).split(' ')[1] }}</div>
              </div>

              <div>
                <div class="am-event-date-month" :style="getBookableColor(evt, false)">{{ getEventFrontedFormattedDate(evt.periods[evt.periods.length - 1].periodEnd).split(' ')[0] }}</div>
                <div class="am-event-date-day">{{ getEventFrontedFormattedDate(evt.periods[evt.periods.length - 1].periodEnd).split(' ')[1] }}</div>
              </div>
            </div>

            <!-- Event Info -->
            <div class="am-event-info">
              <div class="am-event-title">{{ evt.name }}
                <span class="am-event-booking-status" :class="getEventAvailability(evt).class" >{{ getEventAvailability(evt).label }}</span>
              </div>
              <div class="am-event-sub-info">
                <div class="am-event-sub-info-capacity"><img :src="$root.getUrl + 'public/img/capacity.svg'"> {{$root.labels.event_capacity}} {{ evt.maxCapacity - evt.places }} / {{ evt.maxCapacity }}</div>
                <div v-if="getLocation(evt)"><img :src="$root.getUrl + 'public/img/pin.svg'"> {{ getLocation(evt) }}</div>
              </div>
              <div class="am-event-sub-info"><div><img :src="$root.getUrl + 'public/img/clock.svg'"> {{ getEventDatesAndTimes(evt.periods) }}</div></div>
            </div>

            <!-- Event Price -->

            <div class="am-event-price" :style="getBookableColor(evt, true)" v-if="evt.price !== 0">{{ getFormattedPrice(evt.price) }}</div>
            <div class="am-event-price am-event-free" :style="getBookableColor(evt, false)" v-else>{{$root.labels.event_free}}</div>

          </div>

          <transition name="fade">
            <div v-show="evt.showEventDetails">

              <!-- Event Details -->
              <div class="am-event-details" v-if="(evt.gallery && evt.gallery.length) || (evt.description && evt.description.length)">
                <div class="am-event-photos" v-if="evt.gallery && evt.gallery.length">


                  <div v-for="(photo, index) in evt.gallery"
                         :key="photo.id">
                    <lightbox
                        :thumbnail="photo.pictureThumbPath"
                        :images="getImages(evt.gallery.map(image => image.pictureFullPath), index)"
                    >
                      <lightbox-default-loader slot="loader"></lightbox-default-loader>
                      <!-- If you want to use built-in loader -->
                      <!-- <div slot="loader"></div> --> <!-- If you want to use your own loader -->
                    </lightbox>
                  </div>
                 <!-- <img v-for="photo in evt.photos" :src="photo.url">-->
                </div>
                <div class="am-event-about" v-if="evt.description && evt.description.length">
                  <div>{{$root.labels.event_about}}</div>
                  <div v-html="evt.description">
                  </div>
                </div>
              </div>

              <!-- Event Book -->
              <div class="am-event-book-cta" :class="getEventAvailability(evt).class">
                <div>
                  {{$root.labels.event_book}}
                </div>
                <div>

                  <el-form>
                    <el-form-item :label="$root.labels.event_book_persons">
                      <el-input-number
                          size="small"
                          @change="setPlaces"
                          type="number"
                          :value="appointment.bookings[0].persons"
                          :min="1"
                          :max="evt.places"
                      >
                      </el-input-number>
                    </el-form-item>

                    <el-form-item>
                      <el-button
                          type="primary"
                          :style="getBookableColor(evt, true)"
                          @click="toggleEventBooking(evt)"
                          :disabled="evt.places <= 0">
                        {{$root.labels.continue}}
                      </el-button>
                    </el-form-item>
                  </el-form>
                </div>
              </div>
            </div>
          </transition>

          <transition name="fade">
            <div class="am-event-booking" v-show="evt.showEventBooking">
              <confirm-booking
                  v-if="evt.showEventBooking"
                  :visible.sync="evt.showEventBooking"
                  :appointment="appointment"
                  bookableType="event"
                  :bookable="getBookableData(evt)"
                  :customFields="options.entities.customFields"
                  @confirmedBooking="confirmedBooking"
                  @cancelBooking="evt.showEventBooking = false"
              >
              </confirm-booking>
            </div>
          </transition>

          <!-- Add To Calendar -->
          <transition name="fade">
            <div v-show="evt.showAddToCalendar" class="am-event-booking">
              <add-to-calendar
                  v-if="evt.showAddToCalendar"
                  :addToCalendarData="evt.addToCalendarData"
                  @closeDialogAddToCalendar="evt.showAddToCalendar = false"
              >
              </add-to-calendar>
            </div>
          </transition>

        </div>
      </div>

      <div class="am-event-pagination">
        <el-pagination
            v-show="pagination.count > pagination.show"
            :page-size="pagination.show"
            :total="pagination.count"
            layout="prev, pager, next"
            :current-page.sync=pagination.page
        >
        </el-pagination>

      </div>

    </div>
  </div>
</template>

<script>
  import moment from 'moment'
  import imageMixin from '../../../js/common/mixins/imageMixin'
  import settingsMixin from '../../../js/common/mixins/settingsMixin'
  import dateMixin from '../../../js/common/mixins/dateMixin'
  import priceMixin from '../../../js/common/mixins/priceMixin'
  import ConfirmBooking from '../parts/ConfirmBooking.vue'
  import bookingMixin from '../../../js/frontend/mixins/bookingMixin'
  import commonEventMixin from '../../../js/common/mixins/eventMixin'
  import helperMixin from '../../../js/backend/mixins/helperMixin'
  import AddToCalendar from '../parts/AddToCalendar.vue'
  import customFieldMixin from '../../../js/common/mixins/customFieldMixin'

  export default {
    mixins: [imageMixin, dateMixin, priceMixin, bookingMixin, commonEventMixin, helperMixin, customFieldMixin, settingsMixin],

    data () {
      return {
        tags: [],

        pagination: {
          show: this.$root.settings.general.itemsPerPage,
          page: 1,
          count: 0
        },

        params: {
          tag: null,
          date: new Date(),
          page: 1
        },

        options: {
          entities: {
            tags: [],
            locations: []
          }
        },

        fetched: false,
        fetchedFiltered: false,

        events: [],

        appointment: {
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
            status: 'approved'
          }],
          payment: {
            amount: 0,
            gateway: '',
            data: {}
          }
        }
      }
    },

    methods: {
      getImages (gallery, index) {
        for (let i = 0; i < index; i++) {
          gallery.push(gallery.shift())
        }

        return gallery
      },

      setPlaces (value) {
        this.appointment.bookings[0].persons = value
      },

      getBookableColor (bookable, colorBackground) {
        return colorBackground ? {
          'color': '#ffffff',
          'background-color': bookable.color,
          'border-color': '#ffffff'
        } : {
          'color': bookable.color,
          'background-color': '',
          'border-color': ''
        }
      },

      getEventDatesAndTimes (periods) {
        let $this = this

        let resultPeriods = []

        this.getImplodedPeriods(periods).forEach(function (period) {
          let periodStart = period.periodStart.split(' ')
          let periodEnd = period.periodEnd.split(' ')

          if (period.isConnected) {
            resultPeriods.push($this.getFrontedFormattedDateTime(periodStart) + ' - ' + $this.getFrontedFormattedDateTime(periodEnd))
          } else {
            if (periodStart[0] === periodEnd[0]) {
              resultPeriods.push($this.getFrontedFormattedDate(periodStart[0]) + ' ' + $this.getFrontedFormattedTime(periodStart[1]) + ' - ' + $this.getFrontedFormattedTime(periodEnd[1]))
            } else {
              resultPeriods.push($this.getFrontedFormattedDate(periodStart[0]) + ' - ' + $this.getFrontedFormattedDate(periodEnd[0]) + ' ' + $this.getFrontedFormattedTime(periodStart[1]) + ' - ' + $this.getFrontedFormattedTime(periodEnd[1]))
            }
          }
        })

        return resultPeriods.join(', ')
      },

      getPreselectedTag () {
        return 'eventTag' in this.$root.shortcodeData.booking ? this.$root.shortcodeData.booking.eventTag : null
      },

      getPreselectedEventId () {
        return 'eventId' in this.$root.shortcodeData.booking ? this.$root.shortcodeData.booking.eventId : null
      },

      getPreselectedEventRecurring () {
        return 'eventRecurring' in this.$root.shortcodeData.booking ? this.$root.shortcodeData.booking.eventRecurring : null
      },

      showTags () {
        return this.options.entities.tags.length > 1 && this.showDatePicker()
      },

      showDatePicker () {
        return this.getPreselectedEventId() === null || (this.getPreselectedEventId() !== null && this.getPreselectedEventRecurring())
      },

      getColumnLength () {
        if (this.showTags() && this.showDatePicker()) {
          return [12, 12]
        }

        if (this.showTags() && !this.showDatePicker()) {
          return [24, 0]
        }

        if (!this.showTags() && this.showDatePicker()) {
          return [0, 24]
        }

        if (!this.showTags() && !this.showDatePicker()) {
          return [12, 12]
        }

        return [12, 12]
      },

      getEntities () {
        this.$http.get(`${this.$root.getAjaxUrl}/entities`, {
          params: {
            types: []
          }
        }).then(response => {
          this.options.entities.locations = response.data.data.locations

          this.options.entities.tags = this.getPreselectedTag() ? [] : response.data.data.tags

          this.options.entities.customFields = response.data.data.customFields
          this.setBookingCustomFields()
        }).catch(() => {
        })
      },

      getEventAvailability (evt) {
        if (evt.status === 'approved' || evt.status === 'pending') {
          return evt.places <= 0 || !evt.bookable ? {
            label: this.$root.labels.closed,
            class: 'closed'
          } : {
            label: this.$root.labels.open,
            class: 'open'
          }
        } else {
          return {
            label: this.$root.labels.canceled,
            class: 'canceled'
          }
        }
      },

      isEventInSameDay (evt) {
        let result = true

        if (evt.periods.length === 1) {
          result = evt.periods[0].periodStart.split(' ')[0] === evt.periods[0].periodEnd.split(' ')[0]
        } else {
          let periodStart = evt.periods[0].periodStart.split(' ')[0]
          let periodEnd = evt.periods[0].periodEnd.split(' ')[0]

          evt.periods.forEach(function (period) {
            if (period.periodStart.split(' ')[0] !== periodStart || period.periodEnd.split(' ')[0] !== periodEnd) {
              result = false
            }
          })
        }

        return result
      },

      confirmedBooking (responseData) {
        this.$http.post(`${this.$root.getAjaxUrl}/bookings/success/` + responseData.booking.id, {
          type: 'event',
          appointmentStatusChanged: responseData.appointmentStatusChanged
        }).then(response => {
        }).catch(e => {
        })

        let event = this.events.find(event => event.id === responseData.event.id)

        event.places = event.places - this.appointment.bookings[0].persons

        event.showEventBooking = false

        // Customization hook
        if ('beforeConfirmedBooking' in window) {
          window.beforeConfirmedBooking()
        } else {
          let dates = []
          let location = ''

          if (responseData.event.locationId) {
            location = this.options.entities.locations.find(location => location.id === responseData.event.locationId).address
          } else if (responseData.event.customLocation) {
            location = responseData.event.customLocation
          }

          responseData.utcTime.forEach(function (date) {
            dates.push(
              {
                start: moment.utc(date.start.replace(/ /g, 'T')).toDate(),
                end: moment.utc(date.end.replace(/ /g, 'T')).toDate()
              }
            )
          })

          event.addToCalendarData = {
            title: responseData.event.name,
            dates: dates,
            address: location,
            description: responseData.event.description,
            id: responseData.booking.id,
            status: responseData.booking.status,
            active: this.$root.settings.general.addToCalendar,
            color: responseData.color,
            type: responseData.type
          }

          event.showAddToCalendar = true
          event.bookingCompleted = true
        }
      },

      getBookableData (evt) {
        return {
          id: evt.id,
          name: evt.name,
          price: evt.price,
          maxCapacity: evt.maxCapacity,
          color: evt.color,
          aggregatedPrice: true,
          bookingStart: evt.periods[0].periodStart,
          bookingStartTime: evt.periods[0].periodStart.split(' ')[1]
        }
      },

      getEvents () {
        let params = JSON.parse(JSON.stringify(this.params))

        this.fetchedFiltered = false

        let eventId = this.getPreselectedEventId()

        let recurring = this.getPreselectedEventRecurring()

        let tagName = this.getPreselectedTag() ? this.getPreselectedTag() : params.tag

        this.$http.get(`${this.$root.getAjaxUrl}/events`, {
          params: {
            dates: params.date ? [
              this.getDateString(params.date)
            ] : [
              this.getDateString(this.getNowDate())
            ],
            tag: tagName,
            page: this.pagination.page,
            id: eventId,
            recurring: recurring
          }
        })
          .then(response => {
            let $this = this

            this.events = []

            this.pagination.count = response.data.data.count

            response.data.data.events.forEach(function (event) {
              event.gallery = event.gallery.sort((a, b) => (a.position > b.position) ? 1 : -1)

              event.showEventDetails = false
              event.showEventBooking = false
              event.showAddToCalendar = false
              event.bookingCompleted = false

              $this.events.push(event)

              if ($this.$root.settings.general.showClientTimeZone) {
                event.periods.forEach(function (period) {
                  let utcOffsetStart = (new Date(period.periodStart)).getTimezoneOffset()
                  let utcOffsetEnd = (new Date(period.periodEnd)).getTimezoneOffset()

                  if (utcOffsetStart > 0) {
                    period.periodStart = moment.utc(period.periodStart, 'YYYY-MM-DD HH:mm:ss').subtract(utcOffsetStart, 'minutes').format('YYYY-MM-DD HH:mm:ss')
                  } else {
                    period.periodStart = moment.utc(period.periodStart, 'YYYY-MM-DD HH:mm:ss').add(-1 * utcOffsetStart, 'minutes').format('YYYY-MM-DD HH:mm:ss')
                  }

                  if (utcOffsetEnd > 0) {
                    period.periodEnd = moment.utc(period.periodEnd, 'YYYY-MM-DD HH:mm:ss').subtract(utcOffsetEnd, 'minutes').format('YYYY-MM-DD HH:mm:ss')
                  } else {
                    period.periodEnd = moment.utc(period.periodEnd, 'YYYY-MM-DD HH:mm:ss').add(-1 * utcOffsetEnd, 'minutes').format('YYYY-MM-DD HH:mm:ss')
                  }
                })
              }
            })

            this.fetched = true
            this.fetchedFiltered = true
          })
          .catch(e => {
            console.log(e.message)
          })
      },

      toggleEventDetails (evt) {
        this.scrollView('am-event-' + evt.id)
        evt.showEventDetails = !evt.showEventDetails
        this.events.forEach(function (event) {
          if (event.id !== evt.id) {
            event.showEventDetails = false
            event.showEventBooking = false
            event.showAddToCalendar = false
            event.showConfirmBooking = false
          }
        })
        evt.showEventBooking = false

        if (event.bookingCompleted) {
          event.showAddToCalendar = evt.showEventDetails
        }

        this.updateSettings(evt.settings)
      },

      toggleEventBooking (evt) {
        evt.showEventDetails = !evt.showEventDetails
        evt.showEventBooking = !evt.showEventBooking
      },

      getLocation (evt) {
        if (evt.locationId && this.options.entities.locations.length) {
          let location = this.options.entities.locations.find(location => location.id === evt.locationId)

          return typeof location !== 'undefined' ? location.name : ''
        } else if (evt.customLocation) {
          return evt.customLocation
        }
      },

      inlineBookingSVG () {
        let inlineSVG = require('inline-svg')
        inlineSVG.init({
          svgSelector: 'img.svg-booking',
          initClass: 'js-inlinesvg'
        })
      }
    },

    computed: {
    },

    created () {
    },

    mounted () {
      this.getEntities(['locations', 'tags'])
      this.inlineBookingSVG()
      this.getEvents()
      this.getCurrentUser()
    },

    watch: {
      'pagination.page' () {
        this.getEvents()
      }
    },

    components: {
      ConfirmBooking,
      AddToCalendar
    }
  }
</script>

