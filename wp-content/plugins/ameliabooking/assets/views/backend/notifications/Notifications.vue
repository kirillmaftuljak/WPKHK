<template>
  <div id="am-notifications" class="am-wrap">
    <div id="am-email-notifications" class="am-body">

      <!-- Page Header -->
      <page-header></page-header>
      <!-- /Page Header -->

      <!-- Spinner -->
      <div class="am-spinner am-section" v-if="!fetched">
        <img :src="$root.getUrl + 'public/img/spinner.svg'"/>
      </div>
      <!-- /Spinner -->

      <!-- Notifications Tab -->
      <div class="am-notifications am-section">
        <el-tabs v-model="notificationTab" @tab-click="inlineSVG()" v-if="fetched">

          <!-- Email Notifications -->
          <el-tab-pane :label="$root.labels.email_notifications" name="email">
            <customize-notifications
                :notifications="notifications"
                :customFields="options.entities.customFields"
                :categories="options.entities.categories"
                :coupons="options.entities.coupons"
                type="email"
            ></customize-notifications>
          </el-tab-pane>
          <!-- /Email Notifications -->

          <!-- SMS Notifications -->
          <el-tab-pane :label="$root.labels.sms_notifications" name="sms">
            <sms-notifications
                v-if="notificationTab === 'sms'"
                :notifications="notifications"
                :customFields="options.entities.customFields"
                :coupons="options.entities.coupons"
            >
            </sms-notifications>
          </el-tab-pane>
          <!-- /SMS Notifications -->

        </el-tabs>
      </div>

      <!-- /Notifications Tab -->

      <!-- Help Button TODO - Change href based on selected tab -->
      <el-col :md="6" class="">
        <a class="am-help-button" :href="needHelpPage" target="_blank">
          <i class="el-icon-question"></i> {{ $root.labels.need_help }}?
        </a>
      </el-col>
      <!-- /Help Button -->

    </div>
  </div>
</template>

<script>
  import PageHeader from '../parts/PageHeader.vue'
  import CustomizeNotifications from './common/CustomizeNotifications.vue'
  import SmsNotifications from './sms/SmsNotifications.vue'
  import imageMixin from '../../../js/common/mixins/imageMixin'
  import { quillEditor } from 'vue-quill-editor'
  import notifyMixin from '../../../js/backend/mixins/notifyMixin'
  import durationMixin from '../../../js/common/mixins/durationMixin'
  import helperMixin from '../../../js/backend/mixins/helperMixin'

  export default {
    mixins: [imageMixin, notifyMixin, durationMixin, helperMixin],

    data () {
      return {
        fetched: false,
        notifications: [],
        notificationTab: 'email',
        options: {
          entities: {
            customFields: []
          },
          fetched: false
        }
      }
    },

    created () {
      this.setActiveTab()
      this.getEntities()
      this.inlineSVG()
    },

    mounted () {
      this.inlineSVG()
    },

    methods: {
      getEntities () {
        this.$http.get(`${this.$root.getAjaxUrl}/entities`, {
          params: {
            types: []
          }
        }).then(response => {
          this.options.entities = response.data.data
          this.options.fetched = true
          this.getNotifications()
        }).catch(e => {
          console.log(e.message)
          this.fetched = true
          this.options.fetched = true
        })
      },

      getNotifications () {
        this.fetched = false

        this.$http.get(
          `${this.$root.getAjaxUrl}/notifications`
        ).then(response => {
          this.notifications = response.data.data.notifications
          this.fetched = true
        }).catch(e => {
          console.log(e.message)
          this.fetched = true
        })
      },

      setActiveTab () {
        let urlParams = this.getUrlQueryParams(window.location.href)

        if ('notificationTab' in urlParams && urlParams.notificationTab === 'sms') {
          this.notificationTab = 'sms'
        }
      }
    },

    computed: {
      needHelpPage () {
        return this.notificationTab === 'email'
          ? 'https://wpamelia.com/notifications/' : 'https://wpamelia.com/sms-notifications/'
      }
    },

    components: {
      PageHeader,
      CustomizeNotifications,
      SmsNotifications,
      quillEditor
    }
  }
</script>
