<template>
  <div>

    <!-- Dialog Loader -->
    <div class="am-dialog-loader" v-show="dialogLoading">
      <div class="am-dialog-loader-content">
        <img :src="$root.getUrl + 'public/img/spinner.svg'" class=""/>
        <p>{{$root.labels.loader_message}}</p>
      </div>
    </div>

    <!-- Dialog Content -->
    <div class="am-dialog-scrollable" :class="{'am-edit': event.id !== 0}" v-if="event && !dialogLoading">

      <!-- Dialog Header -->
      <div class="am-dialog-header">
        <el-row>
          <el-col :span="18">
            <h2 v-if="event && event.id !== 0">{{$root.labels.edit_event}}</h2>
            <h2 v-else>{{$root.labels.new_event}}</h2>
          </el-col>
          <el-col :span="6" class="align-right">
            <el-button @click="closeDialog" class="am-dialog-close" size="small" icon="el-icon-close">
            </el-button>
          </el-col>
        </el-row>
      </div>


      <el-form v-if="mounted" :model="event" ref="event" label-position="top">
        <el-tabs v-model="defaultEventTab">

          <!-- Event Details -->
          <el-tab-pane :label="$root.labels.event_details" name="details">

            <!-- Event Name -->
            <el-form-item :label="$root.labels.event_name" prop="name" :rules="rules.name" @input="clearValidation()">
              <el-input v-model="event.name" :placeholder="$root.labels.event_name">
              </el-input>
            </el-form-item>

            <!-- Event Dates -->
            <div class="am-event-dates am-section-grey">

              <!-- Event Start -->
              <div class="am-event-date"
                   v-for="(period, index) in event.periods"
                   :key="index"
              >
                <el-row :gutter="10">
                  <el-col :sm="6">
                    <p>{{$root.labels.event_period_dates}}</p>
                  </el-col>
                  <el-col :sm="16" class="v-calendar-column">
                    <el-form-item :prop="'periods.' + index + '.range'" :rules="rules.range">
                      <v-date-picker
                          v-model="period.range"
                          :attributes="[{
                            dates: {
                              start: getNowDate()
                            },
                            eventDateIndex: index
                          }]"
                          :is-double-paned="false"
                          mode='range'
                          popover-visibility="focus"
                          popover-direction="bottom"
                          popover-align="right"
                          tint-color='#1A84EE'
                          :show-day-popover=false
                          :input-props='{class: "el-input__inner"}'
                          :is-expanded=false
                          :is-required=false
                          :is-read-only=true
                          input-class="el-input__inner"
                          :formats="vCalendarFormats"
                          :available-dates="{start: getNowDate()}"
                          style="margin-bottom: 20px;"
                      >
                      </v-date-picker>
                    </el-form-item>

                  </el-col>
                </el-row>

                <!-- Event End -->
                <el-row :gutter="10">
                  <el-col :sm="6">
                    <p>{{$root.labels.event_period_time}}</p>
                  </el-col>
                  <el-col :sm="8">
                    <el-form-item :prop="'periods.' + index + '.startTime'" :rules="rules.startTime">
                      <el-time-select
                          v-model="period.startTime"
                          :picker-options="getTimeSelectOptionsWithLimits(null, null)"
                          size="large"
                      >
                      </el-time-select>
                    </el-form-item>
                  </el-col>
                  <el-col :sm="8">
                    <el-form-item :prop="'periods.' + index + '.endTime'" :rules="rules.endTime">
                      <el-time-select
                          v-model="period.endTime"
                          :picker-options="getTimeSelectOptionsWithLimits(period.startTime, null)"
                          size="large"
                      >
                      </el-time-select>
                    </el-form-item>
                  </el-col>
                </el-row>

                <!-- Delete Event Date -->
                <div class="am-delete-element disabled" @click="deleteEventDate(index)" v-show="event.periods.length > 1 && period.bookings.length === 0">
                  <i class="el-icon-minus"></i>
                </div>

              </div>

              <div class="am-add-event-date">
                <el-button size="small" type="primary" @click="addEventDate()">{{$root.labels.add_date}}</el-button>
              </div>

            </div>

            <!-- Recurring -->
            <el-popover :disabled="!$root.isLite" ref="recurringPop" v-bind="$root.popLiteProps"><PopLite/></el-popover>
            <div class="am-section-grey" :class="{'am-lite-disabled': ($root.isLite)}" v-popover:recurringPop>
              <el-checkbox v-model="event.isRecurring" :disabled="$root.isLite">{{$root.labels.event_recurring_enabled}}</el-checkbox>
              <div class="am-recurring-event" v-if="event.isRecurring && !$root.isLite">
                <el-row :gutter="10">
                  <el-col :span="10">
                    <p>{{$root.labels.event_recurring_period}}</p>
                  </el-col>
                  <el-col :span="14">
                    <el-select v-model="event.recurring.cycle" :value="recurringPeriods[0].value" :disabled="!(event.id === 0 || (event.id !== 0 && originRecurring.cycle === null))">
                      <el-option
                          v-for="period in this.recurringPeriods"
                          :key="period.value"
                          :label="period.label"
                          :value="period.value">
                      </el-option>
                    </el-select>
                  </el-col>
                </el-row>
                <el-row :gutter="10">
                  <el-col :span="10">
                    <p>{{$root.labels.event_recurring_until}}</p>
                  </el-col>
                  <el-col :span="14" class="v-calendar-column">
                    <el-form-item prop="recurringUntilDate" :rules="rules.recurringUntilDate">
                      <v-date-picker
                          v-model="event.recurring.until"
                          @dayclick="changeBookingEndsDate"
                          :is-double-paned="false"
                          mode='single'
                          popover-visibility="focus"
                          popover-direction="top"
                          popover-align="left"
                          tint-color='#1A84EE'
                          :show-day-popover=false
                          :input-props='{class: "el-input__inner"}'
                          :is-expanded=false
                          :is-required=false
                          input-class="el-input__inner"
                          :formats="vCalendarFormats"
                          :available-dates="{start: event.id === 0 ? getNowDate() : originRecurring.until}"
                      >
                      </v-date-picker>
                    </el-form-item>
                  </el-col>
                </el-row>
              </div>
            </div>

            <!-- Booking Dates -->
            <div class="am-section-grey">
              <el-checkbox v-model="event.bookingStartsNow">{{$root.labels.event_booking_opens_now}}</el-checkbox>

              <div class="am-booking-starts" v-show="!event.bookingStartsNow">
                <el-row :gutter="10">

                  <el-col :sm="24">
                    <label class="el-form-item__label">{{$root.labels.event_booking_opens_on}}</label>
                  </el-col>

                  <el-col :sm="12" class="v-calendar-column">
                    <el-form-item prop="bookingStartsDate" :rules="rules.bookingStartsDate">
                      <v-date-picker
                          v-model="event.bookingStartsDate"
                          @dayclick="changeBookingStartsDate"
                          :is-double-paned="false"
                          mode='single'
                          popover-visibility="focus"
                          popover-direction="bottom"
                          popover-align="left"
                          tint-color='#1A84EE'
                          :show-day-popover=false
                          :input-props='{class: "el-input__inner"}'
                          :is-expanded=false
                          :is-required=false
                          input-class="el-input__inner"
                          :formats="vCalendarFormats"
                          :available-dates="{start: getNowDate()}"
                      >
                      </v-date-picker>
                    </el-form-item>

                  </el-col>
                  <el-col :sm="9">
                    <el-form-item prop="bookingStartsTime" :rules="rules.bookingStartsTime">
                      <el-time-select
                          v-model="event.bookingStartsTime"
                          :picker-options="getTimeSelectOptionsWithLimits(null, null)"
                          size="large"
                      >
                      </el-time-select>
                    </el-form-item>
                  </el-col>
                </el-row>
              </div>
            </div>

            <div class="am-section-grey">
              <el-checkbox v-model="event.bookingEndsAfter">{{$root.labels.event_booking_closes_after}}</el-checkbox>

              <div class="am-booking-ends" v-show="!event.bookingEndsAfter">
                <el-row :gutter="10">

                  <el-col :sm="24">
                    <label class="el-form-item__label">{{$root.labels.event_booking_closes_on}}</label>
                  </el-col>

                  <el-col :sm="12" class="v-calendar-column">
                    <el-form-item prop="bookingEndsDate" :rules="rules.bookingEndsDate">
                      <v-date-picker
                          v-model="event.bookingEndsDate"
                          @dayclick="changeBookingEndsDate"
                          :is-double-paned="false"
                          mode='single'
                          popover-visibility="focus"
                          popover-direction="bottom"
                          popover-align="left"
                          tint-color='#1A84EE'
                          :show-day-popover=false
                          :input-props='{class: "el-input__inner"}'
                          :is-expanded=false
                          :is-required=false
                          input-class="el-input__inner"
                          :formats="vCalendarFormats"
                          :available-dates="{start: getNowDate()}"
                      >
                      </v-date-picker>
                    </el-form-item>
                  </el-col>

                  <el-col :sm="9">
                    <el-form-item prop="bookingEndsTime" :rules="rules.bookingEndsTime">
                      <el-time-select
                          v-model="event.bookingEndsTime"
                          :picker-options="getTimeSelectOptionsWithLimits(null, null)"
                          size="large"
                      >
                      </el-time-select>
                    </el-form-item>
                  </el-col>
                </el-row>
              </div>
            </div>

            <!-- Slots & Price -->
            <div class="am-border-bottom">
              <el-row :gutter="10">
                <el-col :span="12">
                  <p>{{$root.labels.price}}</p>
                </el-col>
                <el-col :span="12">
                  <money v-model="event.price" v-bind="moneyComponentData" class="el-input el-input__inner">
                  </money>
                </el-col>
              </el-row>
              <el-row :gutter="10">
                <el-col :span="12">
                  <p>{{$root.labels.event_max_capacity}}</p>
                </el-col>
                <el-col :span="12">
                  <el-input-number
                      v-model="event.maxCapacity"
                      :min="1"
                  >
                  </el-input-number>
                </el-col>
              </el-row>
            </div>

            <!-- Address -->
            <div class="am-border-bottom">

              <el-row :gutter="10">
                <el-col :span="12">
                  <p>{{$root.labels.event_select_address}}</p>
                </el-col>
                <el-col :span="12">
                  <el-select v-model="event.locationId" :placeholder="$root.labels.select" :value="null" :clearable="true">
                    <el-option
                        :key="null"
                        :label="this.$root.labels.event_custom_address"
                        :value="null">
                    </el-option>
                    <el-option
                        v-for="location in locations"
                        :key="location.id"
                        :label="location.name"
                        :value="location.id">
                    </el-option>
                  </el-select>
                </el-col>
              </el-row>
              <el-row :gutter="10" v-show="!event.locationId">
                <el-col :span="12">
                  <p>{{$root.labels.event_custom_address}}</p>
                </el-col>
                <el-col :span="12">
                  <el-input v-model="event.customLocation" :placeholder="$root.labels.select">
                  </el-input>
                </el-col>
              </el-row>

            </div>

            <!-- Employee & Tags -->
            <div class="am-border-bottom">

              <el-row :gutter="10" v-if="canManage()">
                <el-col :span="12">
                  <p>{{$root.labels.event_staff}}</p>
                </el-col>
                <el-col :span="12">
                  <!-- Recurring -->
                  <el-popover :disabled="!$root.isLite" ref="staffPop" v-bind="$root.popLiteProps"><PopLite/></el-popover>
                  <el-select
                      v-model="event.providers"
                      value-key="id"
                      :placeholder="$root.labels.select"
                      multiple
                      :disabled="$root.isLite"
                      v-popover:staffPop
                  >
                    <el-option
                        v-for="emp in employees"
                        :key="emp.id"
                        :label="emp.firstName + ' ' + emp.lastName"
                        :value="emp">
                    </el-option>
                  </el-select>
                </el-col>
              </el-row>

              <el-row :gutter="10">
                <el-col :span="12">
                  <p>{{$root.labels.event_tags}}</p>
                </el-col>
                <el-col :span="12">
                  <el-popover :disabled="!$root.isLite" ref="tagsPop" v-bind="$root.popLiteProps"><PopLite/></el-popover>
                  <el-select
                      v-model="event.tags"
                      :placeholder="$root.labels.event_tags_create"
                      multiple
                      filterable
                      allow-create
                      default-first-option
                      :no-data-text="$root.labels.event_tags_create"
                      @change="tagsChanged"
                      :disabled="$root.isLite"
                      v-popover:tagsPop
                  >
                    <el-option
                        v-for="tag, index in tags"
                        v-if="tag"
                        :key="index"
                        :label="tag"
                        :value="tag"
                    >
                    </el-option>
                  </el-select>
                </el-col>
              </el-row>

            </div>

            <!-- Desc -->
            <div class="am-event-description">
              <el-row :gutter="10">
                <el-col :span="24">
                  <el-form-item :label="$root.labels.description">
                    <el-input type="textarea" v-model="event.description">
                    </el-input>
                  </el-form-item>
                </el-col>
              </el-row>
            </div>

          </el-tab-pane>

          <!-- Customize -->
          <el-tab-pane :label="$root.labels.customize" name="customize">
            <BlockLite/>
            <!-- Gallery -->
            <gallery
                    :gallery="event.gallery" :label="$root.labels.event_gallery" @galleryUpdated="galleryUpdated" :class="{'am-lite-disabled': $root.isLite, 'am-lite-container-disabled': $root.isLite}">
            </gallery>

            <!-- Colors -->
            <div class="am-event-colors" :class="{'am-lite-disabled': $root.isLite, 'am-lite-container-disabled': $root.isLite}">
              <div class="am-event-section-title">
                {{$root.labels.event_colors}}
              </div>
              <div class="am-event-color-selection">
                <div>
                  <el-radio v-model="event.colorType" :label="1" value=1>{{$root.labels.event_colors_preset}}</el-radio>
                  <div class="am-event-swatches">
                    <span
                        v-for="color in colors"
                        :key="color"
                        :class="{'color-active' : color === event.selectedColor}"
                        @click="changeEventColor"
                        :data-color="color"
                        :style="'background-color: ' + color"
                    >
                    </span>
                  </div>
                </div>
                <div>
                  <el-radio v-model="event.colorType" :label="2" value=2>{{$root.labels.event_colors_custom}}</el-radio>
                  <el-input
                      :disabled="event.colorType === 1"
                      v-model="event.customColor"
                      class="am-event-custom-color"
                      placeholder="000000"
                  >
                  </el-input>
                  <div class="am-event-swatches">
                    <span
                        :data-color="event.customColor"
                        :style="'background-color: ' + event.customColor"
                    >
                    </span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Show/Hide Event -->
            <div :class="{'am-lite-disabled': $root.isLite, 'am-lite-container-disabled': $root.isLite}">
              <el-checkbox v-model="event.show">{{$root.labels.event_show_on_site}}</el-checkbox>
            </div>

          </el-tab-pane>

          <el-tab-pane :label="$root.labels.settings" name="settings">
            <BlockLite/>
            <bookable-payment
                :settings="settings"
                :bookablePayments="event.settings.payments"
            >
            </bookable-payment>
          </el-tab-pane>
        </el-tabs>
      </el-form>
    </div>

    <!-- Dialog Actions -->
    <dialog-actions
        v-if="event && !dialogLoading && this.$root.settings.role !== 'customer'"
        formName="event"
        urlName="events"
        :isNew="event.id === 0"
        :entity="event"
        :getParsedEntity="getParsedEntity"
        :haveSaveConfirmation="haveSaveConfirmation"
        @validationFailCallback="validationFailCallback"
        @errorCallback="errorCallback"
        :hasIcons="false"
        :hasApplyGloballyVisibility="event.isRecurring"
        :hasApplyGloballyDeletion="event.isRecurring && event.status === 'rejected'"

        :status="{
          on: 'approved',
          off: 'rejected'
        }"

        :buttonType="{
          status: event.status === 'approved' ? 'danger' : 'primary',
          remove: 'danger'
        }"

        :buttonText="{
          action: {
            remove: $root.labels.event_delete,
            status: event.status === 'approved' ? $root.labels.event_cancel : $root.labels.event_open
          },
          confirm: {
            save: event.isRecurring ? {
              yes: $root.labels.update_following,
              no: $root.labels.save_single
            } : null,
            status: event.isRecurring ? {
              yes: event.status === 'rejected' ? $root.labels.open_following : $root.labels.cancel_following,
              no: $root.labels.save_single
            } : null,
            remove: event.isRecurring ? {
              yes: $root.labels.delete_following,
              no: $root.labels.save_single
            } : null
          }
        }"

        :action="{
          haveAdd: true,
          haveEdit: true,
          haveStatus: canManage(),
          haveRemove: $root.settings.capabilities.canDelete === true,
          haveRemoveEffect: event.status !== 'rejected',
          haveDuplicate: false
        }"

        :message="{
          success: {
            save: $root.labels.event_saved,
            remove: $root.labels.event_deleted,
            show: $root.labels.event_opened,
            hide: $root.labels.event_canceled
          },
          confirm: {
            save: $root.labels.confirm_save_following,
            remove: event.isRecurring ? $root.labels.confirm_delete_following : $root.labels.confirm_delete,
            show: event.isRecurring ? $root.labels.confirm_open_following : $root.labels.confirm_open,
            hide: event.isRecurring ? $root.labels.confirm_cancel_following : $root.labels.confirm_cancel,
            duplicate: ''
          }
        }"
    >
    </dialog-actions>

  </div>

