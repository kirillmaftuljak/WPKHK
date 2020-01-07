<template>
  <div :class="{'am-dialog-create': true, 'am-lite-disabled': $root.isLite}" :disabled=$root.isLite>
    <p>{{ $root.labels.payments_settings }}
      <el-tooltip placement="top" v-if="$root.settings.payments.onSite || $root.settings.payments.payPal.enabled || $root.settings.payments.stripe.enabled">
        <div slot="content" v-html="$root.labels.payment_tooltip"></div>
        <i class="el-icon-question am-tooltip-icon"></i>
      </el-tooltip>
    </p>

    <!-- Service Paid On-site -->
    <div class="am-setting-box am-switch-box" v-if="$root.settings.payments.onSite">
      <el-row type="flex" align="middle" :gutter="24">
        <el-col :span="16">
          {{ $root.labels.on_site }}
        </el-col>
        <el-col :span="8" class="align-right">
          <el-switch
              v-model="bookablePayments.onSite"
              active-text=""
              inactive-text=""
          >
          </el-switch>
        </el-col>
      </el-row>
    </div>

    <el-col :span="24" v-if="$root.settings.payments.wc.enabled">
      <el-form-item label="placeholder">
        <label slot="label">
          {{ $root.labels.wc_product }}:
          <el-tooltip placement="top">
            <div slot="content" v-html="$root.labels.wc_product_tooltip"></div>
            <i class="el-icon-question am-tooltip-icon"></i>
          </el-tooltip>
        </label>
        <el-select
            v-model="bookablePayments.wc.productId"
            placeholder=""
        >
          <el-option
              v-for="item in settings.payments.wc"
              :key="item.id"
              :label="item.name"
              :value="item.id"
          >
          </el-option>
        </el-select>
      </el-form-item>
    </el-col>

    <!-- Service Paid PayPal -->
    <div class="am-setting-box am-switch-box" v-if="$root.settings.payments.payPal.enabled">
      <el-row type="flex" align="middle" :gutter="24">
        <el-col :span="16">
          <img class="svg" width="60px" :src="this.$root.getUrl + 'public/img/payments/paypal-light.svg'">
        </el-col>
        <el-col :span="8" class="align-right">
          <el-switch
              v-model="bookablePayments.payPal.enabled"
              active-text=""
              inactive-text=""
          >
          </el-switch>
        </el-col>
      </el-row>
    </div>

    <!-- Service Paid Stripe -->
    <div class="am-setting-box am-switch-box" v-if="$root.settings.payments.stripe.enabled">
      <el-row type="flex" align="middle" :gutter="24">
        <el-col :span="16">
          <img class="svg" width="40px" :src="this.$root.getUrl + 'public/img/payments/stripe.svg'">
        </el-col>
        <el-col :span="8" class="align-right">
          <el-switch
              v-model="bookablePayments.stripe.enabled"
              active-text=""
              inactive-text=""
          >
          </el-switch>
        </el-col>
      </el-row>
    </div>

    <el-alert
        v-if="!$root.settings.payments.wc.enabled &&
                (!bookablePayments.onSite || (!$root.settings.payments.onSite && bookablePayments.onSite)) &&
                (!bookablePayments.payPal.enabled || (!$root.settings.payments.payPal.enabled && bookablePayments.payPal.enabled)) &&
                (!bookablePayments.stripe.enabled || (!$root.settings.payments.stripe.enabled && bookablePayments.stripe.enabled))"
        type="warning"
        show-icon
        title=""
        :description="$root.labels.payment_warning"
        :closable="false"
    >
    </el-alert>
  </div>
</template>

<script>
  export default {

    props: {
      bookablePayments: null,
      settings: null
    },

    data () {
      return {
      }
    },

    mounted () {
    }

  }
</script>