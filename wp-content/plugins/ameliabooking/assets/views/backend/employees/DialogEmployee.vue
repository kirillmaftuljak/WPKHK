<template>
  <div>

    <!-- Dialog Loader -->
    <div class="am-dialog-loader" v-show="dialogLoading">
      <div class="am-dialog-loader-content">
        <img :src="$root.getUrl+'public/img/spinner.svg'" class="">
        <p>{{ $root.labels.loader_message }}</p>
      </div>
    </div>

    <!-- Dialog Content -->
    <div class="am-dialog-scrollable" :class="{'am-edit':employee.id !== 0}" v-if="employee && !dialogLoading">

      <!-- Dialog Header -->
      <div class="am-dialog-header">
        <el-row>
          <el-col :span="14">
            <h2 v-if="employee.id !== 0">{{ !$root.isLite ? $root.labels.edit_employee : $root.labels.settings_employee }} </h2>
            <h2 v-else>{{ !$root.isLite ? $root.labels.new_employee : $root.labels.settings_employee }}</h2>
          </el-col>
          <el-col :span="10" class="align-right">
            <el-button @click="closeDialog" class="am-dialog-close" size="small" icon="el-icon-close"></el-button>
          </el-col>
        </el-row>
      </div>

      <!-- Form -->
      <el-form :model="employee" ref="employee" :rules="rules" label-position="top" @submit.prevent="onSubmit">
        <el-tabs v-model="employeeTabs">

          <!-- Details -->
          <el-tab-pane :label="$root.labels.details" name="details">

            <!-- Profile Photo -->
            <div class="am-employee-profile">
              <picture-upload
                  :edited-entity="this.employee"
                  :entity-name="'employee'"
                  v-on:pictureSelected="pictureSelected"
              >
              </picture-upload>
              <h2>{{ employee.firstName }} {{ employee.lastName }}</h2>
              <span
                  v-if="typeof employee.activity !== 'undefined'"
                  class="am-employee-status-label"
                  :class="employee.activity"
              >
                {{ getEmployeeActivityLabel(employee.activity) }}
              </span>
            </div>

            <el-row :gutter="16">
              <el-col :sm="12">

                <!-- First Name -->
                <el-form-item :label="$root.labels.first_name + ':'" prop="firstName">
                  <el-input v-model="employee.firstName" auto-complete="off" @input="clearValidation()"></el-input>
                </el-form-item>
              </el-col>
              <el-col :sm="12">

                <!-- Last Name -->
                <el-form-item :label="$root.labels.last_name + ':'" prop="lastName">
                  <el-input v-model="employee.lastName" auto-complete="off" @input="clearValidation()"></el-input>
                </el-form-item>
              </el-col>
            </el-row>

            <el-row :gutter="16">
              <el-col :sm="12">

                <!-- Email -->
                <el-form-item :label="$root.labels.email + ':'" prop="email" :error="errors.email">
                  <el-input
                      v-model="employee.email"
                      auto-complete="off"
                      :placeholder="$root.labels.email_placeholder"
                      @input="clearValidation()"
                  >
                  </el-input>
                </el-form-item>
              </el-col>
              <el-col :sm="12">

                <!-- Location -->
                <el-form-item :label="$root.labels.location + ':'" prop="locationId" v-if="locations.length">
                  <el-select
                      v-model="employee.locationId"
                      placeholder=""
                      @change="clearValidation()"
                  >
                    <el-option
                        v-for="location in filteredLocations"
                        :key="location.id"
                        :label="location.name"
                        :value="location.id"
                    >
                    </el-option>
                  </el-select>
                </el-form-item>
              </el-col>
            </el-row>

            <el-row :gutter="16">
              <el-col :sm="12">

                <!-- WP User -->
                <el-form-item label="placeholder" v-if="$root.settings.capabilities.canWriteOthers === true">
                  <label slot="label">
                    {{ $root.labels.wp_user }}:
                    <el-tooltip placement="top">
                      <div slot="content" v-html="$root.labels.wp_user_employee_tooltip"></div>
                      <i class="el-icon-question am-tooltip-icon"></i>
                    </el-tooltip>
                  </label>
                    <el-popover :disabled="!$root.isLite" ref="externalIdPop" v-bind="$root.popLiteProps"><PopLite/></el-popover>
                    <el-select
                            v-model="employee.externalId"
                            ref="wpUser"
                            filterable
                            :placeholder="$root.labels.select_wp_user"
                            clearable
                            @change="clearValidation()"
                            :disabled="$root.isLite"
                            v-popover:externalIdPop
                    >
                    <div class="am-drop">
                      <div class="am-drop-create-item" @click="selectCreateNewWPUser">
                        {{ $root.labels.create_new }}
                      </div>
                      <el-option
                          :class="{'hidden' : item.value === 0}"
                          v-for="item in formOptions.wpUsers"
                          :key="item.value"
                          :label="item.label"
                          :value="item.value">
                      </el-option>
                    </div>
                  </el-select>
                </el-form-item>
              </el-col>
              <el-col :sm="12">

                <!-- Phone -->
                <el-form-item :label="$root.labels.phone + ':'">
                  <phone-input
                      :savedPhone="employee.phone"
                      @phoneFormatted="phoneFormatted"
                  >
                  </phone-input>
                </el-form-item>
              </el-col>
            </el-row>

            <!-- Google Calendar -->
            <el-row
                :gutter="24"
                v-if="$root.settings.googleCalendar && employee.id !== 0"
            >

              <!-- Google Calendar List -->
              <el-col :span="24" v-if="employee.googleCalendar && employee.googleCalendar.calendarList">
                <el-form-item label="placeholder">
                  <label slot="label">
                    {{ $root.labels.google_calendar }}:
                    <el-tooltip placement="top">
                      <div slot="content" v-html="$root.labels.google_calendar_tooltip"></div>
                      <i class="el-icon-question am-tooltip-icon"></i>
                    </el-tooltip>
                  </label>

                  <el-col :span="15" style="padding: 0">

                    <el-select
                        v-model="employee.googleCalendar.calendarId"
                        placeholder=""
                        :disabled="!employee.googleCalendar.token || googleLoading"
                        @change="clearValidation()"
                    >
                      <el-option
                          v-for="calendar in employee.googleCalendar.calendarList"
                          :key="calendar.id"
                          :label="calendar.summary"
                          :value="calendar.id"
                      >
                      </el-option>
                    </el-select>

                  </el-col>

                  <!-- Google Calendar Connect Button -->
                  <el-col :span="9" v-if="employee.googleCalendar">

                    <el-button
                        class="am-google-calendar-button"
                        :class="{ 'connected': employee.googleCalendar.token }"
                        type="primary"
                        @click="!employee.googleCalendar.token ? redirectToGoogleAuthPage() : disconnectFromGoogleAccount()"
                    >
                      <div class="am-google-calendar-button-image">
                        <img class="" :src="$root.getUrl + 'public/img/google-button.png'"/>
                      </div>
                      <span class="am-google-calendar-button-text">
                        {{ !employee.googleCalendar.token ? 'Sign in with Google' : 'Sign out from Google' }}
                      </span>
                    </el-button>
                  </el-col>

                </el-form-item>
              </el-col>

            </el-row>

            <!-- Notes -->
            <div class="am-divider"></div>
            <el-form-item :label="$root.labels.note_internal + ':'">
              <el-input
                  type="textarea"
                  :autosize="{minRows: 4, maxRows: 6}"
                  placeholder=""
                  v-model="employee.note"
                  @input="clearValidation()"
              >
              </el-input>
            </el-form-item>

          </el-tab-pane>

          <!-- Assigned Services -->
          <el-tab-pane :label="$root.labels.assigned_services" name="services"
                       v-if="$root.settings.capabilities.canWriteOthers === true">
            <div class="am-dialog-table"
                 v-for="category in editCategorizedServiceList"
                 v-if="category.serviceList.length > 0"
                 :key="category.id"
            >

              <!-- Header -->
              <el-row :gutter="10" class="am-dialog-table-head">

                <!-- Check All -->
                <el-col :span="2">
                  <el-checkbox
                      v-model="category.state"
                      @change="handleCheckAllInCategory(category.id)">
                  </el-checkbox>
                </el-col>

                <!-- Category Name -->
                <el-col class="am-three-dots" :span="10"><span>{{ category.name }}</span></el-col>

                <!-- Price Label -->
                <el-col :span="6">{{ $root.labels.price }}</el-col>

                <!-- Capacity Label -->
                <el-col :span="6">{{ $root.labels.capacity }}</el-col>
              </el-row>

              <!-- Body -->
              <el-row :gutter="10" type="flex" align="middle" v-for="item in category.serviceList" :key="item.value">

                <!-- Checkbox -->
                <el-col :span="2">
                  <el-checkbox
                      v-model="item.state"
                      @change="handleCheckService(category, item)"
                  >
                  </el-checkbox>
                </el-col>

                <!-- Service Name -->
                <el-col :span="10"><span>{{ item.name }}</span></el-col>

                <!-- Service Price -->
                <el-col :span="6">
                  <money
                      v-model="item.price"
                      v-bind="moneyComponentData"
                      class="el-input__inner"
                      :disabled="!item.state"
                      @input="clearValidation()"
                  >
                  </money>
                </el-col>

                <!-- Min. Capacity -->
                <el-col :span="3">
                  <el-popover :disabled="!$root.isLite" ref="minCapacityPop" v-bind="$root.popLiteProps"><PopLite/></el-popover>
                  <el-input-number
                      v-model="item.minCapacity"
                      :disabled="!item.state || $root.isLite"
                      :value="item.minCapacity"
                      :min="1"
                      @input="checkCapacityLimits(item)"
                      :controls=false
                      v-popover:minCapacityPop
                  >
                  </el-input-number>
                </el-col>

                <!-- Max. Capacity -->
                <el-col :span="3">
                  <el-popover :disabled="!$root.isLite" ref="maxCapacityPop" v-bind="$root.popLiteProps"><PopLite/></el-popover>
                  <el-input-number
                      v-model="item.maxCapacity"
                      :disabled="!item.state || $root.isLite"
                      :value="item.maxCapacity"
                      :min="item.minCapacity"
                      @input="checkCapacityLimits(item)"
                      :controls=false
                      v-popover:maxCapacityPop
                  >
                  </el-input-number>
                </el-col>
              </el-row>
            </div>
          </el-tab-pane>

          <!-- Work Hours -->
          <el-tab-pane
              :label="$root.labels.work_hours" name="hours"
              v-if="$root.settings.capabilities.canWriteOthers === true || $root.settings.roles.allowConfigureSchedule === true"
          >
            <working-hours
                :weekSchedule="editWeekDayList"
                :categorizedServiceList="editCategorizedServiceList"
                :locations="locations"
            >
            </working-hours>
          </el-tab-pane>

          <!-- Days Off -->
          <el-tab-pane
              :label="$root.labels.days_off" name="off"
              v-if="$root.settings.capabilities.canWriteOthers === true || $root.settings.roles.allowConfigureDaysOff === true"
          >
            <BlockLite/>

            <div class="am-days-off">
              <div class="am-employee-days-off">
                <days-off
                    @changeDaysOff="changeDaysOff"
                    @showCompanyDaysOffSettingsDialog="showCompanyDaysOffSettingsDialog"
                    :daysOff="employee.id !== 0 ? employee.dayOffList : []"
                    :listedDaysOff="companyDaysOff"
                >
                </days-off>
              </div>
            </div>
          </el-tab-pane>

          <!-- Special Days -->
          <el-tab-pane
              :label="$root.labels.special_days" name="special"
              v-if="$root.settings.capabilities.canWriteOthers === true || $root.settings.roles.allowConfigureSpecialDays === true"
          >
            <BlockLite/>

            <div class="am-special-days">
              <special-days
                  @changeSpecialDays="changeSpecialDays"
                  :specialDays="employee.specialDayList"
                  :locations="locations"
                  :categorizedServiceList="editCategorizedServiceList">
              </special-days>
            </div>
          </el-tab-pane>

        </el-tabs>

      </el-form>
    </div>

    <!-- Dialog Actions -->
    <dialog-actions
        v-if="employee && !dialogLoading"
        formName="employee"
        urlName="users/providers"
        :isNew="employee.id === 0"
        :entity="employee"
        :getParsedEntity="getParsedEntity"
        @errorCallback="errorCallback"
        @validationFailCallback="validationFailCallback"
        :isDisabledDuplicate="isDisabledDuplicate"
        :hasIcons="true"

        :status="{
          on: 'visible',
          off: 'hidden'
        }"

        :buttonText="{
          confirm: {
            status: {
              yes: employee.status === 'visible' ? $root.labels.visibility_hide : $root.labels.visibility_show,
              no: $root.labels.no
            }
          }
        }"

        :action="{
          haveAdd: true,
          haveEdit: true,
          haveStatus: $root.settings.capabilities.canWriteOthers === true && !$root.isLite,
          haveRemove: $root.settings.capabilities.canDelete === true && !$root.isLite,
          haveRemoveEffect: true,
          haveDuplicate: $root.settings.capabilities.canWriteOthers === true && !$root.isLite
        }"

        :message="{
          success: {
            save: $root.labels.employee_saved,
            remove: $root.labels.employee_deleted,
            show: $root.labels.employee_visible,
            hide: $root.labels.employee_hidden
          },
          confirm: {
            remove: $root.labels.confirm_delete_employee,
            show: $root.labels.confirm_show_employee,
            hide: $root.labels.confirm_hide_employee,
            duplicate: $root.labels.confirm_duplicate_employee
          }
        }"
    >
    </dialog-actions>

  </div>
