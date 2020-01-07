<template>
  <div :class="{'am-lite-container-disabled': $root.isLite}">

  <!-- Employees Special Days -->
  <div class="am-employee-special-days">

    <!-- Special Days List -->
    <div class="am-dialog-table">

      <!-- Special Days Header -->
      <el-row :gutter="20" class="am-dialog-table-head days">
        <el-col :span="24"><span>{{ $root.labels.special_days }}</span></el-col>
      </el-row>
      <!-- /Special Days Header -->

      <!-- Special Days Row -->
      <div
          class="am-special-day"
          v-for="(specialDay, index) in specialDays" :key="index + 1"
      >
        <el-row>

          <!-- Special Day Date, Time, Services -->
          <el-col :span="20">

            <!-- Special Day Date -->
            <div class="am-special-day-data">
              <span class="am-strong">{{ $root.labels.date }}: </span>
              <span>
                {{ getFrontedFormattedDate(specialDay.startDate) }} - {{ getFrontedFormattedDate(specialDay.endDate) }}
              </span>
            </div>
            <!-- /Special Day Date -->

            <!-- Special Day Time -->
            <div class="am-special-day-data">
              <span class="am-strong">{{ $root.labels.work_hours }}: </span>
              <span v-for="(specialDayPeriod, index) in specialDay.periodList" :key="index + 1">
                ({{ getFrontedFormattedTime(specialDayPeriod.startTime) }} - {{ getFrontedFormattedTime(specialDayPeriod.endTime) }})
                {{ (index !== specialDayPeriod.length - 1 && specialDayPeriod.length > 1) ? '; ' : '' }}
              </span>
            </div>
            <!-- /Special Day Time -->

            <!-- Special Day Services -->
            <div class="am-special-day-data">
              <span class="am-strong">{{ $root.labels.special_days_reflect_services }}: </span>
              <span class="am-special-day-services">
                <span class="am-special-day-service">
                  {{ getSpecialDayServices(specialDay) }}
                </span>
              </span>
            </div>
            <!-- /Special Day Services -->

          </el-col>
          <!-- /Special Day Date, Time, Services -->

          <!-- Special Day Edit and Delete -->
          <el-col :span="4" class="align-right">
            <div class="am-edit-element" @click="editSpecialDay(index)">
              <i class="el-icon-edit"></i>
            </div>
            <div class="am-delete-element" @click="deleteSpecialDay(index)">
              <i class="el-icon-minus"></i>
            </div>
          </el-col>
          <!-- /Special Day Edit and Delete -->

        </el-row>
      </div>
      <!-- /Special Days Row -->

      <!-- Add Special Day button -->
      <el-row>
        <el-col>
          <div class="am-add-element" @click="addSpecialDay">
            <i class="el-icon-plus"></i> {{ $root.labels.add_special_day }}
          </div>
        </el-col>
      </el-row>
      <!-- /Add Special Day button -->

    </div>
    <!-- /Special Days List -->

    <!-- Special Day Form -->
    <transition name="fade">
      <div class="am-special-day-add" v-show="showSpecialDayForm">
        <el-form :model="specialDayModel" ref="specialDayModel" label-position="top" @submit.prevent="" id="specialDay">

          <!-- Special Day Date -->
          <el-row :gutter="20">
            <el-col :sm="24" style="overflow: visible;">
              <el-form-item :label="$root.labels.date + ':'" prop="dateRange" :rules="rules.dateRange">
                <v-date-picker
                    v-model="specialDayModel.dateRange"
                    @input="clearValidation"
                    mode='range'
                    popover-visibility="focus"
                    popover-direction="bottom"
                    tint-color='#1A84EE'
                    :show-day-popover=false
                    :input-props='{class: "el-input__inner"}'
                    :is-expanded=false
                    :is-required=true
                    input-class="el-input__inner"
                    :placeholder="$root.labels.pick_a_date_or_range"
                    :formats="vCalendarFormats"
                    style="margin-bottom: 20px;"
                    :available-dates="{ start: this.$moment().subtract(1, 'days').toDate() }"
                >
                </v-date-picker>
              </el-form-item>
            </el-col>
          </el-row>
          <!-- /Special Day Date -->

          <!-- Special Day Work Hours & Services Labels -->
          <el-row :gutter="20" class="am-dialog-table-head hours" style="margin-bottom: 5px;">
            <el-col :span="getColumnLength.workHours + getColumnLength.workHours"><span>{{ $root.labels.work_hours }}</span></el-col>
            <el-col :span="getColumnLength.services" v-if="categorizedServiceList && servicesCount > 1">
              <span>{{ $root.labels.services }}</span>
              <el-tooltip placement="top">
                <div slot="content" v-html="$root.labels.period_services_filter2_tooltip"></div>
                <i class="el-icon-question am-tooltip-icon"></i>
              </el-tooltip>
            </el-col>
            <el-col :span="getColumnLength.location" v-if="locations && locations.length > 1">
              <span>{{ $root.labels.location }}</span>
              <el-tooltip placement="top">
                <div slot="content" v-html="$root.labels.period_location_filter2_tooltip"></div>
                <i class="el-icon-question am-tooltip-icon"></i>
              </el-tooltip>
            </el-col>
          </el-row>
          <!-- Special Day Work Hours & Services Labels -->

          <!-- Special Day Work and Break Hours -->
          <transition-group name="fade">
            <div class="am-period" v-for="(period, index) in specialDayModel.periodList" :key="index + 1">
              <el-row :gutter="10" type="flex">

                <!-- Work Hours Start & End -->
                <el-col :span="getColumnLength.workHours + getColumnLength.workHours">
                  <el-row :gutter="10">

                    <!-- Work Hours Start -->
                    <el-col :span="12">
                      <el-form-item :prop="'periodList.' + index + '.startTime'" :rules="rules.startTime">
                        <el-time-select
                            v-model="period.startTime"
                            :picker-options="getTimeSelectOptionsWithLimits(getPeriodBorderTime(specialDayModel, index)[0], period.endTime)"
                            size="mini"
                            :is-required=true
                            @change="clearValidation"
                        >
                        </el-time-select>
                      </el-form-item>
                    </el-col>
                    <!-- /Work Hours Start -->

                    <!-- Work Hours End -->
                    <el-col :span="12">
                      <el-form-item :prop="'periodList.' + index + '.endTime'" :rules="rules.endTime">
                        <el-time-select
                            v-model="period.endTime"
                            :picker-options="getTimeSelectOptionsWithLimits(period.startTime, getPeriodBorderTime(specialDayModel, index)[1])"
                            size="mini"
                            :is-required=true
                            @change="clearValidation"
                        >
                        </el-time-select>
                      </el-form-item>
                    </el-col>
                    <!-- /Work Hours End -->

                  </el-row>
                </el-col>
                <!-- /Work Hours Start & End -->

                <!-- Services -->
                <el-col :span="getColumnLength.services" v-if="categorizedServiceList && servicesCount > 1">

                  <el-select
                      v-model="period.serviceIds"
                      multiple
                      filterable
                      :placeholder="$root.labels.period_services_filter"
                      collapse-tags
                      size="mini"
                      class="am-select-service"
                      v-if="categorizedServiceList"
                      @change="clearValidation()"
                  >
                    <div v-for="category in categorizedServiceList"
                         v-if="category.serviceList.filter(service => service.state).length > 0"
                         :key="category.id">
                      <div class="am-drop-parent"
                         @click="selectAllInCategory(period, category.id)"
                      >
                        <span>{{ category.name }}</span>
                      </div>
                      <el-option
                          v-for="service in category.serviceList"
                          :key="service.id"
                          :label="service.name"
                          :value="service.id"
                          class="am-drop-child"
                          v-if="service.state"
                      >
                      </el-option>
                    </div>
                  </el-select>

                </el-col>
                <!-- /Services -->

                <!-- Location -->
                <el-col :span="getColumnLength.location" :xs="12" v-if="locations && locations.length > 1">
                  <el-select
                      v-model="period.locationId"
                      filterable
                      clearable
                      :placeholder="$root.labels.location"
                      collapse-tags
                      size="mini"
                      class="am-select-service"
                      v-if="locations.length"
                  >
                    <el-option
                        v-for="location in locations"
                        :key="location.id"
                        :label="location.name"
                        :value="location.id"
                    >
                    </el-option>
                  </el-select>
                </el-col>
                <!-- /Location -->

                <!-- Delete Period -->
                <el-col v-if="specialDayModel.periodList.length > 1" :span="2">
                  <div class="am-delete-element" @click="deletePeriod(index)">
                    <i class="el-icon-minus"></i>
                  </div>
                </el-col>
                <!-- /Delete Period -->

              </el-row>
            </div>
          </transition-group>
          <!-- /Special Day Work and Break Hours -->

          <!-- Add Period Button -->
          <el-row>
            <el-col :sm="24" class="align-left">
              <div class="am-add-elements">
                <div class="am-add-element" @click="addPeriod">
                  <i class="el-icon-plus"></i> {{ $root.labels.add_period }}
                </div>
              </div>
            </el-col>
          </el-row>
          <!-- /Add Period Button -->

          <!-- Save Special Day Button -->
          <el-row>
            <el-col :sm="24" class="align-right">
              <el-button size="small" @click="showSpecialDayForm = !showSpecialDayForm">{{ $root.labels.cancel }}
              </el-button>
              <el-button size="small" type="primary" @click="saveSpecialDay" class="am-dialog-create">
                {{ $root.labels.save_special_day }}
              </el-button>
            </el-col>
          </el-row>
          <!-- /Save Special Day Button -->

        </el-form>
      </div>
    </transition>
    <!-- /Special Day Form -->

  </div>
  <!-- /Employees Special Days -->
  </div>

