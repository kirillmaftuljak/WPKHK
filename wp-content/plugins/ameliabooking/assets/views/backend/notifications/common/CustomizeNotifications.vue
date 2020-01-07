<template>
  <div>

    <!-- Customize Notifications -->
    <el-row class="am-customize-notifications">

      <!-- User Type Tabs -->
      <el-col :md="8" class="">
        <div class="am-section am-gray-section">
          <el-tabs v-model="userTypeTab" @tab-click="onChangeUserTypeTab">

            <!-- To Customer -->
            <el-tab-pane :label="$root.labels.to_customer" name="customer">
              <div v-for="entity in ['appointment', 'event']" class="am-email-notification-buttons">

                <div class="am-email-notification-labels">
                  {{$root.labels[entity]}} {{$root.labels.notifications}}
                </div>

                <!-- Customer's Notifications -->
                <div v-for="(item, index) in customerNotifications(entity)" class="am-button-checkbox">

                  <!-- Customer's Notification Button -->
                  <el-button
                      size="large"
                      :key="index"
                      @click="getNotification(item.id)"
                      :class="{ 'am-active': item.id === notification.id, 'am-lite-disabled': isDisabled('customer', item) }"
                      :disabled="isDisabled('customer', item)"
                  >
                    {{ $root.labels[item.name] }}
                  </el-button>
                  <!-- /Customer's Notification Button -->

                  <!-- Customer's Notification Status Checkbox -->
                  <el-checkbox
                      v-model="item.status"
                      @change="changeNotificationStatus(item)"
                      :disabled="isDisabled('customer', item)"
                      true-label="enabled"
                      false-label="disabled"
                  >
                  </el-checkbox>
                  <!-- /Customer's Notification Status Checkbox -->

                  <!-- Customer's Notification Tooltip For Scheduled Notifications -->
                  <el-tooltip
                      v-if="item.time || item.timeBefore || item.timeAfter"
                      class="item"
                      effect="dark"
                      :content="$root.labels.requires_scheduling_setup"
                      placement="top"
                  >
                    <span class="am-cron-icon" :class="{ 'active': item.id === notification.id }">
                      <img class="svg" :src="$root.getUrl+'public/img/cron-job.svg'"/>
                    </span>
                  </el-tooltip>
                  <!-- /Customer's Notification Tooltip For Scheduled Notifications -->

                </div>
                <!-- /Customer's Notification -->

              </div>
            </el-tab-pane>
            <!-- /To Customer -->

            <!-- To Employee -->
            <el-tab-pane :label="$root.labels.to_employee" name="provider">
              <div v-for="entity in ['appointment', 'event']" class="am-email-notification-buttons">

                <div class="am-email-notification-labels">
                  {{$root.labels[entity]}} {{$root.labels.notifications}}
                </div>

                <!-- Employees's Notifications -->
                <div v-for="(item, index) in employeeNotifications(entity)" class="am-button-checkbox">

                  <!-- Employees's Notification Button -->
                  <el-button
                      size="large"
                      :key="index"
                      @click="getNotification(item.id)"
                      :class="{ 'am-active': item.id === notification.id, 'am-lite-disabled': isDisabled('provider', item) }"
                      :disabled="isDisabled('provider', item)"
                  >
                    {{ $root.labels[item.name] }}
                  </el-button>
                  <!-- /Employees's Notification Button -->

                  <!-- Employees's Notification Status Checkbox -->
                  <el-checkbox
                      v-model="item.status"
                      @change="changeNotificationStatus(item)"
                      :disabled="isDisabled('provider', item)"
                      true-label="enabled"
                      false-label="disabled"
                  >
                  </el-checkbox>
                  <!-- /Employees's Notification Status Checkbox -->

                  <!-- Employee's Notification Tooltip For Scheduled Notifications -->
                  <el-tooltip
                      v-if="item.time || item.timeBefore || item.timeAfter"
                      class="item"
                      effect="dark"
                      :content="$root.labels.requires_scheduling_setup"
                      placement="top"
                  >
                    <span class="am-cron-icon" :class="{ 'active': item.id === notification.id }">
                      <img class="svg" :src="$root.getUrl+'public/img/cron-job.svg'"/>
                    </span>
                  </el-tooltip>
                  <!-- /Employee's Notification Tooltip For Scheduled Notifications -->

                </div>
                <!-- /Employees's Notifications -->

              </div>
            </el-tab-pane>
            <!-- /To Employee -->

          </el-tabs>
        </div>
      </el-col>
      <!-- /User Type Tabs -->

      <!-- Right Side Content -->
      <el-col :md="16">

        <!-- Content -->
        <div class="am-section am-email-form-settings">
          <transition name="fadeIn">
            <el-form :model="notification" ref="notification">

              <!-- Name & Show Email Codes -->
              <el-row :gutter="16">

                <!-- Notification Name -->
                <el-col :span="12">
                  <div>
                    <h2>{{ $root.labels[notification.name] }}</h2>
                  </div>
                </el-col>
                <!-- /Notification Name -->

                <!-- Show Email Codes Button -->
                <el-col :span="12">
                  <div class="align-right">
                    <p class="am-blue-link" @click="showDialogPlaceholders">
                      {{ $root.labels['show_' + type + '_codes'] }}
                    </p>
                  </div>
                </el-col>
                <!-- /Show Email Codes Button -->

              </el-row>
              <!-- /Name & Show Email Codes -->

              <!-- Inputs -->
              <el-row :gutter="16">

                <!-- Subject -->
                <el-col :span="notificationTimeBased ? 18 : 24" v-if="type === 'email'">
                  <el-form-item :label="$root.labels.subject + ':'">
                    <el-input type="text" v-model="notification.subject"></el-input>
                  </el-form-item>
                </el-col>
                <!-- /Subject -->

                <!-- Time -->
                <el-col v-if="notificationTime" :span="6">
                  <el-form-item :label="$root.labels.scheduled_for + ':'">
                    <el-time-select
                        v-model="notificationTime"
                        :picker-options="timeSelectOptions"
                        :clearable="false"
                    >
                    </el-time-select>
                  </el-form-item>
                </el-col>
                <!-- /Time -->

                <!-- Time Before -->
                <el-col v-if="notification.timeBefore" :span="6">
                  <el-form-item :label="$root.labels.scheduled_before + ':'">
                    <el-select v-model="notification.timeBefore">
                      <el-option
                          v-for="item in getPossibleDurationsInSeconds(notification.timeBefore, 86400)"
                          :key="item"
                          :label="secondsToNiceDuration(item)"
                          :value="item"
                      >
                      </el-option>
                    </el-select>
                  </el-form-item>
                </el-col>
                <!-- /Time Before -->

                <!-- Time After -->
                <el-col v-if="notification.timeAfter" :span="6">
                  <el-form-item :label="$root.labels.scheduled_after + ':'">
                    <el-select v-model="notification.timeAfter">
                      <el-option
                          v-for="item in getPossibleDurationsInSeconds(notification.timeAfter, 86400)"
                          :key="item"
                          :label="secondsToNiceDuration(item)"
                          :value="item"
                      >
                      </el-option>
                    </el-select>
                  </el-form-item>
                </el-col>
                <!-- /Time After -->

              </el-row>
              <!-- /Inputs -->

              <!-- Message -->
              <el-form-item :label="$root.labels.message_colon">

                <!-- Quill Editor -->
                <quill-editor
                    v-model="notification.content" v-if="type === 'email'"
                    :options="editorOptions"
                >
                </quill-editor>
                <!-- /Quill Editor -->

                <!-- Textarea -->
                <el-input
                    v-if="type === 'sms'"
                    v-model="notification.content"
                    type="textarea"
                    :rows="7"
                    placeholder=""
                >
                </el-input>
                <!-- /Textarea -->

              </el-form-item>
              <!-- /Message -->

              <!-- Cron Message -->
              <el-alert
                  v-if="notificationTimeBased === true"
                  class="am-alert"
                  :title="$root.labels.cron_instruction + ':'"
                  type="info"
                  :description="'*/15 * * * * ' + $root.getAjaxUrl + '/notifications/scheduled/send'"
                  show-icon
                  :closable="false">
              </el-alert>
              <!-- /Cron Message -->

              <!-- Cancel & Save Buttons -->
              <el-row :gutter="16">

                <!-- Cancel Button -->
                <el-col :span="12">
                  <div>
                    <el-button size="small" @click="openTestNotificationModal">
                      {{ $root.labels['send_test_' + type] }}
                    </el-button>
                  </div>
                </el-col>
                <!-- /Cancel Button -->

                <!-- Save Button -->
                <el-col :span="12">
                  <div class="align-right">
                    <el-button @click="updateNotification()" :loading="!fetchedUpdate" size="small" type="primary">
                      {{ $root.labels.save }}
                    </el-button>
                  </div>
                </el-col>
                <!-- /Save Button -->

              </el-row>
              <!-- /Cancel & Save Buttons -->

            </el-form>
          </transition>
        </div>
        <!-- /Content -->

      </el-col>
      <!-- /Right Side Content -->

    </el-row>
    <!-- /Customize Notifications -->

    <!-- Dialog Placeholders -->
    <transition name="slide">
      <el-dialog
          class="am-side-dialog am-dialog-email-codes"
          :visible.sync="dialogPlaceholders"
          :show-close="false"
          v-if="dialogPlaceholders"
      >
        <dialog-placeholders
            :entity="entity"
            :type="type"
            :customFields="customFields"
            :categories="categories"
            :coupons="coupons"
            @closeDialogPlaceholders="dialogPlaceholders=false"
        >
        </dialog-placeholders>
      </el-dialog>
    </transition>
    <!-- Dialog Placeholders -->

    <!-- Test Notification Modal -->
    <el-dialog :title="$root.labels['send_test_' + type]" class="am-pop-modal" :visible.sync="testNotificationModal">

      <!-- Configure Sender Email Warning -->
      <el-alert
          v-if="$root.settings.notifications.senderEmail === '' && type === 'email'"
          type="warning"
          show-icon
          title=""
          :description="$root.labels.test_email_warning"
          :closable="false"
      >
      </el-alert>
      <!-- /Configure Sender Email Warning -->

      <!-- SMS Balance Warning -->
      <el-alert
          v-if="true === true && type === 'sms' && type === 'sms' && !user.balance"
          type="warning"
          show-icon
          title=""
          :description="$root.labels.test_sms_warning"
          :closable="false"
      >
      </el-alert>
      <!-- /SMS Balance Warning -->

      <!-- Form -->
      <el-form
          v-if="testNotificationModal"
          :model="testNotification"
          ref="testNotification"
          :rules="rules"
          label-position="top"
          @submit.prevent="sendTestNotification"
          v-loading="testNotificationLoading"
      >

        <!-- Recipient Email -->
        <el-form-item v-if="type === 'email'" :label="$root.labels.recipient_email" prop="recipientEmail">
          <el-input
              v-model="testNotification.recipientEmail"
              :placeholder="$root.labels.email_placeholder"
              @input="clearValidation()"
              auto-complete="off"
          >
          </el-input>
        </el-form-item>
        <!-- /Recipient Email -->

        <!-- Recipient Phone -->
        <el-form-item v-if="type === 'sms'" :label="$root.labels.recipient_phone" prop="recipientPhone">
          <phone-input
              :savedPhone="testNotification.recipientPhone"
              @phoneFormatted="phoneFormatted"
          >
          </phone-input>
        </el-form-item>
        <!-- /Recipient Email -->

        <!-- Notification Template -->
        <el-form-item :label="$root.labels.notification_template" prop="notificationTemplate">
          <el-select v-model="testNotification.notificationTemplate">
            <el-option
                v-for="notification in notifications.filter(n => n.type === type)"
                :key="notification.id"
                :label="notification.sendTo === 'provider' ? $root.labels.employee + ' ' + $root.labels[notification.name] : $root.labels.customer + ' ' + $root.labels[notification.name]"
                :value="notification.name"
            >
            </el-option>
          </el-select>
        </el-form-item>
        <!-- /Notification Template -->

      </el-form>
      <!-- /Form -->

      <!-- Cancel & Send Buttons -->
      <span slot="footer" class="dialog-footer">

        <!-- Cancel Button -->
        <el-button size="small" @click="testNotificationModal = false">
          {{ $root.labels.cancel }}
        </el-button>
        <!-- /Cancel Button -->

        <!-- Send Button -->
        <el-button
            size="small"
            type="primary"
            @click="sendTestNotification"
            :loading="testNotificationLoading"
            :disabled="disabledSendTestNotification"
        >
          {{ $root.labels.send }}
        </el-button>
        <!-- /Send Button -->

      </span>
      <!-- /Cancel & Send Buttons -->

    </el-dialog>
    <!-- /Test Notification Modal -->

  </div>
