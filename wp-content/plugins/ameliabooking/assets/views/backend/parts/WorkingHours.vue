<template>
  <div class="am-working-hours">

    <div class="am-dialog-table" v-for="(workDay, workDayIndex) in weekSchedule" :key="workDay.id">

      <el-row :gutter="20" class="am-dialog-table-head hours">
        <el-col :span="12"><span>{{ workDay.day }}</span></el-col>
        <el-col :span="12" class="am-align-right">
          <span class="am-add-element" @click="applyToAllDays(workDay)" v-if="workDayIndex === 0">{{ $root.labels.apply_to_all_days }}</span>
          <div class="am-add-element" @click="showNewHoursForm(workDay)">
            <i class="el-icon-plus"></i>
          </div>
        </el-col>
      </el-row>

      <!-- Add Work Hours -->
      <transition name="fade">
        <div class="am-add-period" v-if="workDay.form.show">
          <el-form label-position="top" :model="workDay" ref="workDay">
            <el-row :gutter="10" v-if="workDay.form.isNew">
              <el-col>
                <el-radio v-model="workDay.form.type" label="Work" v-if="!$root.isLite || ($root.isLite && workDay.periods.length === 0)">{{ $root.labels.work_hours }}</el-radio>
                <el-radio v-model="workDay.form.type" label="Break">{{ $root.labels.breaks }}</el-radio>
              </el-col>
            </el-row>

            <!-- Work Hours & Services Labels -->
            <el-row :gutter="20" style="margin-bottom: 5px;">
              <el-col :span="getColumnLength.workHours + getColumnLength.workHours"><span>{{ $root.labels.work_hours }}</span></el-col>
              <el-col :span="getColumnLength.services" v-if="categorizedServiceList && servicesCount > 1 && workDay.form.type === 'Work'">
                <span>{{ $root.labels.services }}</span>
                <el-tooltip placement="top">
                  <div slot="content" v-html="$root.labels.period_services_filter1_tooltip"></div>
                  <i class="el-icon-question am-tooltip-icon"></i>
                </el-tooltip>
              </el-col>
              <el-col :span="getColumnLength.location" v-if="locations && locations.length > 1 && workDay.form.type === 'Work'">
                <span>{{ $root.labels.location }}</span>
                <el-tooltip placement="top">
                  <div slot="content" v-html="$root.labels.period_location_filter1_tooltip"></div>
                  <i class="el-icon-question am-tooltip-icon"></i>
                </el-tooltip>
              </el-col>
            </el-row>
            <!-- Special Day Work Hours & Services Labels -->

            <el-row v-if="workDay.form.type === 'Work'" :gutter="10">
              <!-- Work Hours Start -->
              <el-col :sm="getColumnLength.workHours" :xs="12">
                <el-form-item :rules="rules.startTime" :prop="'form.data.time.0'">
                  <el-time-select
                      v-model="workDay.form.data.time[0]"
                      :picker-options="getPeriodBorderTime(workDay.form.data.time[0], workDay.form.data.time[1], true)"
                      size="mini"
                      style="margin-bottom: 14px;"
                      @change="startTimeChanged(workDay.form.data.time[0], workDay.form.data.time[1], getWorkingPeriodsInSeconds(workDay), function (value) {workDay.form.data.time[1] = value})"
                      @focus="startTimeChanged(workDay.form.data.time[0], workDay.form.data.time[1], getWorkingPeriodsInSeconds(workDay), function (value) {workDay.form.data.time[1] = value})"
                  >
                  </el-time-select>
                </el-form-item>
              </el-col>

              <!-- Work Hours End -->
              <el-col :sm="getColumnLength.workHours" :xs="12">
                <el-form-item :rules="rules.endTime" :prop="'form.data.time.1'">
                  <el-time-select
                      v-model="workDay.form.data.time[1]"
                      :picker-options="getPeriodBorderTime(workDay.form.data.time[0], workDay.form.data.time[1], false)"
                      size="mini"
                      style="margin-bottom: 14px;"
                      :disabled="workDay.form.data.time[0] === null"
                  >
                  </el-time-select>
                </el-form-item>
              </el-col>

              <!-- Services -->
              <el-col :sm="getColumnLength.services" :xs="12" v-if="categorizedServiceList && servicesCount > 1">
                <el-select
                    v-model="workDay.form.data.serviceIds"
                    multiple
                    filterable
                    :placeholder="$root.labels.period_services_filter"
                    collapse-tags
                    size="mini"
                    class="am-select-service"
                    :disabled="$root.isLite"
                >
                  <div v-for="category in categorizedServiceList"
                       v-if="category.serviceList.filter(service => service.state).length > 0"
                       :key="category.id">
                    <div class="am-drop-parent"
                         @click="selectAllInCategory(workDay.form.data, category.id)"
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

              <!-- Location -->
              <el-col :span="getColumnLength.location" :xs="12" v-if="locations && locations.length > 1">
                <el-select
                    v-model="workDay.form.data.locationId"
                    filterable
                    clearable
                    :placeholder="$root.labels.location"
                    collapse-tags
                    size="mini"
                    class="am-select-service"
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
            </el-row>

            <!-- Break -->
            <el-row v-else-if="workDay.form.type === 'Break'" :gutter="10">

              <el-col :span="12">
                <el-form-item :rules="rules.startTime" :prop="'form.data.time.0'">
                  <el-time-select
                      v-model="workDay.form.data.time[0]"
                      :picker-options="getTimeSelectOptionsForBreaks(workDay.periods.length ? workDay.periods[0].time[0] : '00:00', workDay.periods.length ? workDay.periods[workDay.periods.length - 1].time[1] : '24:00', '', workDay.form.data.time[1])"
                      size="mini"
                      style="margin-bottom: 14px;"
                  >
                  </el-time-select>
                </el-form-item>
              </el-col>

              <el-col :span="12">
                <el-form-item :rules="rules.endTime" :prop="'form.data.time.1'">
                  <el-time-select
                      v-model="workDay.form.data.time[1]"
                      :picker-options="getTimeSelectOptionsForBreaks(workDay.periods.length ? workDay.periods[0].time[0] : '00:00', workDay.periods.length ? workDay.periods[workDay.periods.length - 1].time[1] : '24:00', workDay.form.data.time[0], '')"
                      size="mini"
                      style="margin-bottom: 14px;"
                  >
                  </el-time-select>
                </el-form-item>
              </el-col>
            </el-row>

            <div class="">
              <div class="align-left">
                <el-button size="small" @click="hideHoursForm(workDay)">
                  {{ $root.labels.cancel }}
                </el-button>
                <el-button size="small" type="primary" @click="saveHoursForm(workDay)">
                  {{ $root.labels.save }}
                </el-button>
              </div>
            </div>
          </el-form>
        </div>
      </transition>

      <!-- Periods -->
      <transition-group name="fade" tag="div">
        <div class="am-period" v-for="(hoursData, indexHours) in getDayHours(workDay)" :key="indexHours + 1">
          <el-row :gutter="10" type="flex">

            <!--Hours -->
            <el-col :sm="6">
              <el-row :gutter="10">
                <el-col :span="24">
                  <span class="am-strong" :class="{'am-period-break': hoursData.type === 'Break'}">{{ hoursData.data.time[0] }} - {{ hoursData.data.time[1] }}</span>
                </el-col>
              </el-row>
            </el-col>

            <!-- Services -->
            <el-col :sm="10" class="am-flexed2">
                <span class="am-overflow-ellipsis" v-if="hoursData.type === 'Work'">
                  <el-tooltip effect="dark" placement="top-start">
                    <div slot="content">
                      <div v-for="serviceName in getServicesNames(hoursData.data.serviceIds)">{{ serviceName }}</div>
                    </div>
                    <span>
                      {{ getServicesNames(hoursData.data.serviceIds).join(', ') }}
                    </span>
                  </el-tooltip>
              </span>

            </el-col>

            <!-- Location -->
            <el-col :sm="6" :xs="12" class="am-flexed2">
              <span class="am-overflow-ellipsis" v-if="hoursData.type === 'Work'">
                {{ hoursData.data.locationId && locations.find(location => location.id === hoursData.data.locationId) ? locations.find(location => location.id === hoursData.data.locationId).name : '' }}
              </span>
            </el-col>

            <!-- Edit Hours -->
            <div class="am-flexed2">
              <div class="am-edit-element" @click="editHours(workDay, hoursData.type, hoursData.index)">
                <i class="el-icon-edit"></i>
              </div>

              <!-- Delete Hours -->
              <div class="am-delete-element" @click="deleteHours(workDay, hoursData.type, hoursData.index)">
                <i class="el-icon-minus"></i>
              </div>
            </div>

          </el-row>
        </div>

      </transition-group>

    </div>

  </div>
