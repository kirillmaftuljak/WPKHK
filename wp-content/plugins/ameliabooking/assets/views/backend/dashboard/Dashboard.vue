<template>
  <div class="am-wrap">
    <div id="am-dashboard" class="am-body">

      <!-- Page Header -->
      <page-header @changeFilter="changeFilter" :params="params"></page-header>

      <!-- Spinner -->
      <div class="am-spinner am-section" v-show="!fetched">
        <img :src="$root.getUrl + 'public/img/spinner.svg'"/>
      </div>

      <!-- Statistics -->
      <div v-if="fetched === true">
        <div class="am-hello am-section">
          <div class="am-user-name">
            <h1 v-if="currentUser !== null">{{$root.labels.hello_message_part0}} {{currentUser.firstName}} {{currentUser.lastName}} <img :src="$root.getUrl + 'public/img/wave.png'"></h1>
            <div class="am-user-alert">
              <span>{{$root.labels.hello_message_part1}} <img :src="$root.getUrl + 'public/img/check.png'"> <span>{{todayAppointmentsCount.approved !== null ? todayAppointmentsCount.approved : 0}}</span> {{$root.labels.approved_appointments.toLowerCase()}} {{$root.labels.hello_message_part2}} <img :src="$root.getUrl + 'public/img/clock.png'"> {{todayAppointmentsCount.pending !== null ? todayAppointmentsCount.pending : 0}} {{$root.labels.pending_appointments.toLowerCase()}} {{$root.labels.hello_message_part3}}</span>
            </div>
          </div>
        </div>

        <BlockLite/>

        <div class="am-stats am-section" :class="{'am-lite-container-disabled': $root.isLite}">
          <div class="am-big-stats">
            <el-row :gutter="0">
              <el-col :sm="24" :md="12" :lg="8">
                <div class="am-grid-content">
                  <div class="am-title">
                    <h3>{{$root.labels.approved_appointments}}
                      <el-tooltip placement="top">
                        <div slot="content" v-html="$root.labels.approved_appointments_tooltip"></div>
                        <i class="el-icon-question am-tooltip-icon"></i>
                      </el-tooltip>
                      <span class="am-change" :class="countGrowthClass">{{ selectedPeriodStats.count - previousPeriodStats.count }}</span>
                    </h3>
                  </div>
                  <div class="am-big-num">
                    <span>{{calculateChartTotal('count')}}</span>
                  </div>

                  <!-- Small Chart -->
                  <bar-chart
                      ref="appointmentsCountChart"
                      :data="smallBarChartAppointmentsData"
                      :options="smallBarChartAppointmentsOptions"
                      :width=40
                      :height=15
                  >
                  </bar-chart>

                  <div>
                    <a class="am-goto" @click="navigateTo('appointments')">{{ $root.labels.view }} {{$root.labels.appointments}}</a>
                  </div>
                </div>
              </el-col>

              <el-col :sm="24" :md="12" :lg="8">
                <div class="am-grid-content">
                  <div class="am-title">
                    <h3>{{$root.labels.percentage_of_load}}
                      <el-tooltip placement="top">
                        <div slot="content" v-html="$root.labels.percentage_of_load_tooltip"></div>
                        <i class="el-icon-question am-tooltip-icon"></i>
                      </el-tooltip>
                      <span class="am-change" :class="loadGrowthClass">{{ loadGrowthPercentage }}{{ loadGrowthPercentageCharacter }}</span>
                    </h3>
                  </div>
                  <div class="am-big-num">
                    <span>{{calculateChartTotal('load')}}</span>
                  </div>

                  <line-chart
                      ref="appointmentsLoadChart"
                      :data="smallLineChartLoadData"
                      :options="smallLineChartLoadOptions"
                      :width=40
                      :height=15
                  >
                  </line-chart>

                  <div>
                    <a class="am-goto" @click="navigateTo('employees')">{{ $root.labels.view }} {{$root.labels.employees}}</a>
                  </div>
                </div>
              </el-col>

              <el-col :sm="24" :md="12" :lg="8">
                <div class="am-grid-content">
                  <div class="am-title">
                    <h3>{{$root.labels.revenue}}
                      <el-tooltip placement="top">
                        <div slot="content" v-html="$root.labels.revenue_tooltip"></div>
                        <i class="el-icon-question am-tooltip-icon"></i>
                      </el-tooltip>
                      <span class="am-change" :class="revenueGrowthClass">{{ revenueGrowthPercentage }}{{ revenueGrowthPercentageCharacter }}</span>
                    </h3>
                  </div>
                  <div class="am-big-num">
                    <span>{{calculateChartTotal('revenue')}}</span>
                  </div>

                  <line-chart
                      ref="appointmentsRevenueChart"
                      :data="smallLineChartRevenueData"
                      :options="smallLineChartRevenueOptions"
                      :width=40
                      :height=15
                  >
                  </line-chart>

                  <div>
                    <a class="am-goto" @click="navigateTo('finance')">{{ $root.labels.view }} {{$root.labels.finance}}</a>
                  </div>
                </div>
              </el-col>
            </el-row>
          </div>
        </div>

        <!-- Employee Stats-->
        <div class="am-employee-table-stats am-section" :class="{'am-lite-container-disabled': $root.isLite}">
          <el-tabs v-model="tableStats">
            <el-tab-pane :label="$root.labels.employees" name="employeeTableStats">
              <el-table
                  :data="visibleEmployeeTableData"
                  :default-sort = "{prop: 'employeeName', order: 'ascending'}"
                  style="width: 100%;"
                  :empty-text="$root.labels.no_employees_yet"
                  @sort-change="employeeTableSortChange">
                <el-table-column
                    fixed
                    prop="employeeName"
                    :label="$root.labels.employee"
                    sortable
                    min-width="180"
                >
                  <template slot-scope="scope">
                    <img :src="scope.row.imgSrc"/>
                    {{ scope.row.employeeName }}
                  </template>
                </el-table-column>
                <el-table-column
                    prop="numAppointments"
                    :label="$root.labels.appointments_count"
                    sortable
                    min-width="220"
                >
                </el-table-column>
                <el-table-column
                    prop="sumPayments"
                    :label="$root.labels.appointments_revenue"
                    :formatter="revenueFormatter"
                    sortable
                    min-width="220"
                >
                </el-table-column>

                <el-table-column
                    prop="hoursAppointment"
                    :label="$root.labels.appointments_hours"
                    :formatter="hoursFormatter"
                    sortable
                    min-width="220"
                >
                </el-table-column>
                <el-table-column
                    prop="load"
                    :label="$root.labels.appointments_load"
                    sortable
                    min-width="220"
                >
                  <template slot-scope="scope">
                    <div style="width: 100%;">
                      <div style="width: 50px; display: inline-block;">{{ scope.row.load }}%</div>
                      <el-progress
                          :width="120"
                          :show-text=false
                          :percentage=scope.row.load
                          :color=getPercentageBarColor(scope.row.load)>

                      </el-progress>
                    </div>

                  </template>
                </el-table-column>
              </el-table>

              <!-- Pagination -->
              <pagination-block
                  :params="employeeTableParams"
                  :show="serviceTableParams.show"
                  :count="employeeTableParams.total"
                  :label="$root.labels.employees.toLowerCase()"
                  :visible="employeeTableParams.show < employeeTableParams.total"
                  @change="changeVisibleEmployeeTableData"
              >
              </pagination-block>

            </el-tab-pane>

            <el-tab-pane :label="$root.labels.service" name="serviceTableStats">
              <el-table
                  :data="visibleServiceTableData"
                  :default-sort = "{prop: 'serviceName', order: 'ascending'}"
                  style="width: 100%;"
                  :empty-text="$root.labels.no_services_yet"
                  @sort-change="serviceTableSortChange">
                <el-table-column
                    fixed
                    prop="serviceName"
                    :label="$root.labels.service"
                    sortable
                    min-width="180"
                >
                  <template slot-scope="scope">
                    <img :src="scope.row.imgSrc"/>
                    {{ scope.row.serviceName }}
                  </template>
                </el-table-column>
                <el-table-column
                    prop="numAppointments"
                    :label="$root.labels.appointments_count"
                    sortable
                    min-width="220"
                >
                </el-table-column>
                <el-table-column
                    prop="sumPayments"
                    :label="$root.labels.appointments_revenue"
                    :formatter="revenueFormatter"
                    sortable
                    min-width="220"
                >
                </el-table-column>

                <el-table-column
                    prop="hoursAppointment"
                    :label="$root.labels.appointments_hours"
                    :formatter="hoursFormatter"
                    sortable
                    min-width="220"
                >
                </el-table-column>
                <el-table-column
                    prop="load"
                    :label="$root.labels.appointments_load"
                    sortable
                    min-width="220"
                >
                  <template slot-scope="scope">
                    <div style="width: 100%">
                      <span>{{ scope.row.load }}%</span>
                      <el-progress
                          :width="120"
                          :show-text=false
                          :percentage=scope.row.load
                          :color=getPercentageBarColor(scope.row.load)>
                      </el-progress>
                    </div>

                  </template>
                </el-table-column>
              </el-table>

              <!-- Pagination -->
              <pagination-block
                  :params="serviceTableParams"
                  :show="serviceTableParams.show"
                  :count="serviceTableParams.total"
                  :label="$root.labels.services.toLowerCase()"
                  :visible="serviceTableParams.show < serviceTableParams.total"
                  @change="changeVisibleServiceTableData"
              >
              </pagination-block>
            </el-tab-pane>

          </el-tabs>

        </div>

        <!-- Upcoming Appointments -->
        <div id="am-appointments" class="am-upcoming-appointments am-section">

          <!-- Header -->
          <el-form :model="params" class="demo-form-inline" :action="exportAction" method="POST">
            <el-row>

              <!-- Header Title -->
              <el-col :span="20">
                <h2 class="am-section-title">{{ $root.labels.upcoming_appointments }}</h2>
              </el-col>

              <!-- Export Button -->
              <el-col :span="4">
                <div class="align-right">
                  <el-tooltip placement="top">
                    <div slot="content" v-html="$root.labels.export_tooltip_appointments"></div>
                    <el-button
                        class="button-export am-button-icon"
                        :disabled="appointments.length === 0"
                        @click="dialogExport = true"
                    >
                      <img class="svg" :alt="$root.labels.export" :src="$root.getUrl+'public/img/export.svg'"/>
                    </el-button>
                  </el-tooltip>
                </div>
              </el-col>
            </el-row>


            <!-- Dialog Export -->
            <transition name="slide">
              <el-dialog
                  class="am-side-dialog am-dialog-export"
                  :visible.sync="dialogExport"
                  :show-close="false"
                  v-if="dialogExport"
              >
                <dialog-export
                    :data="getExportParams()"
                    :action="$root.getAjaxUrl + '/report/appointments'"
                    @updateAction="(action) => {this.exportAction = action}"
                    @closeDialogExport="dialogExport = false"
                >
                </dialog-export>
              </el-dialog>
            </transition>
          </el-form>

          <!-- Appointments List Head -->
          <div class="am-appointments-list-head" v-if="appointments.length > 0">
            <el-row>

              <el-col :lg="15">
                <el-row :gutter="10" class="am-appointments-flex-row-middle-align">
                  <el-col :lg="5" :md="5">
                    <p>{{ $root.labels.date }} / {{ $root.labels.time }}:</p>
                  </el-col>
                  <el-col :lg="5" :md="5">
                    <p>{{ $root.labels.customer }}:</p>
                  </el-col>
                  <el-col :lg="5" :md="5">
                    <p>{{ $root.labels.assigned_to }}:</p>
                  </el-col>
                  <el-col :lg="9" :md="9">
                    <p>{{ $root.labels.service }}:</p>
                  </el-col>
                </el-row>
              </el-col>

              <el-col :lg="9">
                <el-row :gutter="10" class="am-appointments-flex-row-middle-align">
                  <el-col :lg="0" :md="3"></el-col>
                  <el-col :lg="5" :md="6">
                    <p>{{ $root.labels.duration }}:</p>
                  </el-col>
                  <el-col :lg="6" :md="6">
                    <p>{{ $root.labels.payment }}:</p>
                  </el-col>
                  <el-col :lg="13" :md="6">
                    <p>{{ $root.labels.status }}:</p>
                  </el-col>
                </el-row>
              </el-col>

            </el-row>
          </div>

          <!-- Appointments List -->
          <div class="am-appointments" v-if="appointments.length > 0">
            <div class="am-appointments-list">
              <el-collapse>
                <el-collapse-item
                    v-for="app in appointments"
                    v-if="app.providerId && (appointmentProvider = getProviderById(app.providerId)) && appointmentProvider !== null"
                    :key="app.id"
                    :name="app.id"
                    class="am-appointment">

                  <template slot="title">
                    <div class="am-appointment-data">
                      <el-row>
                        <el-col :lg="15">
                          <el-row :gutter="10" class="am-appointments-flex-row-middle-align">

                            <!-- Appointment Time -->
                            <el-col :lg="5" :sm="5">
                              <span class="am-appointment-time" :class="app.status">{{ getFrontedFormattedDateTime(app.bookingStart) }}</span>
                            </el-col>

                            <!-- Appointment Customer(s) -->
                            <el-col :lg="5" :sm="6">
                              <p class="am-col-title">{{ $root.labels.customer }}:</p>
                              <template>
                                <el-tooltip
                                    class="item"
                                    effect="dark"
                                    placement="top"
                                    :disabled="app.bookings.length === 1"
                                    popper-class="am-align-left"
                                >
                                  <div v-if="app.bookings.length > 1"
                                       slot="content"
                                       v-html="getCustomersFromGroup(app)"></div>
                                  <h3 :class="{ grouped: app.bookings.length > 1 }">
                                    <img
                                        v-show="app.bookings.length > 1"
                                        width="16px"
                                        :src="$root.getUrl+'public/img/group.svg'"
                                        class="svg"
                                    />
                                    <span v-for="(booking, index) in app.bookings">
                                      {{ ((user = getCustomerById(booking.customerId)) !== null ? user.firstName + ' ' + user.lastName : '') }}<span
                                        v-if="app.bookings.length > 1 && index + 1  !== app.bookings.length">,</span>
                                    </span>
                                  </h3>
                                </el-tooltip>
                                <span v-if="app.bookings.length === 1" v-for="booking in app.bookings">{{ ((user = getCustomerById(booking.customerId)) !== null ? user.email : '') }}</span>
                                <span v-if="app.bookings.length > 1">{{$root.labels.multiple_emails}}</span>
                              </template>
                            </el-col>

                            <!-- Appointment Provider -->
                            <el-col :lg="5" :sm="6">
                              <p class="am-col-title">{{ $root.labels.assigned }}:</p>
                              <div class="am-assigned">
                                <img :src="pictureLoad(getProviderById(app.providerId), true)"
                                     @error="imageLoadError(getProviderById(app.providerId), true)"
                                     v-if="options.fetched"/>
                                <h4>
                                  {{ ((user = getProviderById(app.providerId)) !== null ? user.firstName + ' ' +
                                  user.lastName : '') }}
                                </h4>
                              </div>
                            </el-col>

                            <!-- Appointment Service -->
                            <el-col :lg="9" :sm="7">
                              <p class="am-col-title">{{ $root.labels.service }}:</p>
                              <h4>
                                {{ ((service = getServiceById(app.serviceId)) !== null ? service.name : '') }}
                              </h4>
                            </el-col>

                          </el-row>
                        </el-col>

                        <el-col :lg="9">
                          <el-row :gutter="10" class="am-appointments-flex-row-middle-align">

                            <!-- Appointment Duration -->
                            <el-col :lg="5" :sm="5" :xs="12">
                              <p class="am-col-title">{{ $root.labels.duration }}:</p>
                              <h4>{{
                                momentDurationToNiceDurationWithUnit(convertDateTimeRangeDifferenceToMomentDuration(app.bookingStart,
                                  app.bookingEnd)) }}</h4>
                            </el-col>

                            <!-- Appointment Payment -->
                            <el-col class="am-appointment-payment" :lg="6" :sm="6" :xs="12">
                              <p class="am-col-title">{{ $root.labels.payment }}:</p>
                              <div class="am-appointment-payment-wrap">
                                <img
                                    v-for="method in getAppointmentPaymentMethods(app.bookings)"
                                    :src="$root.getUrl + 'public/img/payments/' + method + '.svg'"
                                >
                                <h4>{{ getAppointmentPrice(app.serviceId, app.serviceId, app.bookings) }}</h4>
                              </div>
                            </el-col>

                            <!-- Appointment Status -->
                            <el-col :lg="8" :sm="8" :xs="17">
                              <div class="am-appointment-status" @click.stop>
                                <span class="am-appointment-status-symbol" :class="app.status"></span>
                                <el-select
                                    v-model="app.status"
                                    :placeholder="$root.labels.status"
                                    @change="updateAppointmentStatus(app, app.status, false)"
                                >
                                  <el-option
                                      v-for="opt in statuses"
                                      :key="opt.value"
                                      :label="opt.label"
                                      :value="opt.value"
                                      class="am-appointment-status-option"

                                  >
                                    <span class="am-appointment-status-symbol" :class="opt.value">{{ opt.label }}</span>
                                  </el-option>
                                </el-select>
                              </div>
                            </el-col>

                            <!-- Appointment Edit -->
                            <el-col :lg="5" :sm="5" :xs="7">
                              <div class="am-edit-btn" @click.stop>
                                <el-button @click="showDialogEditAppointment(app.id)">
                                  {{ $root.labels.edit }}
                                </el-button>
                              </div>
                            </el-col>

                          </el-row>
                        </el-col>
                      </el-row>
                    </div>
                  </template>

                  <appointment-list-collapsed
                      :app="app"
                      :options="options"
                  >
                  </appointment-list-collapsed>

                </el-collapse-item>
              </el-collapse>
            </div>
          </div>

          <!-- No Results -->
          <div class="am-empty-state am-section" v-if="appointments.length === 0">
            <img :src="$root.getUrl + 'public/img/emptystate.svg'">
            <p>{{ $root.labels.no_upcoming_appointments }}</p>
          </div>

        </div>

        <BlockLite/>

        <!-- Charts -->
        <div class="am-charts am-section" :class="{'am-lite-container-disabled': $root.isLite}">
          <el-row :gutter="32">

            <!-- Conversions Charts -->
            <el-col :md="16" class="am-border-right">
              <div class="am-chart bar-chart">
                <h2 class="am-section-title">
                  {{ $root.labels.conversions }}
                  <el-tooltip placement="top">
                    <div slot="content" v-html="$root.labels.conversions_tooltip"></div>
                    <i class="el-icon-question am-tooltip-icon"></i>
                  </el-tooltip>
                </h2>
                <el-tabs v-model="chartTabs">

                  <!-- Employees Conversions Chart Tab -->
                  <el-tab-pane :label="$root.labels.employees" name="employee">

                    <!-- Employees Conversions Chart Filter -->
                    <div class="am-chart-filter">
                      <el-row :gutter="10">
                        <el-col :sm="12">
                          <el-select
                              v-model="employees"
                              @change="filterEmployeesChart"
                              filterable
                              clearable
                              :placeholder="$root.labels.select_employee"
                              multiple
                              collapse-tags
                          >
                            <el-option
                                v-for="item in options.entities.employees"
                                :key="item.id"
                                :label="item.firstName + ' ' + item.lastName"
                                :value="item.id"
                            >
                            </el-option>
                          </el-select>
                        </el-col>
                      </el-row>
                    </div>

                    <!-- Employees Conversions Chart -->
                    <bar-chart
                        v-if="chartTabs === 'employee'"
                        ref="employeesChart"
                        :data="employeesChartData"
                        :options="defaultBarChartOptions"
                    >
                    </bar-chart>

                  </el-tab-pane>

                  <!-- Services Conversions Chart Tab -->
                  <el-tab-pane :label="$root.labels.services" name="service">

                    <!-- Services Conversions Chart Filter -->
                    <div class="am-chart-filter">
                      <el-row :gutter="10">
                        <el-col :sm="12">
                          <el-select
                              v-model="services"
                              @change="filterServicesChart"
                              filterable
                              clearable
                              :placeholder="$root.labels.select_service"
                              multiple
                              collapse-tags
                          >
                            <el-option
                                v-for="item in options.entities.services"
                                :key="item.id"
                                :label="item.name"
                                :value="item.id"
                            >
                            </el-option>
                          </el-select>
                        </el-col>
                      </el-row>
                    </div>

                    <!-- Services Conversions Chart -->
                    <bar-chart
                        v-if="chartTabs === 'service'"
                        ref="servicesChart"
                        :data="servicesChartData"
                        :options="defaultBarChartOptions"
                    >
                    </bar-chart>

                  </el-tab-pane>

                  <!-- Locations Conversions Chart Tab -->
                  <el-tab-pane :label="$root.labels.locations" name="location" v-if="options.entities.locations.length">

                    <!-- Locations Conversions Chart Filter -->
                    <div class="am-chart-filter">
                      <el-row :gutter="10">
                        <el-col :sm="12">
                          <el-select
                              v-model="locations"
                              @change="filterLocationsChart"
                              filterable
                              clearable
                              :placeholder="$root.labels.select_location"
                              multiple
                              collapse-tags
                          >
                            <el-option
                                v-for="item in options.entities.locations"
                                :key="item.id"
                                :label="item.name"
                                :value="item.id"
                            >
                            </el-option>
                          </el-select>
                        </el-col>
                      </el-row>
                    </div>

                    <!-- Locations Conversions Chart -->
                    <bar-chart
                        v-if="chartTabs === 'location'"
                        ref="locationsChart"
                        :data="locationsChartData"
                        :options="defaultBarChartOptions"
                    >
                    </bar-chart>

                  </el-tab-pane>

                </el-tabs>
              </div>
            </el-col>

            <!-- Customers Chart -->
            <el-col :md="8">
              <div class="am-chart doughnut-chart">

                <!-- Customers Label and Growth Stats -->
                <el-row>
                  <el-col :span="12">
                    <h2 class="am-section-title">
                      {{ $root.labels.customers }}
                      <el-tooltip placement="top">
                        <div slot="content" v-html="$root.labels.customers_tooltip"></div>
                        <i class="el-icon-question am-tooltip-icon"></i>
                      </el-tooltip>
                    </h2>
                  </el-col>
                  <el-col :span="12">
                    <h2 class="align-right" v-if="fetched">{{ totalCustomers }}
                      <span :class="customerGrowthClass">
                      {{ customersGrowthPercentage }}{{ customerGrowthPercentageCharacter }}
                    </span>
                    </h2>
                  </el-col>
                </el-row>

                <!-- Customers Chart -->
                <div class="" style="padding: 0 40px;">
                  <doughnut-chart
                      ref="customersChart"
                      :data="customersChartData"
                  >
                  </doughnut-chart>
                </div>

                <!-- Customers Progress Charts -->
                <el-row>
                  <el-col :span="12">
                    <p class="am-big-num" v-if="fetched">
                      {{ newCustomers }}
                    </p>
                    <p>{{ $root.labels.new }}</p>
                    <el-progress
                        v-if="fetched"
                        :percentage="newCustomersPercentage"
                        color="#1A84EE"
                    >
                    </el-progress>
                  </el-col>
                  <el-col :span="12">
                    <p class="am-big-num" v-if="fetched">
                      {{ returningCustomers }}
                    </p>
                    <p>{{ $root.labels.returning }}</p>
                    <el-progress
                        v-if="fetched"
                        :percentage="returnedCustomersPercentage"
                        color="#FFD400"
                    >
                    </el-progress>
                  </el-col>
                </el-row>

              </div>
            </el-col>

          </el-row>
        </div>

        <!-- Button New -->
        <div v-if="$root.settings.capabilities.canWrite === true" id="am-button-new" class="am-button-new">
          <el-popover
              ref="popover"
              placement="top"
              width="160"
              v-model="popover"
              visible-arrow="false"
              popper-class="am-button-popover">
            <div class="am-overlay" @click="popover = false; buttonNewItems = !buttonNewItems">
              <div class="am-button-new-items">
                <transition name="el-zoom-in-bottom">
                  <div v-show="buttonNewItems">
                    <el-button @click="showDialogNewAppointment">{{ $root.labels.new_appointment }}</el-button>
                    <el-button @click="showDialogNewCustomer">{{ $root.labels.create_customer }}</el-button>
                  </div>
                </transition>
              </div>
            </div>
          </el-popover>
          <el-button
              id="am-plus-symbol"
              v-popover:popover
              type="primary"
              icon="el-icon-plus"
              @click="buttonNewItems = !buttonNewItems"
              ref="rotating"
          >
          </el-button>
        </div>

        <!-- Dialog New Appointment -->
        <transition name="slide">
          <el-dialog
              class="am-side-dialog"
              :visible.sync="dialogAppointment"
              :show-close="false"
              v-if="dialogAppointment"
          >
            <dialog-appointment
                :appointment="appointment"
                :savedAppointment="savedAppointment"
                :bookings="bookings"
                :options="options"
                @sortBookings="sortBookings"
                @saveCallback="getDashboardOptions"
                @duplicateCallback="duplicateAppointmentCallback"
                @closeDialog="closeDialogAppointment"
                @showDialogNewCustomer="showDialogNewCustomer"
                @editPayment="editPayment"
            >
            </dialog-appointment>
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
                @saveCallback="saveCustomerCallback"
                @closeDialog="dialogCustomer = false"
            >
            </dialog-customer>
          </el-dialog>
        </transition>

        <!-- Dialog Payment -->
        <transition name="slide">
          <el-dialog
              class="am-side-dialog am-dialog-coupon"
              :visible.sync="dialogPayment"
              :show-close="false"
              v-if="dialogPayment"
          >
            <dialog-payment
                :modalData="selectedPaymentModalData"
                :appointmentFetched=true
                :bookingFetched=true
                @closeDialogPayment="dialogPayment = false"
                @updatePaymentCallback="updatePaymentCallback"
            >
            </dialog-payment>
          </el-dialog>
        </transition>
      </div>

      <DialogLite/>

      <!-- Help Button -->
      <el-col :md="6" class="">
        <a class="am-help-button" href="https://wpamelia.com/admin-dashboard/" target="_blank">
          <i class="el-icon-question"></i> {{ $root.labels.need_help }}?
        </a>
      </el-col>

    </div>
  </div>
