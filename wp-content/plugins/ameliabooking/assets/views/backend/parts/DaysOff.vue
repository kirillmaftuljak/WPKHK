<template>
  <div class="am-days-off" :class="{'am-lite-container-disabled': $root.isLite}">

    <div class="am-dialog-table">
      <el-row :gutter="20" type="flex" align="middle">

        <!-- Year Picker -->
        <el-col :sm="10">
          <el-date-picker
              v-model="filterDate"
              type="year"
              :clearable="false"
              :placeholder="$root.labels.pick_a_year"
              @change="filterDaysOff"
          >
          </el-date-picker>
        </el-col>

        <!-- Repeat Every Year -->
        <el-col :sm="8" class="hidden-xs-only hide-on-mobile">
          <span class="type repeat"></span>
          {{ $root.labels.repeat_every_year }}
        </el-col>

        <!-- Once Off -->
        <el-col :sm="6" class="hidden-xs-only hide-on-mobile">
          <span class="type once"></span>
          {{ $root.labels.once_off }}
        </el-col>
      </el-row>
    </div>

    <!-- Employees Days Off -->
    <div class="am-employee-days-off">

      <!-- Days Off Table -->
      <div class="am-dialog-table">

        <!-- Days Off Table Header -->
        <h4 v-if="listedDaysOff.length > 0">{{ $root.labels.employee_days_off }}</h4>
        <el-row :gutter="20" class="am-dialog-table-head days">
          <el-col :span="12"><span>{{ $root.labels.date }}</span></el-col>
          <el-col :span="12"><span>{{ $root.labels.day_off_name }}</span></el-col>
        </el-row>

        <!-- Day Off Row -->
        <el-row :gutter="20" type="flex" align="middle" class="am-day-off"
                v-for="(dayOff, index) in yearDaysOff" :key="index"
        >

          <!-- Day Off Type and Date -->
          <el-col :span="12">
            <span class="type" :class="{ 'repeat': dayOff.repeat, 'once': !dayOff.repeat }"></span>
            <el-tooltip
                effect="dark"
                :content="dayOff.startDate === dayOff.endDate ? getFrontedFormattedDate (dayOff.startDate) :
              getFrontedFormattedDate(dayOff.startDate) + ' - ' + getFrontedFormattedDate(dayOff.endDate)"
                placement="top-start">
              <span>
                {{ dayOff.startDate === dayOff.endDate ? getFrontedFormattedDate (dayOff.startDate) :
                getFrontedFormattedDate(dayOff.startDate) + ' - ' + getFrontedFormattedDate(dayOff.endDate) }}
              </span>
            </el-tooltip>
          </el-col>

          <!-- Day Off Name -->
          <el-col :span="8">
            <el-tooltip
                effect="dark"
                :content="dayOff.name"
                placement="top-start">
              <span>{{ dayOff.name }}</span>
            </el-tooltip>
          </el-col>

          <!-- Day Off Edit and Delete -->
          <el-col :span="4" class="align-right">
            <div class="am-edit-element" @click="editDayOff(dayOff)">
              <i class="el-icon-edit"></i>
            </div>
            <div class="am-delete-element" @click="deleteDayOff(dayOff)">
              <i class="el-icon-minus"></i>
            </div>
          </el-col>

        </el-row>

        <!-- Add Day Off button -->
        <el-row>
          <el-col>
            <div class="am-add-element" @click="addDayOff">
              <i class="el-icon-plus"></i> {{ $root.labels.add_day_off }}
            </div>
          </el-col>
        </el-row>

      </div>

      <!-- Days Off Form -->
      <transition name="fade">
        <div class="am-day-off-add" v-show="showDayOffForm">

          <!-- Days Off Name -->
          <el-form :model="model" ref="model" :rules="rules" label-position="top" @submit.prevent="saveDayOff">
            <el-form-item :label="$root.labels.day_off_name + ':'" prop="dayOffName">
              <el-input v-model="model.dayOffName" auto-complete="off"
                        :placeholder="$root.labels.add_day_off_placeholder"
              >
              </el-input>
            </el-form-item>

            <!-- Days Off Date -->
            <el-form-item :label="$root.labels.date + ':'" prop="dayOffDates">
              <v-date-picker
                  v-model="model.dayOffDates"
                  mode='range'
                  popover-visibility="focus"
                  popover-direction="top"
                  tint-color='#1A84EE'
                  :show-day-popover=false
                  :input-props='{class: "el-input__inner"}'
                  :is-expanded=false
                  :is-required=true
                  input-class="el-input__inner"
                  :placeholder="$root.labels.pick_a_date_or_range"
                  :formats="vCalendarFormats"
              >
              </v-date-picker>
            </el-form-item>

            <!-- Days Off Controls -->
            <el-row :gutter="20">

              <!-- Days Off Repeat -->
              <el-col :sm="12">
                <el-checkbox v-model="model.dayOffRepeat" class="am-semi-strong">
                  {{ $root.labels.days_off_repeat_yearly }}
                </el-checkbox>
              </el-col>

              <!-- Days Off Buttons -->
              <el-col :sm="12" class="align-right">
                <el-button size="small" @click="showDayOffForm = !showDayOffForm">{{ $root.labels.cancel }}</el-button>
                <el-button size="small" type="primary" @click="saveDayOff" class="am-dialog-create">
                  {{ $root.labels.days_off_add }}
                </el-button>
              </el-col>
            </el-row>
          </el-form>
        </div>
      </transition>
    </div>

    <!-- Company Days Off -->
    <div class="am-company-days-off" v-if="listedDaysOff.length > 0">

      <!-- Listed Days Off Table -->
      <div class="am-dialog-table">

        <!-- Listed Days Off Table Header -->
        <h4>{{ $root.labels.company_days_off }}</h4>
        <el-row :gutter="20" class="am-dialog-table-head days">
          <el-col :span="12"><span>{{ $root.labels.date }}</span></el-col>
          <el-col :span="12"><span>{{ $root.labels.day_off_name }}</span></el-col>
        </el-row>

        <!-- Listed Day Off Row -->
        <el-row :gutter="20" type="flex" align="middle" class="am-day-off"
                v-for="(dayOff, index) in yearListedDaysOff" :key="index"
        >

          <!-- Listed Day Off Type and Date -->
          <el-col :span="12">
            <span class="type" :class="{ 'repeat': dayOff.repeat, 'once': !dayOff.repeat }"></span>
            <span>
              {{ dayOff.startDate === dayOff.endDate ? getFrontedFormattedDate (dayOff.startDate) :
              getFrontedFormattedDate(dayOff.startDate) + ' - ' + getFrontedFormattedDate(dayOff.endDate) }}
            </span>
          </el-col>

          <!-- Listed Day Off Name -->
          <el-col :span="8">
            <span>{{ dayOff.name }}</span>
          </el-col>

        </el-row>

        <!-- Edit Company Days Off -->
        <el-row>
          <el-col :span="24">
            <div class="am-add-element" @click="editCompanyDaysOffSettings">{{ $root.labels.edit_company_days_off }}
            </div>
          </el-col>
        </el-row>

      </div>

    </div>

  </div>