</template>

<script>
  import DialogActions from '../parts/DialogActions.vue'
  import BookablePayment from '../parts/BookablePayment.vue'
  import imageMixin from '../../../js/common/mixins/imageMixin'
  import dateMixin from '../../../js/common/mixins/dateMixin'
  import { Money } from 'v-money'
  import priceMixin from '../../../js/common/mixins/priceMixin'
  import Gallery from '../parts/Gallery.vue'
  import notifyMixin from '../../../js/backend/mixins/notifyMixin'
  import durationMixin from '../../../js/common/mixins/durationMixin'

  export default {

    mixins: [notifyMixin, imageMixin, dateMixin, priceMixin, durationMixin],

    props: {
      event: null,
      employees: null,
      locations: null,
      tags: null,
      settings: null
    },

    data () {
      let isRecurringUntilDateDateRequired = (rule, input, callback) => {
        if (!this.event.recurring.until && !input) {
          callback(new Error(this.$root.labels.select_date_warning))
        } else {
          callback()
        }
      }

      let isBookingStartsDateRequired = (rule, input, callback) => {
        if (!this.event.bookingStartsNow && !input) {
          callback(new Error(this.$root.labels.select_date_warning))
        } else {
          callback()
        }
      }

      let isBookingStartsTimeRequired = (rule, input, callback) => {
        if (!this.event.bookingStartsNow && !input) {
          callback(new Error(this.$root.labels.select_time_warning))
        } else {
          callback()
        }
      }

      let isBookingEndsDateRequired = (rule, input, callback) => {
        if (!this.event.bookingEndsAfter && !input) {
          callback(new Error(this.$root.labels.select_date_warning))
        } else {
          callback()
        }
      }

      let isBookingEndsTimeRequired = (rule, input, callback) => {
        if (!this.event.bookingEndsAfter && !input) {
          callback(new Error(this.$root.labels.select_time_warning))
        } else {
          callback()
        }
      }

      return {
        originRecurring: null,
        originPeriods: null,
        colors: [
          '#1788FB',
          '#4BBEC6',
          '#FBC22D',
          '#FA3C52',
          '#D696B8',
          '#689BCA',
          '#26CC2B',
          '#FD7E35',
          '#E38587',
          '#774DFB'
        ],
        recurringPeriods: [
          {
            label: this.$root.labels.event_recurring_type_weekly,
            value: 'weekly'
          },
          {
            label: this.$root.labels.event_recurring_type_monthly,
            value: 'monthly'
          },
          {
            label: this.$root.labels.event_recurring_type_yearly,
            value: 'yearly'
          }
        ],
        dialogLoading: true,
        executeUpdate: true,
        mounted: false,
        rules: {
          name: [
            {
              required: true, message: this.$root.labels.enter_event_name_warning, trigger: 'submit'
            }
          ],
          range: [
            {
              required: true, message: this.$root.labels.select_date_warning, trigger: 'submit'
            }
          ],
          startTime: [
            {
              required: true, message: this.$root.labels.select_time_warning, trigger: 'submit'
            }
          ],
          endTime: [
            {
              required: true, message: this.$root.labels.select_time_warning, trigger: 'submit'
            }
          ],
          bookingStartsDate: [
            {
              validator: isBookingStartsDateRequired, trigger: 'submit'
            }
          ],
          bookingStartsTime: [
            {
              validator: isBookingStartsTimeRequired, trigger: 'submit'
            }
          ],
          bookingEndsDate: [
            {
              validator: isBookingEndsDateRequired, trigger: 'submit'
            }
          ],
          bookingEndsTime: [
            {
              validator: isBookingEndsTimeRequired, trigger: 'submit'
            }
          ],
          recurringUntilDate: [
            {
              validator: isRecurringUntilDateDateRequired, trigger: 'submit'
            }
          ]
        },
        defaultEventTab: 'details'
      }
    },

    methods: {
      canManage () {
        return this.$root.settings.role === 'admin' || this.$root.settings.role === 'manager'
      },

      galleryUpdated: function () {},

      validationFailCallback () {
        this.defaultEventTab = 'details'
      },

      tagsChanged:function () {},

      haveSaveConfirmation () {
        return this.event.id !== 0 && this.event.isRecurring
      },

      changeBookingStartsDate () {
        if (this.event.bookingStartsTime === null) {
          this.event.bookingStartsTime = '00:00'
        }
      },

      changeBookingEndsDate () {
        if (this.event.bookingEndsTime === null) {
          this.event.bookingEndsTime = '00:00'
        }
      },

      instantiateDialog () {
        if ((this.event !== null || (this.event !== null && this.event.id === 0)) && this.executeUpdate === true) {
          this.originPeriods = JSON.parse(JSON.stringify(this.event.periods))
          this.originRecurring = JSON.parse(JSON.stringify(this.event.recurring))

          this.mounted = true
          this.executeUpdate = false
          this.dialogLoading = false
        }
      },

      clearValidation () {
        if (typeof this.$refs.event !== 'undefined') {
          this.$refs.event.clearValidate()
        }
      },

      getParsedEntity (applyGlobally) {
        let $this = this

        let eventPeriods = []

        this.event.periods.forEach(function (period, index) {
          if (typeof $this.originPeriods[index] !== 'undefined') {
            period.id = $this.originPeriods[index].id
            period.eventId = $this.originPeriods[index].eventId
          }

          eventPeriods.push({
            id: period.id,
            eventId: $this.event.id ? $this.event.id : null,
            periodStart: $this.getDateString(period.range.start) + ' ' + period.startTime + ':00',
            periodEnd: $this.getDateString(period.range.end) + ' ' + period.endTime + ':00'
          })
        })

        let tags = []

        if (!AMELIA_LITE_VERSION) {
          this.event.tags.forEach(function (tag) {
            tags.push({
              name: tag
            })
          })
        }

        let eventSettings = JSON.parse(JSON.stringify(this.event.settings))

        if (eventSettings.payments.wc.productId === this.$root.settings.payments.wc.productId) {
          delete eventSettings.payments.wc
        }

        return {
          id: this.event.id,
          parentId: this.event.parentId,
          name: this.event.name,
          periods: eventPeriods,
          bookingOpens: !this.event.bookingStartsNow ? this.getDateString(this.event.bookingStartsDate) + ' ' + this.event.bookingStartsTime + ':00' : null,
          bookingCloses: !this.event.bookingEndsAfter ? this.getDateString(this.event.bookingEndsDate) + ' ' + this.event.bookingEndsTime + ':00' : null,
          recurring: null,
          maxCapacity: this.event.maxCapacity,
          price: this.event.price,
          tags: tags,
          providers: this.event.providers,
          description: this.event.description,
          gallery: this.event.gallery,
          color: this.event.colorType === 1 ? this.event.selectedColor : this.event.customColor,
          show: this.event.show,
          locationId: this.event.locationId !== null ? this.event.locationId : null,
          customLocation: this.event.locationId === null ? this.event.customLocation : null,
          applyGlobally: applyGlobally,
          settings: JSON.stringify(eventSettings)
        }
      },

      errorCallback (responseData) {
      },

      closeDialog () {
        this.$emit('closeDialog')
      },

      addEventDate () {
        this.event.periods.push({
          id: null,
          eventId: null,
          range: {
            start: new Date(),
            end: new Date()
          },
          startTime: null,
          endTime: null,
          bookings: []
        })
      },

      deleteEventDate (dateKey) {
        this.event.periods.splice(dateKey, 1)
      },

      changeEventColor: function () {}
    },

    mounted () {
      this.instantiateDialog()
    },

    updated () {
      this.instantiateDialog()
    },

    components: {
      BookablePayment,
      DialogActions,
      Money,
      Gallery
    }
  }
</script>