</template>

<script>
  import DaysOff from '../parts/DaysOff.vue'
  import WorkingHours from '../parts/WorkingHours.vue'
  import SpecialDays from '../parts/SpecialDays.vue'
  import DialogActions from '../parts/DialogActions.vue'
  import PhoneInput from '../../parts/PhoneInput.vue'
  import { Money } from 'v-money'
  import PictureUpload from '../parts/PictureUpload.vue'
  import imageMixin from '../../../js/common/mixins/imageMixin'
  import dateMixin from '../../../js/common/mixins/dateMixin'
  import durationMixin from '../../../js/common/mixins/durationMixin'
  import notifyMixin from '../../../js/backend/mixins/notifyMixin'
  import priceMixin from '../../../js/common/mixins/priceMixin'
  import employeeMixin from '../../../js/common/mixins/employeeMixin'

  export default {
    mixins: [imageMixin, dateMixin, durationMixin, notifyMixin, priceMixin, employeeMixin],

    props: {
      locations: null,
      employee: null,
      editCategorizedServiceList: null,
      editWeekDayList: null,
      companyDaysOff: null,
      futureAppointments: null,
      isDisabledDuplicate: null
    },

    data () {
      return {
        appointmentsServices: [],
        dialogLoading: true,
        employeeTabs: 'details',
        errors: {
          email: ''
        },
        executeUpdate: true,
        formOptions: {
          wpUsers: []
        },
        googleAuthURL: '',
        googleLoading: false,
        rules: {
          firstName: [
            {required: true, message: this.$root.labels.enter_first_name_warning, trigger: 'submit'}
          ],
          lastName: [
            {required: true, message: this.$root.labels.enter_last_name_warning, trigger: 'submit'}
          ],
          email: [
            {required: true, message: this.$root.labels.enter_email_warning, trigger: 'submit'},
            {type: 'email', message: this.$root.labels.enter_valid_email_warning, trigger: 'submit'}
          ],
          locationId: [
            {
              required: this.visibleLocations().length > 0,
              message: this.$root.labels.enter_location_warning,
              trigger: 'blur'
            }
          ]
        }
      }
    },

    created () {
      this.instantiateDialog()
    },

    updated () {
      this.instantiateDialog()
    },

    methods: {
      instantiateDialog () {
        if ((this.employee !== null || (this.employee !== null && this.employee.id === 0)) && this.executeUpdate === true) {
          if (this.$root.settings.capabilities.canWriteOthers) {
            if (this.employee.id !== 0) {
              this.getWPUsers(this.employee.externalId)
            } else {
              this.getWPUsers(0)
            }
          }

          // Send request for Google Authorization URL if token is not already set
          if (this.employee.googleCalendar && !this.employee.googleCalendar.token) {
            this.getGoogleAuthURL(false)
          }

          let locations = this.visibleLocations()

          if (locations.length === 1) {
            this.employee.locationId = locations[0].id
          }

          let $this = this

          this.editCategorizedServiceList.forEach(function (catItem) {
            $this.handleCheckSingleInCategory(catItem)
          })

          if (this.employee.id in this.futureAppointments) {
            this.appointmentsServices = this.futureAppointments[this.employee.id]
          }

          // Remove loading when employee is connected to google calendar
          if (!this.$root.settings.capabilities.canWriteOthers) {
            this.dialogLoading = false
            this.inlineDialogEmployeeSVG()
          }

          this.executeUpdate = false
        }
      },

      checkCapacityLimits (item) {
        this.clearValidation()
        if (item.minCapacity > item.maxCapacity) {
          item.maxCapacity = item.minCapacity
        }
      },

      validationFailCallback () {
        this.employeeTabs = 'details'
      },

      errorCallback (responseData) {
        let $this = this

        $this.errors.email = ''

        setTimeout(function () {
          $this.errors.email = responseData.message
          $this.employeeTabs = 'details'
        }, 200)
      },

      closeDialog () {
        this.$emit('closeDialog')
      },

      getParsedEntity () {
        this.employee.serviceList = this.getParsedServiceList(this.editCategorizedServiceList)
        this.employee.weekDayList = this.getParsedWeekDayList(this.editWeekDayList)

        return this.employee
      },

      getParsedServiceList (list) {
        let serviceList = []

        list.forEach(function (catItem) {
          catItem.serviceList.forEach(function (catSerItem) {
            if (catSerItem.state) {
              serviceList.push(Object.assign({}, catSerItem))
            }
          })
        })

        return serviceList
      },

      changeSelectedPeriodServices (service) {
        if (service.state) {
          // set to old state if period is already saved
          this.editWeekDayList.forEach(function (weekDay) {
            weekDay.periods.forEach(function (period) {
              if ('savedPeriodServiceList' in period) {
                period.savedPeriodServiceList.forEach(function (savedPeriodService) {
                  if (savedPeriodService.serviceId === service.id) {
                    period.periodServiceList.push(savedPeriodService)
                    period.serviceIds.push(service.id)
                  }
                })
              }
            })
          })
        } else {
          this.editWeekDayList.forEach(function (weekDay) {
            weekDay.periods.forEach(function (period) {
              period.periodServiceList.forEach(function (periodService, index) {
                if (periodService.serviceId === service.id) {
                  period.periodServiceList.splice(index, 1)
                }
              })

              period.serviceIds.forEach(function (serviceId, index) {
                if (serviceId === service.id) {
                  period.serviceIds.splice(index, 1)
                }
              })
            })
          })
        }
      },

      getParsedWeekDayList (list) {
        let weekDayList = []
        let $this = this

        list.forEach(function (weekDayItem, weekDayItemIndex) {
          let timeOutList = []

          weekDayItem.breaks.forEach(function (timeOutItem) {
            if (timeOutItem.time && timeOutItem.time.length && timeOutItem.time[0] && timeOutItem.time[1]) {
              timeOutList.push(
                {
                  id: timeOutItem.id,
                  startTime: timeOutItem.time[0] + ':00',
                  endTime: timeOutItem.time[1] + ':00'
                }
              )
            }
          })

          let periodList = []

          let minStartTime = 86400
          let maxEndTime = 0

          weekDayItem.periods.forEach(function (periodItem) {
            if (periodItem.time && periodItem.time.length && periodItem.time[0] && periodItem.time[1]) {
              let periodStartTime = $this.getStringTimeInSeconds(periodItem.time[0])
              let periodEndTime = $this.getStringTimeInSeconds(periodItem.time[1])

              minStartTime = periodStartTime < minStartTime ? periodStartTime : minStartTime
              maxEndTime = periodEndTime > maxEndTime ? periodEndTime : maxEndTime

              let periodServiceList = []

              periodItem.serviceIds.forEach(function (serviceId) {
                let oldPeriodService = periodItem.periodServiceList.find(periodService => periodService.serviceId === serviceId)
                periodServiceList.push({
                  id: oldPeriodService ? oldPeriodService.id : null,
                  serviceId: serviceId
                })
              })

              periodList.push(
                {
                  id: periodItem.id,
                  startTime: periodItem.time[0] + ':00',
                  endTime: periodItem.time[1] + ':00',
                  locationId: periodItem.locationId,
                  periodServiceList: periodServiceList
                }
              )
            }
          })

          if (periodList.length) {
            weekDayList.push(
              {
                id: weekDayItem.id,
                dayIndex: weekDayItemIndex + 1,
                startTime: $this.secondsToTimeSelectStep(minStartTime) + ':00',
                endTime: $this.secondsToTimeSelectStep(maxEndTime) + ':00',
                timeOutList: timeOutList,
                periodList: periodList
              }
            )
          }
        })

        return weekDayList
      },

      changeDaysOff: function () {},

      changeSpecialDays: function () {},

      handleCheckAllInCategory (catId) {
        let $this = this
        this.clearValidation()

        let category = this.editCategorizedServiceList.find(category => category.id === catId)

        category.serviceList.forEach(function (service) {
          if ($this.appointmentsServices.indexOf(service.id) !== -1 && category.state === false) {
            $this.notify(
              $this.$root.labels.error,
              $this.$root.labels.service_provider_remove_fail_all + ' ' + service.name + ' ' + $this.$root.labels.service,
              'error'
            )
          } else {
            service.state = category.state
            $this.changeSelectedPeriodServices(service)
          }
        })
      },

      handleCheckService (category, service) {
        if (this.appointmentsServices.indexOf(service.id) !== -1) {
          service.state = true
          this.notify(this.$root.labels.error, this.$root.labels.service_provider_remove_fail, 'error')
        }

        this.changeSelectedPeriodServices(service)

        this.handleCheckSingleInCategory(category)
      },

      handleCheckSingleInCategory (category) {
        this.clearValidation()
        let checkedItemsLength = 0

        category.serviceList.forEach(function (serItem) {
          if (serItem.state) {
            checkedItemsLength++
          }
        })

        category.state = (checkedItemsLength === category.serviceList.length)
      },

      phoneFormatted (phone) {
        this.clearValidation()
        this.employee.phone = phone
      },

      pictureSelected (pictureFullPath, pictureThumbPath) {
        this.employee.pictureFullPath = pictureFullPath
        this.employee.pictureThumbPath = pictureThumbPath
      },

      getWPUsers (currentId) {
        this.$http.get(`${this.$root.getAjaxUrl}/users/wp-users`, {
          params: {
            id: currentId,
            role: 'provider'
          }
        }).then(response => {
          this.formOptions.wpUsers = response.data.data.users
          this.formOptions.wpUsers.unshift({'value': 0, 'label': this.$root.labels.create_new})

          if (this.formOptions.wpUsers.map(user => user.value).indexOf(this.employee.externalId) === -1) {
            this.employee.externalId = ''
          }

          this.dialogLoading = false
          this.inlineDialogEmployeeSVG()
        })
      },

      visibleLocations () {
        return this.locations.filter(location => location.status === 'visible' || (this.employee && location.status === 'hidden' && location.id === this.employee.locationId))
      },

      showCompanyDaysOffSettingsDialog: function () {},

      clearValidation () {
        if (typeof this.$refs.employee !== 'undefined') {
          this.$refs.employee.clearValidate()
        }
      },

      selectCreateNewWPUser () {
        this.employee.externalId = 0
        this.$refs.wpUser.blur()
      },

      getGoogleAuthURL: function () {},

      redirectToGoogleAuthPage: function () {},

      disconnectFromGoogleAccount: function () {},

      inlineDialogEmployeeSVG () {
        let $this = this
        setTimeout(function () {
          $this.inlineSVG()
        }, 10)
      }
    },

    computed: {
      filteredLocations () {
        return this.visibleLocations()
      }
    },

    components: {
      PhoneInput,
      PictureUpload,
      DaysOff,
      WorkingHours,
      SpecialDays,
      DialogActions,
      Money
    }
  }
</script>