</template>

<script>
  import AppointmentListCollapsed from '../appointments/AppointmentListCollapsed.vue'
  import liteMixin from '../../../js/common/mixins/liteMixin'
  import appointmentMixin from '../../../js/backend/mixins/appointmentMixin'
  import appointmentPriceMixin from '../../../js/backend/mixins/appointmentPriceMixin'
  import BarChart from '../../../js/backend/components/barchart'
  import DoughnutChart from '../../../js/backend/components/doughnutchart'
  import LineChart from '../../../js/backend/components/linechart'
  import customerMixin from '../../../js/backend/mixins/customerMixin'
  import dateMixin from '../../../js/common/mixins/dateMixin'
  import DialogAppointment from '../appointments/DialogAppointment.vue'
  import DialogCustomer from '../customers/DialogCustomer.vue'
  import DialogExport from '../parts/DialogExport.vue'
  import DialogPayment from '../finance/DialogFinancePayment.vue'
  import durationMixin from '../../../js/common/mixins/durationMixin'
  import entitiesMixin from '../../../js/common/mixins/entitiesMixin'
  import Form from 'form-object'
  import imageMixin from '../../../js/common/mixins/imageMixin'
  import moment from 'moment'
  import notifyMixin from '../../../js/backend/mixins/notifyMixin'
  import PageHeader from '../parts/PageHeader.vue'
  import paymentMixin from '../../../js/backend/mixins/paymentMixin'
  import priceMixin from '../../../js/common/mixins/priceMixin'
  import PaginationBlock from '../parts/PaginationBlock.vue'

  export default {

    mixins: [liteMixin, paymentMixin, entitiesMixin, appointmentMixin, imageMixin, dateMixin, durationMixin, priceMixin, customerMixin, notifyMixin, appointmentPriceMixin],

    data () {
      return {
        currentUser: null,
        todayAppointmentsCount: {
          approved: null,
          pending: null
        },
        periodChange: {
          count: 0,
          available: 0,
          occupied: 0,
          revenue: 0
        },
        previousPeriodStats: {
          count: 0,
          available: 0,
          occupied: 0,
          revenue: 0
        },
        selectedPeriodStats: {
          count: 0,
          available: 0,
          occupied: 0,
          revenue: 0
        },
        statsLabels: [],
        customer: null,
        appointments: [],
        appointmentsCount: [],
        buttonNewItems: false,
        chartTabs: 'employee',
        customersChartData: {
          labels: [this.$root.labels.new, this.$root.labels.returning, ''],
          datasets: [
            {
              backgroundColor: ['#1a84ee', '#ffd400', '#ebeef5'],
              borderColor: '#E2E6EC',
              data: [0, 0, 1],
              hoverBackgroundColor: ['#117ce6', '#eec600', '#ebeef5'],
              hoverBorderColor: '#D3DDEA'
            }
          ]
        },
        dialogAppointment: false,
        dialogPayment: false,
        dialogExport: false,
        employees: [],

        tableStats: 'employeeTableStats',

        smallBarChartAppointmentsData: {
          labels: [],
          datasets: [
            {
              backgroundColor: '#5FCE19',
              data: [],
              hoverBackgroundColor: '#5FCE19',
              label: '',
              borderWidth: 0
            }
          ]
        },

        smallBarChartAppointmentsOptions: {
          legend: {
            display: false
          },
          scales: {
            xAxes: [{
              barThickness: 6,
              beginAtZero: true,
              gridLines: {
                display: false
              },
              ticks: {
                stepSize: 1,
                min: 1,
                autoSkip: true

              }
            }],
            yAxes: [{
              display: false,
              beginAtZero: true,
              gridLines: {
                display: false
              },
              ticks: {
                stepSize: 10,
                min: 0
              }
            }]
          },
          tooltips: {
            custom: function (tooltip) {
              if (!tooltip) {
                return
              }

              tooltip.displayColors = false
            },
            callbacks: {
              label: (tooltipItems, data) => {
                return this.statsLabels[tooltipItems.xLabel] + ': ' + tooltipItems.yLabel
              },
              title: (tooltipItems, data) => {
              }
            }
          }
        },

        smallLineChartLoadData: {
          labels: [],
          datasets: [
            {
              backgroundColor: 'transparent',
              borderColor: '#9A47FF',
              data: [],
              label: '',
              borderWidth: 2,
              lineTension: 0,
              pointRadius: 3,
              pointBorderColor: '#fff'
            }
          ]
        },

        smallLineChartLoadOptions: {
          legend: {
            display: false
          },
          scales: {
            xAxes: [{
              gridLines: {
                display: false
              },
              ticks: {
                stepSize: 10,
                min: 0,
                autoSkip: true
              }
            }],
            yAxes: [{
              display: false,
              beginAtZero: true,
              gridLines: {
                display: false
              },
              ticks: {
                stepSize: 1,
                min: 0
              }
            }]
          },
          tooltips: {
            custom: function (tooltip) {
              if (!tooltip) {
                return
              }

              tooltip.displayColors = false
            },
            callbacks: {
              label: (tooltipItems, data) => {
                return this.statsLabels[tooltipItems.xLabel] + ': ' + tooltipItems.yLabel + '%'
              },
              title: (tooltipItems, data) => {
              }
            }
          }
        },

        smallLineChartRevenueData: {
          labels: [],
          datasets: [
            {
              backgroundColor: 'transparent',
              borderColor: '#FD8863',
              data: [],
              label: '',
              borderWidth: 2,
              lineTension: 0,
              pointBackgroundColor: '#FD8863',
              pointRadius: 3,
              pointBorderColor: '#fff'
            }
          ]
        },

        smallLineChartRevenueOptions: {
          legend: {
            display: false
          },
          scales: {
            xAxes: [{
              barPercentage: 0.2,
              categoryPercentage: 0.8,
              gridLines: {
                display: false
              },
              ticks: {
                stepSize: 1,
                min: 0,
                autoSkip: true
              }
            }],
            yAxes: [{
              display: false,
              beginAtZero: true,
              gridLines: {
                display: false
              },
              ticks: {
                stepSize: 1,
                min: 0
              }
            }]
          },
          tooltips: {
            custom: function (tooltip) {
              if (!tooltip) {
                return
              }

              tooltip.displayColors = false
            },
            callbacks: {
              label: (tooltipItems, data) => {
                return this.statsLabels[tooltipItems.xLabel] + ': ' + this.getFormattedPrice(tooltipItems.yLabel)
              },
              title: (tooltipItems, data) => {
              }
            }
          }
        },

        employeePeriodStats: [],
        employeeTableData: [],
        visibleEmployeeTableData: [],
        serviceTableData: [],
        visibleServiceTableData: [],
        visibleTableDataCount: [],
        employeeTableParams: {
          show: 5,
          total: 0,
          page: 1
        },
        serviceTableParams: {
          show: 5,
          total: 0,
          page: 1
        },

        employeesChartData: {
          labels: [],
          datasets: [
            {
              backgroundColor: '#D3DDEA',
              data: [],
              hoverBackgroundColor: '#c8d4e5',
              label: this.$root.labels.views,
              borderWidth: 0
            },
            {
              backgroundColor: '#5FCE19',
              data: [],
              hoverBackgroundColor: '#58BF17',
              label: this.$root.labels.scheduled_appointments,
              borderWidth: 0
            }
          ]
        },
        defaultBarChartOptions: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            xAxes: [{
              barPercentage: 0.5,
              categoryPercentage: 0.8,
              ticks: {
                stepSize: 1,
                min: 0,
                autoSkip: false
              }
            }],
            yAxes: [{
              gridLines: {
                display: true
              },
              ticks: {
                beginAtZero: true,
                userCallback: function (label) {
                  if (Math.floor(label) === label) {
                    return label
                  }
                }
              }
            }]
          }
        },

        employeesStats: [],
        fetched: false,
        form: new Form(),
        locations: [],
        locationsChartData: {
          labels: [],
          datasets: [
            {
              backgroundColor: '#D3DDEA',
              data: [],
              hoverBackgroundColor: '#c8d4e5',
              label: this.$root.labels.views,
              borderWidth: 0
            },
            {
              backgroundColor: '#5FCE19',
              data: [],
              hoverBackgroundColor: '#58BF17',
              label: this.$root.labels.scheduled_appointments,
              borderWidth: 0
            }
          ]
        },
        locationsStats: [],
        params: {
          dates: this.getDatePickerInitRange()
        },
        popover: false,
        selectedPaymentModalData: null,
        services: [],
        servicesChartData: {
          labels: [],
          datasets: [
            {
              backgroundColor: '#D3DDEA',
              data: [],
              hoverBackgroundColor: '#c8d4e5',
              label: this.$root.labels.views,
              borderWidth: 0
            },
            {
              backgroundColor: '#5FCE19',
              data: [],
              hoverBackgroundColor: '#58BF17',
              label: this.$root.labels.scheduled_appointments,
              borderWidth: 0
            }
          ]
        },
        totalPastPeriodCustomers: 0
      }
    },

    created () {
      this.getDashboardOptions()
      this.getCurrentUser()
    },

    methods: {
      revenueFormatter (row, column) {
        return this.getFormattedPrice(row.sumPayments)
      },

      hoursFormatter (row, column) {
        let hours = this.getMinutesToDays(row.hoursAppointment)

        return hours === '' ? 0 : hours
      },

      employeeTableSortChange (sortProps) {
        switch (sortProps.order) {
          case (null):
            this.employeeTableData = this.employeeTableData.sort((a, b) => (a.employeeName > b.employeeName) ? 1 : -1)

            break
          case ('ascending'):
            this.employeeTableData = this.employeeTableData.sort((a, b) => (a[sortProps.prop] > b[sortProps.prop]) ? 1 : -1)

            break
          case ('descending'):
            this.employeeTableData = this.employeeTableData.sort((a, b) => (a[sortProps.prop] < b[sortProps.prop]) ? 1 : -1)

            break
        }

        this.showVisibleEmployeeTableData()
      },

      serviceTableSortChange (sortProps) {
        switch (sortProps.order) {
          case (null):
            this.serviceTableData = this.serviceTableData.sort((a, b) => (a.serviceName > b.serviceName) ? 1 : -1)

            break
          case ('ascending'):
            this.serviceTableData = this.serviceTableData.sort((a, b) => (a[sortProps.prop] > b[sortProps.prop]) ? 1 : -1)

            break
          case ('descending'):
            this.serviceTableData = this.serviceTableData.sort((a, b) => (a[sortProps.prop] < b[sortProps.prop]) ? 1 : -1)

            break
        }

        this.showVisibleServiceTableData()
      },

      changeVisibleEmployeeTableData () {
        this.showVisibleEmployeeTableData()
      },

      changeVisibleServiceTableData () {
        this.showVisibleServiceTableData()
      },

      showVisibleEmployeeTableData () {
        this.visibleEmployeeTableData = this.employeeTableData.slice(
          (this.employeeTableParams.page - 1) * this.employeeTableParams.show,
          (this.employeeTableParams.page - 1) * this.employeeTableParams.show + this.employeeTableParams.show
        )
      },

      showVisibleServiceTableData () {
        this.visibleServiceTableData = this.serviceTableData.slice(
          (this.serviceTableParams.page - 1) * this.serviceTableParams.show,
          (this.serviceTableParams.page - 1) * this.serviceTableParams.show + this.serviceTableParams.show
        )
      },

      getExportParams () {
        return Object.assign({count: this.appointmentsCount, dates: {start: moment().format('YYYY-MM-DD'), end: ''}}, this.exportParams)
      },

      showDialogNewCustomer () {
        this.customer = this.getInitCustomerObject()
        this.dialogCustomer = true
      },

      editPayment (obj) {
        this.selectedPaymentModalData = this.getPaymentData(obj.paymentId, this.savedAppointment)
        this.dialogPayment = true
      },

      getDashboardOptions () {
        let $this = this

        this.fetchEntities(function (success) {
          if (success) {
            $this.filterResponseData()

            $this.setBookings(0)
            $this.getDashboard()
          }

          $this.fetched = true
          $this.options.fetched = true
        }, {
          types: ['categories', 'employees', 'customers'],
          isFrontEnd: false
        })
      },

      changeFilter () {
        this.setDatePickerSelectedDaysCount(this.params.dates.start, this.params.dates.end)
        this.getDashboard()
      },

      getDashboard () {
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

        this.$http.get(`${this.$root.getAjaxUrl}/stats`, {
          params: params
        })
          .then(response => {
            this.employeePeriodStats = response.data.data.selectedPeriodStats

            // this.previousPeriodStats = this.getPeriodStats(response.data.data.previousPeriodStats, 'providers')
            // this.selectedPeriodStats = this.getPeriodStats(this.employeePeriodStats, 'providers')

            this.fillAppointmentsChartStats(response.data.data.selectedPeriodStats, 'providers')

            this.fillAppointmentsTablesStats(this.employeePeriodStats)

            this.todayAppointmentsCount.approved = response.data.data.count.approved
            this.todayAppointmentsCount.pending = response.data.data.count.pending

            this.appointments = response.data.data.appointments
            this.appointmentsCount = response.data.data.appointmentsCount

            this.fillCustomersChart(response.data.data.customersStats)
            this.employeesStats = response.data.data.employeesStats
            this.fillEmployeesChart(response.data.data.employeesStats)
            this.servicesStats = response.data.data.servicesStats
            this.fillServicesChart(response.data.data.servicesStats)
            this.locationsStats = response.data.data.locationsStats
            this.fillLocationsChart(response.data.data.locationsStats)

            this.updateCharts()
            this.filterEmployeesChart()
            this.filterServicesChart()
            this.filterLocationsChart()
            this.fetched = true
          })
      },

      navigateTo (pageName) {
        let startDate = moment(this.params.dates.start).format('YYYY-MM-DD')
        let endDate = moment(this.params.dates.end).format('YYYY-MM-DD')

        let url = 'admin.php?page=wpamelia-' + pageName

        switch (pageName) {
          case ('appointments'):
            url += '&dateFrom=' + startDate + '&dateTo=' + endDate + '&status=approved'
            break

          case ('finance'):
            url += '&dateFrom=' + startDate + '&dateTo=' + endDate + '&status=paid'
            break

          case ('employees'):
            break
        }

        window.location = url
      },

      updateCharts () {
        if (typeof this.$refs.customersChart !== 'undefined') { this.$refs.customersChart.update(false) }
        if (typeof this.$refs.employeesChart !== 'undefined') { this.$refs.employeesChart.update(false) }
        if (typeof this.$refs.servicesChart !== 'undefined') { this.$refs.servicesChart.update(false) }
        if (typeof this.$refs.locationsChart !== 'undefined') { this.$refs.locationsChart.update(false) }
        if (typeof this.$refs.appointmentsCountChart !== 'undefined') { this.$refs.appointmentsCountChart.update(true) }
        if (typeof this.$refs.appointmentsLoadChart !== 'undefined') { this.$refs.appointmentsLoadChart.update(true) }
        if (typeof this.$refs.appointmentsRevenueChart !== 'undefined') { this.$refs.appointmentsRevenueChart.update(true) }
      },

      getPeriodStats: function () {
        return {
          count: 0,
          occupied: 0,
          available: 0,
          revenue: 0
        }
      },

      fillAppointmentsChartStats: function () {},

      fillAppointmentsTablesStats: function () {},

      fillCustomersChart: function () {},

      fillEmployeesChart: function () {},

      filterEmployeesChart: function () {},

      fillServicesChart: function () {},

      filterServicesChart: function () {},

      fillLocationsChart: function () {},

      filterLocationsChart: function () {},

      calculateChartTotal (name) {
        switch (name) {
          case ('count'):
            let appointmentsCount = 0

            this.smallBarChartAppointmentsData.datasets[0].data.forEach(function (value) {
              appointmentsCount += (value !== null ? value : 0)
            })

            return appointmentsCount
          case ('load'):
            let availableSum = 0
            let occupiedSum = 0

            for (let key in this.employeeTableData) {
              availableSum += this.employeeTableData[key].available
              occupiedSum += this.employeeTableData[key].occupied
            }

            return (availableSum !== 0 ? parseFloat((occupiedSum / availableSum * 100).toFixed(1)) : 0) + '%'
          case ('revenue'):
            let appointmentsRevenue = 0

            this.smallLineChartRevenueData.datasets[0].data.forEach(function (value) {
              appointmentsRevenue += (value !== null ? value : 0)
            })

            return this.getFormattedPrice(appointmentsRevenue)
        }
      },

      getCurrentUser () {
        this.$http.get(`${this.$root.getAjaxUrl}/users/current`)
          .then(response => {
            this.currentUser = response.data.data.user
          })
          .catch(e => {
            console.log('getCurrentUser fail')
          })
      },

      getPercentageBarColor (percent) {
        switch (true) {
          case (percent < 25):
            return '#FF1563'
          case (percent > 25 && percent < 50):
            return '#FFA700'
          case (percent > 50 && percent < 75):
            return '#BDDE00'
          case (percent > 75):
            return '#5FCE19'
          default:
            return '#5FCE19'
        }
      },

      growthClass (value) {
        if (value > 0 || value === '+∞') {
          return 'am-growth-increase'
        }

        if (value < 0 || value === '-∞') {
          return 'am-growth-decrease'
        }

        return 'am-growth-equal'
      },

      growthPercentageCharacter (value) {
        if (value === '+∞' || value === '-∞') {
          return ''
        }

        return '%'
      },

      growthPercentage (totalValue, pastTotalValue) {
        if (totalValue === 0 && pastTotalValue === 0) {
          return 0
        }

        if (totalValue === 0 && pastTotalValue !== 0) {
          return '-∞'
        }

        if (totalValue !== 0 && pastTotalValue === 0) {
          return '+∞'
        }

        return totalValue - pastTotalValue === 0 ? 0 : ((totalValue - pastTotalValue) / pastTotalValue * 100).toFixed(1)
      }
    },

    computed: {
      newCustomers () {
        return this.customersChartData.datasets[0].data[0]
      },

      returningCustomers () {
        return this.customersChartData.datasets[0].data[1]
      },

      totalCustomers () {
        return this.newCustomers + this.returningCustomers
      },

      newCustomersPercentage () {
        return this.totalCustomers === 0 ? 0 : parseFloat((this.newCustomers / this.totalCustomers * 100).toFixed(1))
      },

      returnedCustomersPercentage () {
        return this.totalCustomers === 0 ? 0 : parseFloat((this.returningCustomers / this.totalCustomers * 100).toFixed(1))
      },

      countGrowthPercentage () {
        return this.growthPercentage(this.selectedPeriodStats.count, this.previousPeriodStats.count)
      },

      countGrowthClass () {
        return this.growthClass(this.countGrowthPercentage)
      },

      revenueGrowthPercentage () {
        return this.growthPercentage(this.selectedPeriodStats.revenue, this.previousPeriodStats.revenue)
      },

      revenueGrowthClass () {
        return this.growthClass(this.revenueGrowthPercentage)
      },

      revenueGrowthPercentageCharacter () {
        return this.growthPercentageCharacter(this.revenueGrowthPercentage)
      },

      loadGrowthPercentage () {
        return this.growthPercentage(this.selectedPeriodStats.occupied, this.previousPeriodStats.occupied)
      },

      loadGrowthClass () {
        return this.growthClass(this.loadGrowthPercentage)
      },

      loadGrowthPercentageCharacter () {
        return this.growthPercentageCharacter(this.loadGrowthPercentage)
      },

      customersGrowthPercentage () {
        return this.growthPercentage(this.totalCustomers, this.totalPastPeriodCustomers)
      },

      customerGrowthClass () {
        return this.growthClass(this.customersGrowthPercentage)
      },

      customerGrowthPercentageCharacter () {
        return this.growthPercentageCharacter(this.customersGrowthPercentage)
      }
    },

    components: {
      BarChart,
      DoughnutChart,
      LineChart,
      DialogCustomer,
      DialogAppointment,
      DialogPayment,
      PageHeader,
      DialogExport,
      AppointmentListCollapsed,
      PaginationBlock
    }

  }
</script>
