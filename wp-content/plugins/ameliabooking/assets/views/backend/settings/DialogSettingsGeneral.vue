<template>
  <div>
    <div class="am-dialog-scrollable">

      <!-- Dialog Header -->
      <div class="am-dialog-header">
        <el-row>
          <el-col :span="20">
            <h2>{{ $root.labels.general_settings }}</h2>
          </el-col>
          <el-col :span="4" class="align-right">
            <el-button @click="closeDialog" class="am-dialog-close" size="small" icon="el-icon-close"></el-button>
          </el-col>
        </el-row>
      </div>

      <!-- Form -->
      <el-form :model="settings" ref="settings" :rules="rules" label-position="top" @submit.prevent="onSubmit">

        <el-row :gutter="24">

          <!-- Time Slot -->
          <el-popover :disabled="!$root.isLite" ref="timeSlotStepPop" v-bind="$root.popLiteProps"><PopLite/></el-popover>
          <el-col :span="12" v-popover:timeSlotStepPop>
            <el-form-item label="placeholder">
              <label slot="label">
                {{ $root.labels.default_time_slot_step }}:
                <el-tooltip placement="top">
                  <div slot="content" v-html="$root.labels.default_time_slot_step_tooltip"></div>
                  <i class="el-icon-question am-tooltip-icon"></i>
                </el-tooltip>
              </label>
              <el-select v-model="settings.timeSlotLength" :disabled="$root.isLite">
                <el-option
                    v-for="item in options.timeSlotLength"
                    :key="item.value"
                    :label="item.label"
                    :value="item.value">
                </el-option>
              </el-select>
            </el-form-item>
          </el-col>

          <!-- Appointment Status -->
          <el-col :span="12">
            <el-form-item label="placeholder">
              <label slot="label">
                {{ $root.labels.default_appointment_status }}:
                <el-tooltip placement="top">
                  <div slot="content" v-html="$root.labels.default_appointment_status_tooltip"></div>
                  <i class="el-icon-question am-tooltip-icon"></i>
                </el-tooltip>
              </label>
              <el-select v-model="settings.defaultAppointmentStatus">
                <el-option
                    v-for="item in options.defaultAppointmentStatus"
                    :key="item.value"
                    :label="item.label"
                    :value="item.value">
                </el-option>
              </el-select>
            </el-form-item>
          </el-col>

        </el-row>

        <!-- Use Service Duration As Booking Time Slot -->
        <div class="am-setting-box am-switch-box">
          <el-row type="flex" align="middle" :gutter="24">
            <el-col :span="16">
              {{ $root.labels.service_duration_as_slot }}
              <el-tooltip placement="top">
                <div slot="content" v-html="$root.labels.service_duration_as_slot_tooltip"></div>
                <i class="el-icon-question am-tooltip-icon"></i>
              </el-tooltip>
            </el-col>
            <el-col :span="8" class="align-right">
              <el-switch
                  v-model="settings.serviceDurationAsSlot"
                  active-text=""
                  inactive-text=""
              ></el-switch>
            </el-col>
          </el-row>
        </div>

        <!-- Minimum Time Prior to Booking -->
        <el-popover :disabled="!$root.isLite" ref="minimumTimeBeforeBookingPop" v-bind="$root.popLiteProps"><PopLite/></el-popover>
        <el-form-item label="placeholder" v-popover:minimumTimeBeforeBookingPop>
          <label slot="label">
            {{ $root.labels.minimum_time_before_booking }}:
            <el-tooltip placement="top">
              <div slot="content" v-html="$root.labels.minimum_time_before_booking_tooltip"></div>
              <i class="el-icon-question am-tooltip-icon"></i>
            </el-tooltip>
          </label>
          <el-select v-model="settings.minimumTimeRequirementPriorToBooking" :disabled="$root.isLite">
            <el-option
                v-for="item in options.minimumTime"
                :key="item.value"
                :label="item.label"
                :value="item.value">
            </el-option>
          </el-select>
        </el-form-item>

        <!-- Minimum Time Prior to Canceling -->
        <el-popover :disabled="!$root.isLite" ref="minimumTimeBeforeCancelingPop" v-bind="$root.popLiteProps"><PopLite/></el-popover>
        <el-form-item label="placeholder" v-popover:minimumTimeBeforeCancelingPop>
          <label slot="label">
            {{ $root.labels.minimum_time_before_canceling }}:
            <el-tooltip placement="top">
              <div slot="content" v-html="$root.labels.minimum_time_before_canceling_tooltip"></div>
              <i class="el-icon-question am-tooltip-icon"></i>
            </el-tooltip>
          </label>
          <el-select v-model="settings.minimumTimeRequirementPriorToCanceling" :disabled="$root.isLite">
            <el-option
                v-for="item in options.minimumTime"
                :key="item.value"
                :label="item.label"
                :value="item.value">
            </el-option>
          </el-select>
        </el-form-item>

        <!-- Number of days available for booking -->
        <el-form-item label="placeholder">
          <label slot="label">
            {{ $root.labels.period_available_for_booking }}:
            <el-tooltip placement="top">
              <div slot="content" v-html="$root.labels.period_available_for_booking_tooltip"></div>
              <i class="el-icon-question am-tooltip-icon"></i>
            </el-tooltip>
          </label>
          <el-input-number
              v-model="settings.numberOfDaysAvailableForBooking"
              :min="1"
          >
          </el-input-number>
        </el-form-item>

        <!-- Phone default country code -->
        <el-form-item :label="$root.labels.default_phone_country_code+':'">
          <el-select v-model="settings.phoneDefaultCountryCode" placeholder=""
                     :class="'am-selected-flag am-selected-flag-'+settings.phoneDefaultCountryCode">
            <el-option
                v-for="country in countries"
                :key="country.id"
                :value="country.iso"
                :label="country.nicename"
            >
              <span :class="'am-flag am-flag-'+country.iso"></span>
              <span style="float: left">{{ country.nicename }}</span>
              <span style="float: right; color: #7F8BA4; font-size: 13px">{{ country.phonecode ? '+' : ''}}{{ country.phonecode }}</span>
            </el-option>
          </el-select>
        </el-form-item>

        <!-- Default required phone number input -->
        <div class="am-setting-box am-switch-box">
          <el-row type="flex" align="middle" :gutter="24">
            <el-col :span="16">
              {{ $root.labels.required_phone_number_field }}
            </el-col>
            <el-col :span="8" class="align-right">
              <el-switch
                  v-model="settings.requiredPhoneNumberField"
                  active-text=""
                  inactive-text=""
              ></el-switch>
            </el-col>
          </el-row>
        </div>

        <!-- Default required phone number input -->
        <div class="am-setting-box am-switch-box">
          <el-row type="flex" align="middle" :gutter="24">
            <el-col :span="16">
              {{ $root.labels.required_email_field }}
            </el-col>
            <el-col :span="8" class="align-right">
              <el-switch
                  v-model="settings.requiredEmailField"
                  active-text=""
                  inactive-text=""
              ></el-switch>
            </el-col>
          </el-row>
        </div>

        <!-- Default show client time zone -->
        <div class="am-setting-box am-switch-box">
          <el-row type="flex" align="middle" :gutter="24">
            <el-col :span="16">
              {{ $root.labels.show_client_time_zone }}
              <el-tooltip placement="top">
                <div slot="content" v-html="$root.labels.show_client_time_zone_tooltip"></div>
                <i class="el-icon-question am-tooltip-icon"></i>
              </el-tooltip>
            </el-col>
            <el-col :span="8" class="align-right">
              <el-switch
                  v-model="settings.showClientTimeZone"
                  active-text=""
                  inactive-text=""
              ></el-switch>
            </el-col>
          </el-row>
        </div>

        <!-- Default add to calendar -->
        <el-popover :disabled="!$root.isLite" ref="addToCalendarPop" v-bind="$root.popLiteProps"><PopLite/></el-popover>
        <div class="am-setting-box am-switch-box" :class="{'am-lite-disabled': ($root.isLite)}" v-popover:addToCalendarPop>
          <el-row type="flex" align="middle" :gutter="24" >
            <el-col :span="16">
              {{ $root.labels.add_to_calendar }}
              <el-tooltip placement="top">
                <div slot="content" v-html="$root.labels.add_to_calendar_tooltip"></div>
                <i class="el-icon-question am-tooltip-icon"></i>
              </el-tooltip>
            </el-col>
            <el-col :span="8" class="align-right">
              <el-switch
                  v-model="settings.addToCalendar"
                  active-text=""
                  inactive-text=""
                  :disabled="$root.isLite"
              ></el-switch>
            </el-col>
          </el-row>
        </div>

        <!-- Default items per page -->
        <el-form-item :label="$root.labels.default_items_per_page+':'">
          <el-select v-model="settings.itemsPerPage">
            <el-option
                v-for="item in options.itemsPerPage"
                :key="item"
                :label="item"
                :value="item">
            </el-option>
          </el-select>
        </el-form-item>

        <!-- Default page on the backend -->
        <el-form-item :label="$root.labels.default_page_on_backend+':'">
          <el-select v-model="settings.defaultPageOnBackend">
            <el-option
                v-for="item in options.defaultPageOnBackend"
                :key="item.value"
                :label="item.label"
                :value="item.value">
            </el-option>
          </el-select>
        </el-form-item>


        <!-- Google Map Api Key -->
        <el-popover :disabled="!$root.isLite" ref="gMapApiKeyPop" v-bind="$root.popLiteProps"><PopLite/></el-popover>
        <el-form-item label="placeholder" v-popover:gMapApiKeyPop>
          <label slot="label">
            {{ $root.labels.gMap_api_key }}:
            <el-tooltip placement="top">
              <div slot="content" v-html="$root.labels.gMap_api_key_tooltip"></div>
              <i class="el-icon-question am-tooltip-icon"></i>
            </el-tooltip>
          </label>
          <el-input v-model="settings.gMapApiKey" auto-complete="off" :disabled="$root.isLite"></el-input>
        </el-form-item>

        <!-- Redirect URL after appointment -->
        <el-form-item label="placeholder" prop="redirectURLAfter" :error="errors.redirectURLAfter">
          <label slot="label">
            {{ $root.labels.redirect_url_after_appointment }}:
            <el-tooltip placement="top">
              <div slot="content" v-html="$root.labels.redirect_url_after_appointment_tooltip"></div>
              <i class="el-icon-question am-tooltip-icon"></i>
            </el-tooltip>
          </label>
          <el-input
              v-model="settings.redirectUrlAfterAppointment"
              @input="clearValidation()"
              auto-complete="off"
          >
          </el-input>
        </el-form-item>

        <!-- Uploads Path -->
        <el-form-item label="placeholder">
          <label slot="label">
            {{ $root.labels.custom_fields_upload_path }}:
            <el-tooltip placement="top">
              <div slot="content" v-html="$root.labels.custom_fields_upload_path_tooltip"></div>
              <i class="el-icon-question am-tooltip-icon"></i>
            </el-tooltip>
          </label>
          <el-input
              v-model="settings.customFieldsUploadsPath"
              @input="clearValidation()"
              auto-complete="off"
          >
          </el-input>
        </el-form-item>

      </el-form>

    </div>

    <!-- Dialog Footer -->
    <div class="am-dialog-footer">
      <div class="am-dialog-footer-actions">
        <el-row>
          <el-col :sm="24" class="align-right">
            <el-button type="" @click="closeDialog" class="">{{ $root.labels.cancel }}</el-button>
            <el-button type="primary" @click="onSubmit" class="am-dialog-create">{{ $root.labels.save }}</el-button>
          </el-col>
        </el-row>
      </div>
    </div>

  </div>
