<template>
  <div class="am-wrap">
    <div id="am-settings" class="am-body">

      <!-- Page Header -->
      <page-header></page-header>

      <!-- Spinner -->
      <div class="am-spinner am-section" v-show="!fetched">
        <img :src="$root.getUrl + 'public/img/spinner.svg'"/>
      </div>

      <!-- Settings -->
      <div v-show="fetched" class="am-section am-settings-cards">
        <el-row :gutter="48">

          <!-- General -->
          <el-col :md="8">
            <div class="am-settings-card">
              <h3><img :src="$root.getUrl + 'public/img/setting.svg'" class="svg"/> {{ $root.labels.general }}</h3>
              <p>{{ $root.labels.general_settings_description }}</p>
              <p class="link" @click="showDialogSettingsGeneral">
                {{ $root.labels.view_general_settings }}
              </p>
            </div>
          </el-col>

          <!-- Company -->
          <el-col :md="8">
            <div class="am-settings-card">
              <h3><img :src="$root.getUrl + 'public/img/company.svg'" class="svg"/> {{ $root.labels.company }}</h3>
              <p>{{ $root.labels.company_settings_description }}</p>
              <p class="link" @click="showDialogSettingsCompany">
                {{ $root.labels.view_company_settings }}
              </p>
            </div>
          </el-col>

          <!-- Notification -->
          <el-col :md="8">
            <div class="am-settings-card">
              <h3><img :src="$root.getUrl + 'public/img/email-settings.svg'" class="svg"/> {{ $root.labels.notifications
                }}</h3>
              <p>{{ $root.labels.notifications_settings_description }}</p>
              <p class="link" @click="showDialogSettingsNotifications">
                {{ $root.labels.view_notifications_settings }}
              </p>
            </div>
          </el-col>

        </el-row>

        <el-row :gutter="48">

          <!-- Working Hours & Days Off -->
          <el-col :md="8" v-if="!$root.isLite">
            <div class="am-settings-card">
              <h3><img :src="$root.getUrl+'public/img/calendar.svg'" class="svg"/> {{ $root.labels.work_hours_days_off
                }}</h3>
              <p>{{ $root.labels.days_off_settings_description }}</p>
              <p class="link" @click="showDialogSettingsWorkHoursDaysOff">
                {{ $root.labels.view_days_off_settings }}
              </p>
            </div>
          </el-col>
          <el-col :md="8" v-else>
            <div class="am-settings-card">
              <h3><img :src="$root.getUrl + 'public/img/customer.svg'" class="svg"/>
                {{ $root.labels.provider_details_settings }}
              </h3>
              <p>{{ $root.labels.provider_details_settings_description }}</p>
              <p class="link" @click="showDialogEmployee">
                {{ $root.labels.view_provider_details_settings }}
              </p>
            </div>
          </el-col>

          <!-- Payments -->
          <el-col :md="8">
            <div class="am-settings-card">
              <h3><img :src="$root.getUrl+'public/img/credit-card.svg'" class="svg"/> {{ $root.labels.payments }}</h3>
              <p>{{ $root.labels.payments_settings_description }}</p>
              <p class="link" @click="showDialogSettingsPayments">
                {{ $root.labels.view_payments_settings }}
              </p>
            </div>
          </el-col>

          <!-- Google Calendar -->
          <el-col :md="8">
            <div class="am-settings-card">
              <h3><img :src="$root.getUrl+'public/img/google.svg'" class="svg"/> {{ $root.labels.google_calendar }}</h3>
              <p>{{ $root.labels.google_calendar_settings_description }}</p>
              <p class="link" @click="showDialogSettingsGoogleCalendar">
                {{ $root.labels.view_google_calendar_settings }}
              </p>
            </div>
          </el-col>

        </el-row>

        <el-row :gutter="48">

          <!-- Appointments -->
          <el-col :md="8">
            <div class="am-settings-card">
              <h3><img :src="$root.getUrl + 'public/img/appointment-settings.svg'" class="svg"/>
                {{ $root.labels.appointments }}
              </h3>
              <p>{{ $root.labels.appointments_settings_description }}</p>
              <p class="link" @click="showDialogSettingsAppointments">
                {{ $root.labels.view_appointments_settings }}
              </p>
            </div>
          </el-col>

          <!-- Activation -->
          <el-col :md="8">
            <div class="am-settings-card">
              <h3><img :src="$root.getUrl + 'public/img/purchase-code.svg'" class="svg"/>
                {{ $root.labels.activation }}
              </h3>
              <p>{{ $root.labels.activation_settings_description }}</p>
              <p class="link" @click="showDialogSettingsActivation">
                {{ $root.labels.view_activation_settings }}
              </p>
            </div>
          </el-col>

          <!-- Roles -->
          <el-col :md="8">
            <div class="am-settings-card">
              <h3><img :src="$root.getUrl + 'public/img/roles.svg'" class="svg"/>
                {{ $root.labels.roles_settings }}
              </h3>
              <p>{{ $root.labels.roles_settings_description }}</p>
              <p class="link" @click="showDialogSettingsRoles">
                {{ $root.labels.view_roles_settings_description }}
              </p>
            </div>
          </el-col>

        </el-row>

        <el-row :gutter="48">

          <!-- Labels -->
          <el-col :md="8">
            <div class="am-settings-card">
              <h3><img :src="$root.getUrl + 'public/img/labels.svg'" class="svg"/>
                {{ $root.labels.labels }}
              </h3>
              <p>{{ $root.labels.labels_settings_description }}</p>
              <p class="link" @click="showDialogSettingsLabels">
                {{ $root.labels.view_labels_settings }}
              </p>
            </div>
          </el-col>

          <!-- WebHooks -->
          <el-col :md="8">
            <div class="am-settings-card">
              <h3><img :src="$root.getUrl + 'public/img/web-hook.svg'" class="svg"/>
                {{ $root.labels.web_hooks }}
              </h3>
              <p>{{ $root.labels.labels_settings_web_hooks }}</p>
              <p class="link" @click="showDialogSettingsWebHooks">
                {{ $root.labels.view_web_hooks_settings }}
              </p>
            </div>
          </el-col>

        </el-row>

      </div>

      <!-- Dialog General -->
      <transition name="slide">
        <el-dialog
            class="am-side-dialog am-dialog-settings"
            :visible.sync="dialogSettingsGeneral"
            :show-close="false"
            v-if="dialogSettingsGeneral"
        >
          <dialog-settings-general
              @closeDialogSettingsGeneral="dialogSettingsGeneral = false"
              @updateSettings="updateSettings"
              :general="settings.general"
          >
          </dialog-settings-general>
        </el-dialog>
      </transition>

      <!-- Dialog Company -->
      <transition name="slide">
        <el-dialog
            class="am-side-dialog am-dialog-settings"
            :visible.sync="dialogSettingsCompany"
            :show-close="false"
            v-if="dialogSettingsCompany"
        >
          <dialog-settings-company
              @closeDialogSettingsCompany="dialogSettingsCompany = false"
              @updateSettings="updateSettings"
              :company="settings.company"
          >
          </dialog-settings-company>
        </el-dialog>
      </transition>

      <!-- Dialog Notification -->
      <transition name="slide">
        <el-dialog
            class="am-side-dialog am-dialog-settings"
            :visible.sync="dialogSettingsNotifications"
            :show-close="false"
            v-if="dialogSettingsNotifications"
        >
          <dialog-settings-notifications
              @closeDialogSettingsNotifications="dialogSettingsNotifications = false"
              @updateSettings="updateSettings"
              :notifications="settings.notifications"
          >
          </dialog-settings-notifications>
        </el-dialog>
      </transition>

      <!-- Dialog Work Hours & Days Off -->
      <transition name="slide">
        <el-dialog
            class="am-side-dialog am-dialog-settings"
            :visible.sync="dialogSettingsWorkHoursDaysOff"
            :show-close="false"
            v-if="dialogSettingsWorkHoursDaysOff"
        >
          <dialog-settings-work-hours-days-off
              @closeDialogSettingsWorkHoursDaysOff="dialogSettingsWorkHoursDaysOff = false"
              @updateSettings="updateSettings"
              :daysOff="settings.daysOff"
              :weekSchedule="settings.weekSchedule"
          >
          </dialog-settings-work-hours-days-off>
        </el-dialog>
      </transition>

      <!-- Dialog Payment -->
      <transition name="slide">
        <el-dialog
            class="am-side-dialog am-dialog-settings"
            :visible.sync="dialogSettingsPayments"
            :show-close="false"
            v-if="dialogSettingsPayments"
        >
          <dialog-settings-payments
              @closeDialogSettingsPayments="dialogSettingsPayments = false"
              @updateSettings="updateSettings"
              :payments="settings.payments"
          >
          </dialog-settings-payments>
        </el-dialog>
      </transition>

      <!-- Dialog Google Calendar -->
      <transition name="slide">
        <el-dialog
            class="am-side-dialog am-dialog-settings"
            :visible.sync="dialogSettingsGoogleCalendar"
            :show-close="false"
            v-if="dialogSettingsGoogleCalendar"
        >
          <dialog-settings-google-calendar
              @closeDialogSettingsGoogleCalendar="dialogSettingsGoogleCalendar = false"
              @updateSettings="updateSettings"
              :googleCalendar="settings.googleCalendar"
          >
          </dialog-settings-google-calendar>
        </el-dialog>
      </transition>

      <!-- Dialog Labels -->
      <transition name="slide">
        <el-dialog
            class="am-side-dialog am-dialog-settings"
            :visible.sync="dialogSettingsLabels"
            :show-close="false"
            v-if="dialogSettingsLabels"
        >
          <dialog-settings-labels
              @closeDialogSettingsLabels="dialogSettingsLabels = false"
              @updateSettings="updateSettings"
              :labels="settings.labels"
          >
          </dialog-settings-labels>
        </el-dialog>
      </transition>

      <!-- Dialog Activation -->
      <transition name="slide">
        <el-dialog
            class="am-side-dialog am-dialog-settings"
            :visible.sync="dialogSettingsActivation"
            :show-close="false"
            v-if="dialogSettingsActivation"
        >
          <dialog-settings-activation
              @closeDialogSettingsActivation="dialogSettingsActivation = false"
              @updateSettings="updateSettings"
              :activation="settings.activation"
          >
          </dialog-settings-activation>
        </el-dialog>
      </transition>

      <!-- Dialog Settings Roles -->
      <transition name="slide">
        <el-dialog
            class="am-side-dialog am-dialog-settings"
            :visible.sync="dialogSettingsRoles"
            :show-close="false"
            v-if="dialogSettingsRoles"
        >
          <dialog-settings-roles
              @closeDialogSettingsRoles="dialogSettingsRoles = false"
              @updateSettings="updateSettings"
              :roles="settings.roles"
          >
          </dialog-settings-roles>
        </el-dialog>
      </transition>

      <!-- Dialog Appointments -->
      <transition name="slide">
        <el-dialog
            class="am-side-dialog am-dialog-settings"
            :visible.sync="dialogSettingsAppointments"
            :show-close="false"
            v-if="dialogSettingsAppointments"
        >
          <dialog-settings-appointments
                @closeDialogSettingsAppointments="dialogSettingsAppointments = false"
                @updateSettings="updateSettings"
                :appointments="settings.appointments"
            >
          </dialog-settings-appointments>
        </el-dialog>
      </transition>

      <transition name="slide" >
        <el-dialog
            class="am-side-dialog am-dialog-employee"
            :visible.sync="employeeInfo.dialogEmployee"
            :show-close="false"
            v-if="employeeInfo.dialogEmployee"
        >
          <dialog-employee
              :locations=[]
              :employee="employeeInfo.employee"
              :futureAppointments="employeeInfo.futureAppointments"
              :editCategorizedServiceList="employeeInfo.editCategorizedServiceList"
              :editWeekDayList="employeeInfo.editWeekDayList"
              :companyDaysOff="$root.settings.daysOff"
              @saveCallback="saveEmployeeCallback"
              @closeDialog="employeeInfo.dialogEmployee = false"
              @showCompcanyDaysOffSettingsDialog="employeeInfo.dialogCompanyDaysOffSettings = true"
              :isDisabledDuplicate="true"
          >
          </dialog-employee>
        </el-dialog>
      </transition>

      <DialogLite :isEmployeeDialog="employeeInfo.dialogEmployee"/>

      <!-- Dialog WebHooks -->
      <transition name="slide">
        <el-dialog
            class="am-side-dialog am-dialog-settings"
            :visible.sync="dialogSettingsWebHooks"
            :show-close="false"
            v-if="dialogSettingsWebHooks"
        >
          <dialog-settings-web-hooks
              @closeDialogSettingsWebHooks="dialogSettingsWebHooks = false"
              @updateSettings="updateSettings"
              :webHooks="settings.webHooks"
          >
          </dialog-settings-web-hooks>
        </el-dialog>
      </transition>

      <!-- Help Button -->
      <el-col :md="6" class="">
        <a class="am-help-button" href="https://wpamelia.com/general-settings/" target="_blank">
          <i class="el-icon-question"></i> {{ $root.labels.need_help }}?
        </a>
      </el-col>

    </div>
  </div>
