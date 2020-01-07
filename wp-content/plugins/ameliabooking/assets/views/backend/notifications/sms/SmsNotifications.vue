<template>
  <div id="am-sms-notifications" class="am-body">

    <!-- SMS Authorization -->
    <authorization
        v-if="$root.settings.notifications.smsSignedIn === false"
    >
    </authorization>

    <!-- SMS Dashboard -->
    <dashboard
        v-else
        :notifications="notifications"
        :customFields="customFields"
        :coupons="coupons"
    >
    </dashboard>

  </div>
</template>

<script>
  import Authorization from './auth/Authorization.vue'
  import Dashboard from './dashboard/Dashboard.vue'

  export default {
    props: {
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
      }
    },

    components: {
      Authorization,
      Dashboard
    }
  }
</script>