</template>

<script>
  import durationMixin from '../../../../js/common/mixins/durationMixin'
  import Form from 'form-object'
  import { quillEditor } from 'vue-quill-editor'
  import notifyMixin from '../../../../js/backend/mixins/notifyMixin'
  import imageMixin from '../../../../js/common/mixins/imageMixin'
  import DialogPlaceholders from './DialogPlaceholders.vue'
  import PhoneInput from '../../../parts/PhoneInput.vue'
  import notificationMixin from '../../../../js/backend/mixins/notificationMixin'

  export default {
    mixins: [durationMixin, notifyMixin, imageMixin, notificationMixin],

    props: {
      categories: {
        default: () => [],
        type: Array
      },
      customFields: {
        default: () => [],
        type: Array
      },
      coupons: {
        default: () => [],
        type: Array
      },
      notifications: {
        default: () => [],
        type: Array
      },
      type: {
        default: 'email',
        type: String
      },
      user: {
        default: () => {},
        type: Object
      }
    },

    data () {
      let validatePhone = (rule, input, callback) => {
        if (input !== '' && !input.startsWith('+')) {
          callback(new Error(this.$root.labels.enter_valid_phone_warning))
        } else {
          callback()
        }
      }

      return {
        dialogPlaceholders: false,
        editorOptions: {
          modules: {
            toolbar: [
              ['bold', 'italic', 'underline', 'strike'],
              ['blockquote'],
              [{'list': 'ordered'}, {'list': 'bullet'}],
              [{'script': 'sub'}, {'script': 'super'}],
              [{'indent': '-1'}, {'indent': '+1'}],
              [{'direction': 'rtl'}],
              [{'size': ['small', false, 'large', 'huge']}],
              [{'header': [1, 2, 3, 4, 5, 6, false]}],
              [{'font': []}],
              [{'color': []}, {'background': []}],
              [{'align': []}],
              ['clean'],
              ['link', 'image']
            ]
          }
        },
        fetchedUpdate: true,
        form: new Form(),
        notification: {},
        entity: 'appointment',
        rules: {
          recipientEmail: [
            {required: true, message: this.$root.labels.enter_recipient_email_warning, trigger: 'submit'},
            {type: 'email', message: this.$root.labels.enter_valid_email_warning, trigger: 'submit'}
          ],
          recipientPhone: [
            {required: true, message: this.$root.labels.enter_recipient_phone_warning, trigger: 'submit'},
            {validator: validatePhone, trigger: 'submit'}
          ],
          notificationTemplate: [
            {required: true, message: this.$root.labels.select_email_template_warning, trigger: 'submit'}
          ]
        },
        testNotification: {
          recipientEmail: '',
          recipientPhone: '',
          notificationTemplate: 'customer_appointment_approved',
          type: null
        },
        testNotificationLoading: false,
        testNotificationModal: false,
        userTypeTab: 'customer'
      }
    },

    mounted () {
      this.getNotification(null)
    },

    methods: {
      onChangeUserTypeTab (tab) {
        this.inlineSVG()
        if (this.notification.type !== tab.name) {
          this.notification = this.notifications.find(
            notification => notification.type === this.type && notification.sendTo === tab.name
          )
        }
      },

      getNotification (id) {
        if (id === null) {
          this.notification = this.notifications.find(notification => notification.type === this.type)
        } else {
          this.notification = this.notifications.find(notification => notification.id === id)
        }

        this.entity = this.notification.entity
        this.testNotification.type = this.notification.entity
        this.testNotification.notificationTemplate = this.notification.name
      },

      updateNotification () {
        this.fetchedUpdate = false

        this.form.post(
          `${this.$root.getAjaxUrl}/notifications/${this.notification.id}`, this.notification
        ).then((response) => {
          if (response.data.update) {
            this.notification.content = response.data.notification.content
          }
          this.fetchedUpdate = true
          this.notify(this.$root.labels.success, this.$root.labels.notification_saved, 'success')
        }).catch(() => {
          this.fetchedUpdate = true
          this.notify(this.$root.labels.error, this.$root.labels.notification_not_saved, 'error')
        })
      },

      changeNotificationStatus (notification) {
        this.fetchedUpdate = false
        this.form.post(
          `${this.$root.getAjaxUrl}/notifications/status/${notification.id}`, notification
        ).then(() => {
          this.fetchedUpdate = true
        }).catch(() => {
          this.fetchedUpdate = true
        })
      },

      showDialogPlaceholders () {
        this.dialogPlaceholders = true
      },

      openTestNotificationModal () {
        this.testNotificationModal = true
      },

      sendTestNotification () {
        this.type === 'email' ? this.sendTestEmail() : this.sendTestSMS()
      },

      sendTestEmail () {
        this.$refs.testNotification.validate((valid) => {
          if (valid) {
            this.testNotificationLoading = true
            this.form.post(
              `${this.$root.getAjaxUrl}/notifications/email/test`, this.testNotification
            ).then(() => {
              this.onSendTestNotificationSuccess()
            }).catch(() => {
              this.onSendTestNotificationError()
            })
          } else {
            return false
          }
        })
      },

      sendTestSMS () {
        this.$refs.testNotification.validate((valid) => {
          if (valid) {
            this.testNotificationLoading = true
            this.sendAmeliaSmsApiRequest('testNotification', this.onSendTestNotificationSuccess, this.onSendTestNotificationError)
          } else {
            return false
          }
        })
      },

      onSendTestNotificationSuccess () {
        this.clearValidation()
        this.testNotificationModal = false
        this.testNotificationLoading = false
        this.testNotification = this.resetTestNotificationOnInitialState()
        this.notify(this.$root.labels.success, this.$root.labels['test_' + this.type + '_success'], 'success')
      },

      onSendTestNotificationError () {
        this.testNotificationLoading = false
        this.notify(this.$root.labels.error, this.$root.labels['test_' + this.type + '_error'], 'error')
      },

      phoneFormatted (phone) {
        this.clearValidation()
        this.testNotification.recipientPhone = phone
      },

      clearValidation () {
        if (typeof this.$refs.testNotification !== 'undefined') {
          this.$refs.testNotification.clearValidate()
        }
      },

      resetTestNotificationOnInitialState () {
        return {
          recipientEmail: '',
          recipientPhone: '',
          notificationTemplate: this.notification.name,
          type: this.entity
        }
      },

      isDisabled: function (type, item) {
        item.status = (item.name !== (type + '_appointment_approved') && item.name !== (type + '_appointment_pending') && item.name !== (type + '_event_approved')) ? 'disabled' : item.status

        return this.$root.isLite ? (item.name !== (type + '_appointment_approved') && item.name !== (type + '_appointment_pending') && item.name !== (type + '_event_approved')) : false
      },

      customerNotifications (entity) {
        return this.notifications.filter(
          notification => notification.sendTo === 'customer' && notification.type === this.type && notification.entity === entity
        )
      },

      employeeNotifications (entity) {
        return this.notifications.filter(
          notification => notification.sendTo === 'provider' && notification.type === this.type && notification.entity === entity
        )
      },
    },

    computed: {
      notificationTime: {
        get () {
          if (this.notification.time !== null) {
            return this.$moment(this.notification.time, 'HH:mm:ss').format('HH:mm')
          }

          return null
        },
        set (selected) {
          this.notification.time = this.$moment(selected, 'HH:mm').format('HH:mm:ss')
        }
      },

      notificationTimeBased () {
        return this.notification.time !== null || this.notification.timeBefore !== null || this.notification.timeAfter !== null
      },

      disabledSendTestNotification () {
        if (this.type === 'email' && !this.$root.settings.notifications.senderEmail) {
          return true
        }

        return this.type === 'sms' && (typeof this.user !== 'undefined' && !this.user.balance)
      }
    },

    components: {
      quillEditor,
      DialogPlaceholders,
      PhoneInput
    }
  }
</script>