</template>

<script>
  import DialogSettingsGeneral from './DialogSettingsGeneral.vue'
  import DialogSettingsCompany from './DialogSettingsCompany.vue'
  import DialogSettingsNotifications from './DialogSettingsNotifications.vue'
  import DialogSettingsWorkHoursDaysOff from './DialogSettingsWorkHoursDaysOff.vue'
  import DialogSettingsPayments from './DialogSettingsPayments.vue'
  import DialogSettingsGoogleCalendar from './DialogSettingsGoogleCalendar.vue'
  import DialogSettingsLabels from './DialogSettingsLabels.vue'
  import DialogSettingsWebHooks from './DialogSettingsWebHooks.vue'
  import DialogSettingsActivation from './DialogSettingsActivation.vue'
  import DialogSettingsRoles from './DialogSettingsRoles.vue'
  import DialogEmployee from './../employees/DialogEmployee.vue'
  import PageHeader from '../parts/PageHeader.vue'
  import imageMixin from '../../../js/common/mixins/imageMixin'
  import notifyMixin from '../../../js/backend/mixins/notifyMixin'
  import helperMixin from '../../../js/backend/mixins/helperMixin'

  import DialogSettingsAppointments from './DialogSettingsAppointments.vue'
  import employeeMixin from '../../../js/common/mixins/employeeMixin'
  import settingsMixin from '../../../js/common/mixins/settingsMixin'

  export default {

    mixins: [imageMixin, notifyMixin, helperMixin, employeeMixin, settingsMixin],

    data () {
      return {
        employeeInfo: {
          isDuplicated: false,
          dialogCompanyDaysOffSettings: false,
          dialogEmployee: false,
          editCategorizedServiceList: null,
          editWeekDayList: [],
          employee: null,
          futureAppointments: {}
        },
        dialogSettingsGeneral: false,
        dialogSettingsCompany: false,
        dialogSettingsNotifications: false,
        dialogSettingsWorkHoursDaysOff: false,
        dialogSettingsPayments: false,
        dialogSettingsGoogleCalendar: false,
        dialogSettingsLabels: false,
        dialogSettingsActivation: false,
        dialogSettingsRoles: false,
        dialogSettingsAppointments: false,
        dialogSettingsWebHooks: false,
        fetched: false,
        settings: {}
      }
    },

    created () {
      this.fetchData()
    },

    updated () {
      this.inlineSVG()
    },

    mounted () {
      this.inlineSVG()
    },

    methods: {
      fetchData () {
        this.$http.get(`${this.$root.getAjaxUrl}/settings`)
          .then(response => {
            this.fetched = true
            let settings = response.data.data.settings

            settings.weekSchedule.forEach(function (weekDay) {
              weekDay.form = {
                type: null,
                isNew: null,
                index: null,
                show: false,
                data: {}
              }

              if (weekDay.time[0] && weekDay.time[1]) {
                if (!('periods' in weekDay) || weekDay.periods.length === 0) {
                  weekDay.periods = [
                    {
                      time: [
                        weekDay.time[0],
                        weekDay.time[1]
                      ],
                      id: null,
                      serviceIds: [],
                      periodServiceList: []
                    }
                  ]
                } else {
                  weekDay.periods.forEach(function (period) {
                    period['id'] = null
                    period['serviceIds'] = []
                    period['periodServiceList'] = []
                    period['time'] = [
                      period.time[0],
                      period.time[1]
                    ]
                  })
                }
              } else {
                weekDay['periods'] = []
              }
            })

            this.settings = settings
            this.openActiveSettingFromQueryParameter()
            this.handleEnvatoActivation()
          })
          .catch(e => {
            console.log(e.message)
            this.fetched = true
          })
      },

      updateSettings (settings, message = null, notify = true) {
        for (let category in settings) {
          if (settings.hasOwnProperty(category)) {
            this.settings[category] = settings[category]
          }
        }

        this.settings['customization'] = null

        this.$http.post(`${this.$root.getAjaxUrl}/settings`, this.settings)
          .then(response => {
            this.$root.settings = response.data.data.settings

            if (notify === true) {
              this.notify(
                this.$root.labels.success,
                message || this.$root.labels.settings_saved,
                'success'
              )
            }
          })
          .catch(e => {
            this.notify(this.$root.labels.error, e.message, 'error')
          })
      },

      showDialogSettingsGeneral () {
        this.dialogSettingsGeneral = true
      },

      showDialogSettingsCompany () {
        this.dialogSettingsCompany = true
      },

      showDialogSettingsWorkHoursDaysOff () {
        this.dialogSettingsWorkHoursDaysOff = true
      },

      showDialogSettingsNotifications () {
        this.dialogSettingsNotifications = true
      },

      showDialogSettingsPayments () {
        this.dialogSettingsPayments = true
      },

      showDialogSettingsGoogleCalendar () {
        this.dialogSettingsGoogleCalendar = true
      },

      showDialogSettingsLabels () {
        this.dialogSettingsLabels = true
      },

      showDialogSettingsActivation () {
        this.dialogSettingsActivation = true
      },

      showDialogSettingsRoles () {
        this.dialogSettingsRoles = true
      },

      showDialogEmployee: function () {
        this.$http.get(`${this.$root.getAjaxUrl}/entities`, {
          params: {
            types: ['employees', 'categories', 'appointments'],
            page: 'settings'
          }
        })
          .then(response => {
            let $this = this

            let appointments = response.data.data.appointments['futureAppointments']

            this.employeeInfo.employee = response.data.data.employees.length ? response.data.data.employees[0] : this.getInitEmployeeObject()

            this.employeeInfo.employee.specialDayList = []
            this.employeeInfo.employee.dayOffList = []

            this.employeeInfo.employee.weekDayList.forEach(function (weekDay) {
              weekDay.periodList = weekDay.periodList.sort((a, b) => $this.$moment('2000-01-01 ' + a.startTime + ':00', 'YYYY-MM-DD HH:mm:ss').diff($this.$moment('2000-01-01 ' + b.startTime + ':00', 'YYYY-MM-DD HH:mm:ss')))
              weekDay.timeOutList = weekDay.timeOutList.sort((a, b) => $this.$moment('2000-01-01 ' + a.startTime + ':00', 'YYYY-MM-DD HH:mm:ss').diff($this.$moment('2000-01-01 ' + b.startTime + ':00', 'YYYY-MM-DD HH:mm:ss')))
            })

            for (let key in appointments) {
              if (this.employeeInfo.employee.id !== parseInt(appointments[key].providerId)) {
                continue
              }

              let serviceId = appointments[key].serviceId
              let providerId = appointments[key].providerId

              if (!(providerId in this.employeeInfo.futureAppointments)) {
                this.employeeInfo.futureAppointments[providerId] = []
                this.employeeInfo.futureAppointments[providerId].push(serviceId)
              } else if (this.employeeInfo.futureAppointments[providerId].indexOf(serviceId) === -1) {
                this.employeeInfo.futureAppointments[providerId].push(serviceId)
              }
            }

            this.employeeInfo.employee.googleCalendar = {
              calendarId: null,
              calendarList: []
            }

            this.employeeInfo.editWeekDayList = this.getParsedWeekDayList(this.employeeInfo.employee)
            this.employeeInfo.editCategorizedServiceList = this.getParsedCategorizedServiceList(this.employeeInfo.employee, response.data.data.categories)

            this.employeeInfo.editCategorizedServiceList.forEach(function (category) {
              if (!$this.employeeInfo.employee.id) {
                category.serviceList.forEach(function (service) {
                  service.state = true
                })

                category.state = true
              }
            })

            this.employeeInfo.dialogEmployee = true
          })
          .catch(e => {
          })
      },

      showDialogSettingsAppointments () {
        this.dialogSettingsAppointments = true
      },

      showDialogSettingsWebHooks () {
        this.dialogSettingsWebHooks = true
      },

      openActiveSettingFromQueryParameter () {
        let queryParams = this.getUrlQueryParams(window.location.href)

        let activeSetting = queryParams['activeSetting']

        if (activeSetting) {
          if (activeSetting === 'activation') {
            this.showDialogSettingsActivation()
            let redirectURL = this.removeURLParameter(window.location.href, 'activeSetting')
            history.pushState(null, null, redirectURL + '#/settings')
          }
        }
      },

      handleEnvatoActivation () {
        let queryParams = this.getUrlQueryParams(window.location.href)

        if (queryParams['valid'] && queryParams['domainRegistered']) {
          this.settings.activation.envatoTokenEmail = typeof queryParams['envatoTokenEmail'] !== 'undefined' ? queryParams['envatoTokenEmail'] : ''
          this.settings.activation.active = queryParams['valid'] === 'true' && queryParams['domainRegistered'] === 'true'
          this.updateSettings(this.settings, null, false)
          this.showDialogSettingsActivation()
        }
      },

      saveEmployeeCallback () {
        this.employeeInfo.dialogEmployee = false
      }
    },

    components: {
      PageHeader,
      DialogSettingsGeneral,
      DialogSettingsCompany,
      DialogSettingsNotifications,
      DialogSettingsWorkHoursDaysOff,
      DialogSettingsPayments,
      DialogSettingsGoogleCalendar,
      DialogSettingsLabels,
      DialogSettingsWebHooks,
      DialogSettingsRoles,
      DialogSettingsAppointments,
      DialogSettingsActivation,
      DialogEmployee
    }

  }
</script>
