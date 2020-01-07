<template>
  <div>
    <div class="am-dialog-scrollable">

      <!-- Dialog Header -->
      <div class="am-dialog-header">
        <el-row>
          <el-col :span="20">
            <h2>{{ $root.labels.google_calendar_settings }}</h2>
          </el-col>
          <el-col :span="4" class="align-right">
            <el-button @click="closeDialog" class="am-dialog-close" size="small" icon="el-icon-close"></el-button>
          </el-col>
        </el-row>
      </div>
      <!-- /Dialog Header -->

      <BlockLite/>
<!---->
      <!-- Form -->
      <el-form :model="settings" ref="settings" label-position="top" @submit.prevent="onSubmit" :class="{'am-lite-container-disabled': $root.isLite}">


        <!-- Client ID -->
        <el-form-item :label="$root.labels.google_client_id+':'">
          <el-row :gutter="24">

            <el-col :span="21">
              <el-input v-model.trim="settings.clientID" auto-complete="off"></el-input>
            </el-col>

            <el-col :span="3">
              <el-tooltip class="am-google-calendar-tooltip" effect="dark" placement="top">
                <div slot="content" v-html="$root.labels.google_credentials_obtain"></div>
                <el-button
                    class="am-google-calendar-button am-button-icon"
                    type="primary"
                    @click="redirectToDocumentation()"
                >
                  <img class="svg" :src="$root.getUrl + 'public/img/question.svg'"/>
                </el-button>
              </el-tooltip>
            </el-col>

          </el-row>
        </el-form-item>
        <!-- /Client ID -->

        <!-- Client Secret -->
        <el-form-item :label="$root.labels.google_client_secret+':'">
          <el-row :gutter="24">

            <el-col :span="21">
              <el-input v-model.trim="settings.clientSecret" auto-complete="off"></el-input>
            </el-col>

            <el-col :span="3">
              <el-tooltip class="am-google-calendar-tooltip" effect="dark" placement="top">
                <div slot="content" v-html="$root.labels.google_credentials_obtain"></div>
                <el-button
                    class="am-google-calendar-button am-button-icon"
                    type="primary"
                    @click="redirectToDocumentation()"
                >
                  <img class="svg" :src="$root.getUrl + 'public/img/question.svg'"/>
                </el-button>
              </el-tooltip>
            </el-col>

          </el-row>
        </el-form-item>
        <!-- /Client Secret -->

        <!-- Redirect URI -->
        <el-form-item label="placeholder">
          <label slot="label">
            {{ $root.labels.google_redirect_uri }}:
            <el-tooltip placement="top">
              <div slot="content" v-html="$root.labels.google_redirect_uri_tooltip"></div>
              <i class="el-icon-question am-tooltip-icon"></i>
            </el-tooltip>
          </label>
          <el-input v-model="settings.redirectURI" auto-complete="off" readonly="readonly"></el-input>
        </el-form-item>
        <!-- /Redirect URI -->

        <!-- Event Title -->
        <el-form-item label="placeholder">
          <label slot="label">
            {{ $root.labels.event_title }}:
            <el-tooltip placement="top">
              <div slot="content" v-html="$root.labels.event_title_tooltip"></div>
              <i class="el-icon-question am-tooltip-icon"></i>
            </el-tooltip>
          </label>
          <el-input v-model="settings.eventTitle" auto-complete="off"></el-input>
        </el-form-item>
        <!-- /Event Title -->

        <!-- Event Description -->
        <el-form-item label="placeholder">
          <label slot="label">
            {{ $root.labels.event_description }}:
            <el-tooltip placement="top">
              <div slot="content" v-html="$root.labels.event_description_tooltip"></div>
              <i class="el-icon-question am-tooltip-icon"></i>
            </el-tooltip>
          </label>
          <el-input v-model="settings.eventDescription" type="textarea" auto-complete="off"></el-input>
        </el-form-item>
        <!-- /Event Description -->

        <!-- Insert Pending Appointments -->
        <div class="am-setting-box am-switch-box">
          <el-row type="flex" align="middle" :gutter="24">
            <el-col :span="20">
              {{ $root.labels.insert_pending_appointments }}
              <el-tooltip placement="top">
                <div slot="content" v-html="$root.labels.insert_pending_appointments_tooltip"></div>
                <i class="el-icon-question am-tooltip-icon"></i>
              </el-tooltip>
            </el-col>
            <el-col :span="4" class="align-right">
              <el-switch
                  v-model="settings.insertPendingAppointments"
                  active-text=""
                  inactive-text=""
              >
              </el-switch>
            </el-col>
          </el-row>
        </div>
        <!-- /Insert Pending Appointments -->

        <!-- Add Event's Attendees -->
        <div class="am-setting-box am-switch-box">
          <el-row type="flex" align="middle" :gutter="24">
            <el-col :span="20">
              {{ $root.labels.customers_as_attendees }}
              <el-tooltip placement="top">
                <div slot="content" v-html="$root.labels.customers_as_attendees_tooltip"></div>
                <i class="el-icon-question am-tooltip-icon"></i>
              </el-tooltip>
            </el-col>
            <el-col :span="4" class="align-right">
              <el-switch
                  v-model="settings.addAttendees"
                  active-text=""
                  inactive-text=""
                  @change="onChangeAddAttendees()"
              >
              </el-switch>
            </el-col>
          </el-row>
        </div>
        <!-- /Add Event's Attendees -->

        <!-- Show Attendees -->
        <div class="am-setting-box am-switch-box">
          <el-row type="flex" align="middle" :gutter="24">
            <el-col :span="20">
              {{ $root.labels.show_attendees }}
              <el-tooltip placement="top">
                <div slot="content" v-html="$root.labels.show_attendees_tooltip"></div>
                <i class="el-icon-question am-tooltip-icon"></i>
              </el-tooltip>
            </el-col>
            <el-col :span="4" class="align-right">
              <el-switch
                  v-model="settings.showAttendees"
                  active-text=""
                  inactive-text=""
              >
              </el-switch>
            </el-col>
          </el-row>
        </div>
        <!-- /Show Attendees -->

        <!-- Send Event Invitation Email -->
        <div class="am-setting-box am-switch-box">
          <el-row type="flex" align="middle" :gutter="24">
            <el-col :span="20">
              {{ $root.labels.send_event_invitation_email }}
              <el-tooltip placement="top">
                <div slot="content" v-html="$root.labels.send_event_invitation_email_tooltip"></div>
                <i class="el-icon-question am-tooltip-icon"></i>
              </el-tooltip>
            </el-col>
            <el-col :span="4" class="align-right">
              <el-switch
                  v-model="settings.sendEventInvitationEmail"
                  :disabled="!settings.addAttendees"
                  active-text=""
                  inactive-text=""
              >
              </el-switch>
            </el-col>
          </el-row>
        </div>
        <!-- /Send Event Invitation Email -->

        <!-- Remove Google Calendar Busy Slots -->
        <div class="am-setting-box am-switch-box">
          <el-row type="flex" align="middle" :gutter="24">
            <el-col :span="20">
              {{ $root.labels.remove_google_busy_slots }}
              <el-tooltip placement="top">
                <div slot="content" v-html="$root.labels.remove_google_busy_slots_tooltip"></div>
                <i class="el-icon-question am-tooltip-icon"></i>
              </el-tooltip>
            </el-col>
            <el-col :span="4" class="align-right">
              <el-switch
                  v-model="settings.removeGoogleCalendarBusySlots"
                  active-text=""
                  inactive-text=""
              >
              </el-switch>
            </el-col>
          </el-row>
        </div>
        <!-- /Remove Google Calendar Busy Slots -->

        <!-- Maximum Number Of Events Returned -->
        <el-form-item label="placeholder">
          <label slot="label">
            {{ $root.labels.number_of_events_returned }}:
            <el-tooltip placement="top">
              <div slot="content" v-html="$root.labels.number_of_events_returned_tooltip"></div>
              <i class="el-icon-question am-tooltip-icon"></i>
            </el-tooltip>
          </label>
          <el-select
              v-model="settings.maximumNumberOfEventsReturned"
              :disabled="!settings.removeGoogleCalendarBusySlots"
          >
            <el-option
                v-for="item in options.maximumNumberOfEventsReturned"
                :key="item"
                :label="item"
                :value="item">
            </el-option>
          </el-select>
        </el-form-item>
        <!-- /Maximum Number Of Events Returned -->

      </el-form>
      <!-- /Form -->

    </div>

    <!-- Dialog Footer -->
    <div class="am-dialog-footer" :class="{'am-lite-container-disabled': $root.isLite}">
      <div class="am-dialog-footer-actions">
        <el-row>
          <el-col :sm="24" class="align-right">
            <el-button type="" @click="closeDialog" class="">Cancel</el-button>
            <el-button type="primary" @click="onSubmit" class="am-dialog-create">Save</el-button>
          </el-col>
        </el-row>
      </div>
    </div>
    <!-- /Dialog Footer -->

  </div>
</template>

<script>
  import imageMixin from '../../../js/common/mixins/imageMixin'

  export default {

    mixins: [imageMixin],

    props: {
      googleCalendar: {
        type: Object
      }
    },

    data () {
      return {
        options: {
          maximumNumberOfEventsReturned: [50, 100, 200, 500, 1000, 2000, 2500]
        },
        readonly: true,
        settings: Object.assign({}, this.googleCalendar)
      }
    },

    methods: {
      closeDialog () {
        this.$emit('closeDialogSettingsGoogleCalendar')
      },

      onSubmit () {
        this.$emit('closeDialogSettingsGoogleCalendar')
        this.$emit('updateSettings', {'googleCalendar': this.settings})
      },

      onChangeAddAttendees () {
        if (this.settings.addAttendees === false) {
          this.settings.sendEventInvitationEmail = false
        }
      },

      redirectToDocumentation () {
        window.open('https://wpamelia.com/configuring-google-calendar/', '_blank')
      }
    }
  }
</script>