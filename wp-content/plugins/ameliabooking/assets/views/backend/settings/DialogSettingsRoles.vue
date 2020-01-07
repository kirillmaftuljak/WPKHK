<template>
  <div>

    <div class="am-dialog-scrollable">

      <!-- Dialog Header -->
      <div class="am-dialog-header">
        <el-row>
          <el-col :span="20">
            <h2>{{ $root.labels.roles_settings }}</h2>
          </el-col>
          <el-col :span="4" class="align-right">
            <el-button @click="closeDialog" class="am-dialog-close" size="small" icon="el-icon-close"></el-button>
          </el-col>
        </el-row>
      </div>
      <!-- /Dialog Header -->

      <!-- Form -->
      <el-form label-position="top" @submit.prevent="onSubmit">
        <el-tabs>

          <!-- Employee -->
          <el-tab-pane :label="$root.labels.employee">
            <BlockLite/>
            <br>
            <!-- Default allow configure own schedule -->
            <div class="am-setting-box am-switch-box" :class="{'am-lite-disabled': ($root.isLite)}" >
              <el-row type="flex" align="middle" :gutter="24">
                <el-col :span="20">
                  {{ $root.labels.allow_configure_schedule }}
                </el-col>
                <el-col :span="4" class="align-right">
                  <el-switch
                      v-model="settings.allowConfigureSchedule"
                      active-text=""
                      inactive-text=""
                      :disabled="$root.isLite"
                  >
                  </el-switch>
                </el-col>
              </el-row>
            </div>
            <!-- /Allow employees configuring their own schedule -->

            <!-- Allow employees configuring their own days off -->
            <div class="am-setting-box am-switch-box" :class="{'am-lite-disabled': ($root.isLite)}">
              <el-row type="flex" align="middle" :gutter="24">
                <el-col :span="20">
                  {{ $root.labels.allow_configure_days_off }}
                </el-col>
                <el-col :span="4" class="align-right">
                  <el-switch
                      v-model="settings.allowConfigureDaysOff"
                      active-text=""
                      inactive-text=""
                      :disabled="$root.isLite"
                  >
                  </el-switch>
                </el-col>
              </el-row>
            </div>
            <!-- /Allow employees configuring their own days off -->

            <!-- Allow employees configuring their own special days -->
            <div class="am-setting-box am-switch-box" :class="{'am-lite-disabled': ($root.isLite)}">
              <el-row type="flex" align="middle" :gutter="24">
                <el-col :span="20">
                  {{ $root.labels.allow_configure_special_days }}
                </el-col>
                <el-col :span="4" class="align-right">
                  <el-switch
                      v-model="settings.allowConfigureSpecialDays"
                      active-text=""
                      inactive-text=""
                      :disabled="$root.isLite"
                  >
                  </el-switch>
                </el-col>
              </el-row>
            </div>
            <!-- /Allow employees configuring their own special days -->

            <!-- Default allow provider add/edit appointment -->
            <div class="am-setting-box am-switch-box" :class="{'am-lite-disabled': ($root.isLite)}">
              <el-row type="flex" align="middle" :gutter="24">
                <el-col :span="20">
                  {{ $root.labels.allow_write_appointments }}
                </el-col>
                <el-col :span="4" class="align-right">
                  <el-switch
                      v-model="settings.allowWriteAppointments"
                      active-text=""
                      inactive-text=""
                      :disabled="$root.isLite"
                  >
                  </el-switch>
                </el-col>
              </el-row>
            </div>
            <!-- /Allow employees managing their appointments -->

            <!-- Allow employees managing their events -->
            <div class="am-setting-box am-switch-box" :class="{'am-lite-disabled': ($root.isLite)}">
              <el-row type="flex" align="middle" :gutter="24">
                <el-col :span="20">
                  {{ $root.labels.allow_write_events }}
                </el-col>
                <el-col :span="4" class="align-right">
                  <el-switch
                      v-model="settings.allowWriteEvents"
                      active-text=""
                      inactive-text=""
                  >
                  </el-switch>
                </el-col>
              </el-row>
            </div>
            <!-- /Allow employees managing their appointments -->

          </el-tab-pane>
          <!-- /Employee -->

          <!-- Customer -->
          <el-tab-pane :label="$root.labels.customer">

            <!-- Automatically create Amelia Customer user -->
            <el-popover :disabled="!$root.isLite" ref="automaticallyCreateCustomer" v-bind="$root.popLiteProps"><PopLite/></el-popover>
            <div class="am-setting-box am-switch-box" v-popover:automaticallyCreateCustomer :class="{'am-lite-disabled': ($root.isLite)}">
              <el-row type="flex" align="middle" :gutter="24">
                <el-col :span="20">
                  {{ $root.labels.automatically_create_customer }}
                  <el-tooltip placement="top">
                    <div slot="content" v-html="$root.labels.automatically_create_customer_tooltip"></div>
                    <i class="el-icon-question am-tooltip-icon"></i>
                  </el-tooltip>
                </el-col>
                <el-col :span="4" class="align-right">
                  <el-switch
                      v-model="settings.automaticallyCreateCustomer"
                      active-text=""
                      inactive-text=""
                      :disabled="$root.isLite"
                  >
                  </el-switch>
                </el-col>
              </el-row>
            </div>
            <!-- /Automatically create Amelia Customer user -->

            <!-- Check customer's name for existing email when booking -->
            <div class="am-setting-box am-switch-box">
              <el-row type="flex" align="middle" :gutter="24">
                <el-col :span="20">
                  {{ $root.labels.inspect_customer_info }}
                  <el-tooltip placement="top">
                    <div slot="content" v-html="$root.labels.inspect_customer_info_tooltip"></div>
                    <i class="el-icon-question am-tooltip-icon"></i>
                  </el-tooltip>
                </el-col>
                <el-col :span="4" class="align-right">
                  <el-switch
                      v-model="settings.inspectCustomerInfo"
                      active-text=""
                      inactive-text=""
                  >
                  </el-switch>
                </el-col>
              </el-row>
            </div>
            <!-- /Check customer's name for existing email when booking -->

            <!-- Allow customers rescheduling their appointments -->
            <el-popover :disabled="!$root.isLite" ref="allowCustomerReschedule" v-bind="$root.popLiteProps"><PopLite/></el-popover>
            <div class="am-setting-box am-switch-box" v-popover:allowCustomerReschedule :class="{'am-lite-disabled': ($root.isLite)}">
              <el-row type="flex" align="middle" :gutter="24">
                <el-col :span="20">
                  {{ $root.labels.allow_customer_reschedule }}
                  <el-tooltip placement="top">
                    <div slot="content" v-html="$root.labels.allow_customer_reschedule_tooltip"></div>
                    <i class="el-icon-question am-tooltip-icon"></i>
                  </el-tooltip>
                </el-col>
                <el-col :span="4" class="align-right">
                  <el-switch
                      v-model="settings.allowCustomerReschedule"
                      active-text=""
                      inactive-text=""
                      :disabled="$root.isLite"
                  >
                  </el-switch>
                </el-col>
              </el-row>
            </div>
            <!-- Allow customers rescheduling their appointments -->
          </el-tab-pane>

        </el-tabs>
      </el-form>
      <!-- /Form -->

    </div>

    <!-- Dialog Footer -->
    <div class="am-dialog-footer">
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
      roles: {
        type: Object
      }
    },

    data () {
      return {
        settings: Object.assign({}, this.roles)
      }
    },

    updated () {
      this.inlineSVG()
    },

    mounted () {
      this.inlineSVG()
    },

    methods: {
      closeDialog () {
        this.$emit('closeDialogSettingsRoles')
      },
      onSubmit () {
        this.$emit('closeDialogSettingsRoles')
        this.$emit('updateSettings', {'roles': this.settings})
      }
    }
  }
</script>