</template>

<script>
  import imageMixin from '../../../js/common/mixins/imageMixin'
  import dateMixin from '../../../js/common/mixins/dateMixin'
  import durationMixin from '../../../js/common/mixins/durationMixin'
  import periodMixin from '../../../js/backend/mixins/periodMixin'

  export default {

    mixins: [periodMixin, imageMixin, dateMixin, durationMixin],

    props: {
      weekSchedule: null,
      categorizedServiceList: null,
      locations: null
    },

    data () {
      return {
        rules: {
          startTime: [
            {
              required: true, message: this.$root.labels.select_time_warning, trigger: 'submit'
            }
          ],
          endTime: [
            {
              required: true, message: this.$root.labels.select_time_warning, trigger: 'submit'
            }
          ]
        }
      }
    },

    created () {
    },

    methods: {
      getWorkingPeriodsInSeconds (workDay) {
        let workPeriods = this.getDayHours(workDay).filter(period => period.type === 'Work').map(period => period.data).map(periodData => periodData.time)

        let periodsInSeconds = []

        let $this = this

        workPeriods.forEach(function (period) {
          if (!(workDay.form.data.time[0] === period[0] && workDay.form.data.time[1] === period[1])) {
            periodsInSeconds.push([$this.getStringTimeInSeconds(period[0]), $this.getStringTimeInSeconds(period[1])])
          }
        })

        return periodsInSeconds
      },

      getDayHours (day) {
        let hours = []

        day.periods.forEach(function (dayPeriod, index) {
          hours.push({
            index: index,
            type: 'Work',
            data: dayPeriod
          })
        })

        day.breaks.forEach(function (dayBreak, index) {
          hours.push({
            index: index,
            type: 'Break',
            data: dayBreak
          })
        })

        return hours.sort((a, b) => this.$moment('2000-01-01 ' + a.data.time[0] + ':00', 'YYYY-MM-DD HH:mm:ss').diff(this.$moment('2000-01-01 ' + b.data.time[0] + ':00', 'YYYY-MM-DD HH:mm:ss')))
      },

      getServicesNames (serviceIds) {
        let services = []

        if (this.categorizedServiceList) {
          this.categorizedServiceList.forEach(function (category) {
            category.serviceList.forEach(function (service) {
              if (serviceIds.indexOf(service.id) !== -1) {
                services.push(service.name)
              }
            })
          })
        }

        return services
      },

      selectAllInCategory (period, id) {
        let servicesIds = this.categorizedServiceList.find(category => category.id === id).serviceList.filter(service => service.state).map(service => service.id)

        // Deselect all services if they are already selected
        if (_.isEqual(_.intersection(servicesIds, period.serviceIds), servicesIds)) {
          period.serviceIds = _.difference(period.serviceIds, servicesIds)
        } else {
          period.serviceIds = _.uniq(period.serviceIds.concat(servicesIds))
        }
      },

      getTimeSelectOptionsForBreaks: function (minTimeWorkingHour, maxTimeWorkingHour, minTimeBreak, maxTimeBreak) {
        return {
          start: '00:00',
          end: '24:00',
          step: this.secondsToTimeSelectStep(this.getTimeSlotLength()),
          minTime: minTimeBreak || minTimeWorkingHour,
          maxTime: maxTimeBreak || maxTimeWorkingHour
        }
      },

      editHours (day, type, index) {
        let $this = this

        switch (type) {
          case ('Work'):
            day.form.show = false

            setTimeout(function () {
              day.form = {
                data: day.periods[index],
                oldData: JSON.parse(JSON.stringify(day.periods[index])),
                isNew: false,
                type: 'Work',
                show: true,
                index: index
              }

              $this.findFreePeriods($this.getWorkingPeriodsInSeconds(day))
            }, 200)

            break

          case ('Break'):
            day.form.show = false

            setTimeout(function () {
              day.form = {
                data: day.breaks[index],
                oldData: JSON.parse(JSON.stringify(day.breaks[index])),
                isNew: false,
                type: 'Break',
                show: true,
                index: index
              }
            }, 200)

            break
        }
      },

      deleteHours (day, type, index) {
        switch (type) {
          case ('Work'):
            day.periods.splice(index, 1)

            break

          case ('Break'):
            day.breaks.splice(index, 1)

            break
        }
      },

      showNewHoursForm (day) {
        day.form = {
          data: {
            time: day.form.type === 'Work' ? [day.periods.length ? (day.periods[day.periods.length - 1].time[1]) : '', ''] : ['', ''],
            id: null,
            locationId: null,
            serviceIds: [],
            periodServiceList: []
          },
          isNew: true,
          type: this.$root.isLite && day.periods.length > 0 ? 'Break' : 'Work',
          show: true,
          index: null
        }

        this.findFreePeriods(this.getWorkingPeriodsInSeconds(day))
      },

      hideHoursForm (day) {
        day.form.show = false

        switch (day.form.type) {
          case ('Work'):
            if (!day.form.isNew) {
              day.periods[day.form.index] = day.form.oldData
            }

            break

          case ('Break'):
            if (!day.form.isNew) {
              day.breaks[day.form.index] = day.form.oldData
            }

            break
        }
      },

      saveHoursForm (day) {
        this.$refs.workDay[0].validate((valid) => {
          if (valid) {
            switch (day.form.type) {
              case ('Work'):
                if (day.form.isNew) {
                  day.periods.push({
                    id: null,
                    time: day.form.data.time,
                    serviceIds: day.form.data.serviceIds,
                    locationId: day.form.data.locationId,
                    periodServiceList: day.form.data.periodServiceList
                  })
                } else {
                  day.periods[day.form.index] = day.form.data
                }

                break

              case ('Break'):
                if (day.form.isNew) {
                  day.breaks.push({
                    id: null,
                    time: day.form.data.time
                  })
                } else {
                  day.breaks[day.form.index] = day.form.data
                }

                break
            }

            day.form.show = false
          } else {
            return false
          }
        })
      },

      applyToAllDays (selectedWorkDay) {
        let periods = JSON.parse(JSON.stringify(selectedWorkDay.periods))

        periods.forEach(function (period) {
          period.id = null

          period.periodServiceList = []

          period.savedPeriodServiceList = []
        })

        let breaks = JSON.parse(JSON.stringify(selectedWorkDay.breaks))

        breaks.forEach(function (dayBreak) {
          dayBreak.id = null
        })

        this.weekSchedule.forEach(function (weekDay) {
          weekDay.id = null
          weekDay.periods = JSON.parse(JSON.stringify(periods))
          weekDay.breaks = JSON.parse(JSON.stringify(breaks))

          weekDay.time = JSON.parse(JSON.stringify(selectedWorkDay.time))
        })
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
            services: 8,
            location: 6
          }
        } else if (this.categorizedServiceList && this.servicesCount > 1) {
          return {
            workHours: 5,
            services: 14,
            location: 0
          }
        } else if (this.locations && this.locations.length > 1) {
          return {
            workHours: 5,
            services: 0,
            location: 14
          }
        } else {
          return {
            workHours: 12,
            services: 0,
            location: 0
          }
        }
      }
    },

    components: {}

  }
</script>
