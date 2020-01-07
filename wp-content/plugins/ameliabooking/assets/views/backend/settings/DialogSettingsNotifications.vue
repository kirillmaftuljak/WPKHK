<template>
  <div>
    <div class="am-dialog-scrollable">

      <!-- Dialog Header -->
      <div class="am-dialog-header">
        <el-row>
          <el-col :span="20">
            <h2>{{ $root.labels.notifications_settings }}</h2>
          </el-col>
          <el-col :span="4" class="align-right">
            <el-button @click="closeDialog" class="am-dialog-close" size="small" icon="el-icon-close"></el-button>
          </el-col>
        </el-row>
      </div>

      <!-- Form -->
      <el-form :model="settings" ref="settings" :rules="rules" label-position="top" @submit.prevent="onSubmit">

        <!-- Mail Service -->
        <el-form-item :label="$root.labels.mail_service + ':'">
          <el-select v-model="settings.mailService" @change="changeMailService()">
            <el-option
                v-for="item in options.mailServices"
                :key="item.value"
                :label="item.label"
                :value="item.value"
                :disabled="item.value !== 'php' && $root.isLite"
            >
            </el-option>
          </el-select>
        </el-form-item>

        <!-- SMTP Host -->
        <el-form-item
            v-show="settings.mailService === 'smtp'"
            :label="$root.labels.smtp_host + ':'"
            prop="smtpHost"
        >
          <el-input
              v-model="settings.smtpHost"
              @input="clearValidation()"
          >
          </el-input>
        </el-form-item>

        <!-- SMTP Port -->
        <el-form-item
            v-show="settings.mailService === 'smtp'"
            :label="$root.labels.smtp_port + ':'"
            prop="smtpPort"
        >
          <el-input
              v-model="settings.smtpPort"
              @input="clearValidation()"
          >
          </el-input>
        </el-form-item>

        <!-- SMTP Secure -->
        <el-form-item
            v-show="settings.mailService === 'smtp'"
            :label="$root.labels.smtp_secure + ':'"
        >
          <el-select v-model="settings.smtpSecure">
            <el-option
                v-for="item in options.smtpSecureOptions"
                :key="item.value"
                :label="item.label"
                :value="item.value"
            >
            </el-option>
          </el-select>
        </el-form-item>

        <!-- SMTP Username -->
        <el-form-item
            v-show="settings.mailService === 'smtp'"
            :label="$root.labels.smtp_username + ':'"
            prop="smtpUsername"
        >
          <el-input
              v-model="settings.smtpUsername"
              @input="clearValidation()"
          >
          </el-input>
        </el-form-item>

        <!-- SMTP Password -->
        <el-form-item
            v-show="settings.mailService === 'smtp'"
            :label="$root.labels.smtp_password + ':'"
            prop="smtpPassword"
        >
          <el-input
              type="password"
              v-model="settings.smtpPassword"
              @input="clearValidation()"
          >
          </el-input>
        </el-form-item>

        <!-- Mailgun Key -->
        <el-form-item
            v-show="settings.mailService === 'mailgun'"
            :label="$root.labels.mailgun_api_key + ':'"
            prop="mailgunApiKey"
        >
          <el-input
              v-model="settings.mailgunApiKey"
              @input="clearValidation()"
          >
          </el-input>
        </el-form-item>

        <!-- Mailgun Domain -->
        <el-form-item
            v-show="settings.mailService === 'mailgun'"
            :label="$root.labels.mailgun_domain + ':'"
            prop="mailgunDomain"
        >
          <el-input
              v-model="settings.mailgunDomain"
              @input="clearValidation()"
          >
          </el-input>
        </el-form-item>

        <!-- Sender Name -->
        <el-form-item :label="$root.labels.sender_name + ':'" prop="senderName">
          <el-input
              v-model="settings.senderName"
              @input="clearValidation()"
          >
          </el-input>
        </el-form-item>

        <!-- Sender Email -->
        <el-form-item :label="$root.labels.sender_email +':'" prop="senderEmail">
          <el-input
              v-model="settings.senderEmail"
              :placeholder="$root.labels.email_placeholder"
              @input="clearValidation()"
          >
          </el-input>
        </el-form-item>

        <!-- Notify Customers By Default -->
        <div class="am-setting-box am-switch-box">
          <el-row type="flex" align="middle" :gutter="24">
            <el-col :span="16">
              <p>{{ $root.labels.notify_customers_default }}</p>
            </el-col>
            <el-col :span="8" class="align-right">
              <el-switch v-model="settings.notifyCustomers" active-text="" inactive-text=""></el-switch>
            </el-col>
          </el-row>
        </div>

        <!-- Cancel Booking Success URL -->
        <el-popover :disabled="!$root.isLite" ref="cancelSuccessPop" v-bind="$root.popLiteProps"><PopLite/></el-popover>
        <el-form-item :label="$root.labels.cancel_success_url +':'" v-popover:cancelSuccessPop>
          <el-input
              v-model="settings.cancelSuccessUrl"
              :placeholder="$root.labels.cancel_url_placeholder"
              @input="clearValidation()"
              :disabled="$root.isLite"
          >
          </el-input>
        </el-form-item>

        <!-- Cancel Booking Error URL -->
        <el-popover :disabled="!$root.isLite" ref="cancelErrorPop" v-bind="$root.popLiteProps"><PopLite/></el-popover>
        <el-form-item label="placeholder" v-popover:cancelErrorPop>
          <label slot="label">
            {{ $root.labels.cancel_error_url }}:
            <el-tooltip placement="top">
              <div slot="content" v-html="$root.labels.cancel_error_url_tooltip"></div>
              <i class="el-icon-question am-tooltip-icon"></i>
            </el-tooltip>
          </label>
          <el-input
              v-model="settings.cancelErrorUrl"
              :placeholder="$root.labels.cancel_url_placeholder"
              @input="clearValidation()"
              :disabled="$root.isLite"
          >
          </el-input>
        </el-form-item>

        <!-- Send all notifications to the additional address (bcc) -->
        <el-form-item label="placeholder" prop="bccEmail">
          <label slot="label">
            {{ $root.labels.bcc_email }}:
            <el-tooltip placement="top">
              <div slot="content" v-html="$root.labels.bcc_email_tooltip"></div>
              <i class="el-icon-question am-tooltip-icon"></i>
            </el-tooltip>
          </label>
          <el-select
              class="am-setting-notifications-select"
              v-model="bccEmails"
              multiple
              filterable
              allow-create
              default-first-option
              :no-data-text="$root.labels.event_tags_create"
              @change="emailsChanged"
          >
            <el-option
                v-for="(email, index) in bccEmails"
                :key="index"
                :label="email"
                :value="email"
            >
            </el-option>
          </el-select>
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

  export default {

    mixins: [imageMixin],

    props: {
      notifications: {
        type: Object
      }
    },

    data () {
      return {
        bccEmails: [],
        settings: Object.assign({}, this.notifications),
        options: {
          mailServices: [
            {
              label: this.$root.labels.php_mail,
              value: 'php'
            },
            {
              label: this.$root.labels.wp_mail,
              value: 'wp_mail'
            },
            {
              label: this.$root.labels.smtp,
              value: 'smtp'
            },
            {
              label: this.$root.labels.mailgun,
              value: 'mailgun'
            }
          ],
          smtpSecureOptions: [
            {
              label: this.$root.labels.smtp_secure_ssl,
              value: 'ssl'
            },
            {
              label: this.$root.labels.smtp_secure_tls,
              value: 'tls'
            },
            {
              label: this.$root.labels.smtp_secure_disabled,
              value: false
            }
          ]
        },
        rules: {
          senderName: [
            {required: true, message: this.$root.labels.sender_name_warning, trigger: 'submit'}
          ],
          senderEmail: [
            {required: true, message: this.$root.labels.sender_email_warning, trigger: 'submit'},
            {type: 'email', message: this.$root.labels.enter_valid_email_warning, trigger: 'submit'}
          ],
          smtpHost: [],
          smtpPort: [],
          smtpUsername: [],
          smtpPassword: [],
          mailgunApiKey: [],
          mailgunDomain: []
        }
      }
    },

    updated () {
      this.inlineSVG()
    },

    mounted () {
      if (this.settings.bccEmail) {
        this.bccEmails = this.settings.bccEmail.split(',')
      }

      this.inlineSVG()
      this.changeMailService()
    },

    methods: {
      closeDialog () {
        this.$emit('closeDialogSettingsNotifications')
      },

      changeMailService () {
        this.clearValidation()

        this.rules.smtpHost = []
        this.rules.smtpPort = []
        this.rules.smtpUsername = []
        this.rules.smtpPassword = []
        this.rules.mailgunApiKey = []
        this.rules.mailgunDomain = []

        if (this.settings.mailService === 'smtp') {
          this.rules.smtpHost = [{
            required: true,
            message: this.$root.labels.smtp_host_warning,
            trigger: 'submit'
          }]
          this.rules.smtpPort = [{
            required: true,
            message: this.$root.labels.smtp_port_warning,
            trigger: 'submit'
          }]
          this.rules.smtpUsername = [{
            required: true,
            message: this.$root.labels.smtp_username_warning,
            trigger: 'submit'
          }]
          this.rules.smtpPassword = [{
            required: true,
            message: this.$root.labels.smtp_password_warning,
            trigger: 'submit'
          }]
        }

        if (this.settings.mailService === 'mailgun') {
          this.rules.mailgunApiKey = [{
            required: true,
            message: this.$root.labels.mailgun_api_key_warning,
            trigger: 'submit'
          }]
          this.rules.mailgunDomain = [{
            required: true,
            message: this.$root.labels.mailgun_domain_warning,
            trigger: 'submit'
          }]
        }
      },

      emailsChanged (emails) {
        if (emails.length && (emails[emails.length - 1].trim() === '' || !(/(.+)@(.+){2,}\.(.+){2,}/.test(emails[emails.length - 1])))) {
          emails.pop()
        }
      },

      onSubmit () {
        this.$refs.settings.validate((valid) => {
          if (valid) {
            this.$emit('closeDialogSettingsNotifications')
            this.$emit('updateSettings', {'notifications': Object.assign(this.settings, {bccEmail: this.bccEmails.join(',')})})
          }
        })
      },

      clearValidation () {
        this.$refs.settings.clearValidate()
      }
    },

    components: {}
  }
</script>