</template>

<script>
  import imageMixin from '../../../js/common/mixins/imageMixin'
  import dateMixin from '../../../js/common/mixins/dateMixin'

  export default {

    mixins: [imageMixin, dateMixin],

    props: {
      daysOff: {
        type: Array
      },
      listedDaysOff: {
        default: () => [],
        type: Array
      }
    },

    data () {
      return {
        editedDayOffIndex: -1,
        filterDate: this.$moment(),
        model: {
          dayOffDates: null,
          dayOffId: 0,
          dayOffName: '',
          dayOffRepeat: 0
        },
        rules: {
          dayOffName: [
            {required: true, message: this.$root.labels.days_off_name_warning, trigger: 'submit'}
          ],
          dayOffDates: [
            {required: true, message: this.$root.labels.days_off_date_warning, trigger: 'submit'}
          ]
        },
        settings: this.daysOff.slice(0),
        showDayOffForm: false,
        yearDaysOff: [],
        yearListedDaysOff: []
      }
    },

    mounted () {
      this.filterDaysOff()
    },

    methods: {
      editDayOff: function () {},

      addDayOff: function () {},

      saveDayOff: function () {},

      deleteDayOff: function () {},

      filter: function () {},

      editCompanyDaysOffSettings: function () {},

      filterDaysOff: function () {}

    },

    computed: {

      filterYear: function () {},

      dayOff: function () {}

    },

    watch: {

      settings: function () {},

      listedDaysOff: function () {}

    },

    components: {}

  }
</script>