</template>

<script>
  import imageMixin from '../../../js/common/mixins/imageMixin'
  import phoneCountriesMixin from '../../../js/common/mixins/phoneCountriesMixin'

  export default {

    mixins: [imageMixin, phoneCountriesMixin],

    props: {
      general: {
        type: Object
      }
    },

    data () {
      let validateRedirectURL = (rule, input, callback) => {
        this.clearValidation()
        let currentURL = this.$refs.settings.model.redirectUrlAfterAppointment
        let regex = /^((http|https):\/\/)/
        if (currentURL !== '' && !regex.test(currentURL)) {
          callback(new Error(this.$root.labels.enter_valid_url_warning))
        } else {
          callback()
        }
      }
      return {
        settings: Object.assign({}, this.general),
        rules: {
          'redirectURLAfter': [
            {validator: validateRedirectURL, trigger: 'submit'}
          ]
        },
        errors: {
          redirectURLAfter: ''
        },
        options: {
          timeSlotLength: [
            {
              label: this.$root.labels.min30,
              value: 1800
            }
          ],
          defaultAppointmentStatus: [
            {
              label: this.$root.labels.pending,
              value: 'pending'
            },
            {
              label: this.$root.labels.approved,
              value: 'approved'
            }
          ],
          minimumTime: [
            {
              label: this.$root.labels.disabled,
              value: 0
            },
            {
              label: this.$root.labels.min30,
              value: 1800
            },
            {
              label: this.$root.labels.min45,
              value: 2700
            },
            {
              label: this.$root.labels.h1,
              value: 3600
            },
            {
              label: this.$root.labels.h1min30,
              value: 5400
            },
            {
              label: this.$root.labels.h2,
              value: 7200
            },
            {
              label: this.$root.labels.h3,
              value: 10800
            },
            {
              label: this.$root.labels.h4,
              value: 14400
            },
            {
              label: this.$root.labels.h6,
              value: 21600
            },
            {
              label: this.$root.labels.h8,
              value: 28800
            },
            {
              label: this.$root.labels.h9,
              value: 32400
            },
            {
              label: this.$root.labels.h10,
              value: 36000
            },
            {
              label: this.$root.labels.h11,
              value: 39600
            },
            {
              label: this.$root.labels.h12,
              value: 43200
            },
            {
              label: this.$root.labels.day1,
              value: 86400
            },
            {
              label: this.$root.labels.days2,
              value: 172800
            },
            {
              label: this.$root.labels.days3,
              value: 259200
            },
            {
              label: this.$root.labels.days4,
              value: 345600
            },
            {
              label: this.$root.labels.days5,
              value: 432000
            },
            {
              label: this.$root.labels.days6,
              value: 518400
            },
            {
              label: this.$root.labels.week1,
              value: 604800
            },
            {
              label: this.$root.labels.weeks2,
              value: 1209600
            },
            {
              label: this.$root.labels.weeks3,
              value: 1814400
            },
            {
              label: this.$root.labels.weeks4,
              value: 2419200
            },
            {
              label: this.$root.labels.months3,
              value: 7884000
            },
            {
              label: this.$root.labels.months6,
              value: 15768000
            }
          ],
          itemsPerPage: [3, 6, 9, 12, 15, 18, 21, 24, 27, 30],
          defaultPageOnBackend: [
            {
              label: this.$root.labels.dashboard,
              value: 'Dashboard'
            },
            {
              label: this.$root.labels.calendar,
              value: 'Calendar'
            },
            {
              label: this.$root.labels.appointments,
              value: 'Appointments'
            },
            {
              label: this.$root.labels.events,
              value: 'Events'
            }
          ]
        }
      }
    },

    updated () {
      this.inlineSVG()
    },

    mounted () {
      this.inlineSVG()
      this.countries.unshift({
        'id': 0,
        'iso': 'auto',
        'nicename': 'Identify country code by user\'s IP address'
      })
    },

    methods: {
      closeDialog () {
        this.$emit('closeDialogSettingsGeneral')
      },
      onSubmit () {
        this.$refs.settings.validate((valid) => {
          if (valid) {
            this.$emit('closeDialogSettingsGeneral')
            this.$emit('updateSettings', {'general': this.settings})
          } else {
            return false
          }
        })
      },

      clearValidation () {
        this.$refs.settings.clearValidate()
      }
    }
  }
</script>