</template>

<script>
  import imageMixin from '../../../js/common/mixins/imageMixin'
  import dateMixin from '../../../js/common/mixins/dateMixin'
  import durationMixin from '../../../js/common/mixins/durationMixin'
  import helperMixin from '../../../js/backend/mixins/helperMixin'

  export default {

    mixins: [imageMixin, dateMixin, durationMixin, helperMixin],

    props: {
      categorizedServiceList: null,
      locations: null,
      specialDays: null
    },

    data () {
      return {
        model: {},

        rules: {
          dateRange: [
            {required: true, message: this.$root.labels.special_day_date_warning, trigger: 'submit'}
          ],
          startTime: [
            {required: true, message: this.$root.labels.special_day_start_time_warning, trigger: 'submit'}
          ],
          endTime: [
            {required: true, message: this.$root.labels.special_day_end_time_warning, trigger: 'submit'}
          ]
        },

        specialDayModel: this.getInitSpecialDayModel(),

        showSpecialDayForm: false
      }
    },

    mounted () {
    },

    methods: {

      getInitSpecialDayModel () {
        return {
          index: null,
          id: null,
          dateRange: null,
          periodList: [
            {
              id: null,
              startTime: '',
              endTime: '',
              locationId: null,
              serviceIds: [],
              periodServiceList: []
            }
          ]
        }
      },

      addSpecialDay: function () {},

      editSpecialDay: function () {},

      deleteSpecialDay: function () {},

      saveSpecialDay: function () {},

      getSpecialDayServices: function () {},

      selectAllInCategory: function () {},

      getPeriodBorderTime (workDay, index) {
        let nextPeriodStart = '24:00'
        let previousPeriodEnd = '00:00'

        workDay.periodList.forEach(function (period, periodIndex) {
          if (index === periodIndex - 1) {
            nextPeriodStart = period.startTime
          }

          if (index === periodIndex + 1) {
            previousPeriodEnd = period.endTime
          }
        })

        return [
          this.secondsToTimeSelectStep(this.getStringTimeInSeconds(previousPeriodEnd) - this.getTimeSlotLength()),
          this.secondsToTimeSelectStep(this.getStringTimeInSeconds(nextPeriodStart) + this.getTimeSlotLength())
        ]
      },

      addPeriod: function () {},

      deletePeriod: function (index) {
        this.specialDayModel.periodList.splice(index, 1)
      },

      clearValidation () {
        if (typeof this.$refs.specialDayModel !== 'undefined') {
          this.$refs.specialDayModel.clearValidate()
        }
      }
    },

    computed: {
      servicesCount () {
        let servicesCount = 0

        this.categorizedServiceList.forEach(function (category) {
          servicesCount += category.serviceList.length
        })

        return servicesCount
      },

      getColumnLength () {
        if (this.categorizedServiceList && this.servicesCount > 1 && this.locations && this.locations.length > 1) {
          return {
            workHours: 5,
            services: 7,
            location: 5
          }
        } else if (this.categorizedServiceList && this.servicesCount > 1) {
          return {
            workHours: 5,
            services: 12,
            location: 0
          }
        } else if (this.locations && this.locations.length > 1) {
          return {
            workHours: 5,
            services: 0,
            location: 12
          }
        } else {
          return {
            workHours: 11,
            services: 0,
            location: 0
          }
        }
      }
    }

  }
</script>