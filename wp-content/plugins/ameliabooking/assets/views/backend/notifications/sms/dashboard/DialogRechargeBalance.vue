<template>
  <div>
    <div class="am-dialog-scrollable">

      <!-- Dialog Header -->
      <div class="am-dialog-header">
        <el-row>
          <el-col :span="14">
            <h2>{{ $root.labels.recharge_balance }}</h2>
          </el-col>
          <el-col :span="10" class="align-right">
            <el-button @click="closeDialog" class="am-dialog-close" size="small" icon="el-icon-close"></el-button>
          </el-col>
        </el-row>
      </div>
      <!-- /Dialog Header -->

      <!-- Dialog Body -->
      <div class="am-sms-money">

        <!-- 10$, 20$, 50$ -->
        <el-row :gutter="16">

          <!-- 10$ -->
          <el-col :sm="8">
            <div class="am-sms-money-card" @click="changeAmount(10)"
                 :class="[ paymentCheckout.amount === 10 && customAmountEnabled === false ? 'active' : '' ]">
              $10
            </div>
          </el-col>
          <!-- /10$ -->

          <!-- 20$ -->
          <el-col :sm="8">
            <div class="am-sms-money-card" @click="changeAmount(20)"
                 :class="[ paymentCheckout.amount === 20 && customAmountEnabled === false ? 'active' : '' ]">
              $20
            </div>
          </el-col>
          <!-- /20$ -->

          <!-- 50$ -->
          <el-col :sm="8">
            <div class="am-sms-money-card" @click="changeAmount(50)"
                 :class="[ paymentCheckout.amount === 50 && customAmountEnabled === false ? 'active' : '' ]">
              $50
            </div>
          </el-col>
          <!-- /50$ -->

        </el-row>
        <!-- /10$, 20$, 50$ -->

        <!-- Recharge Custom Amount -->
        <div>

          <!-- Recharge Custom Amount Checkbox -->
          <el-checkbox v-model="customAmountEnabled">{{ $root.labels.recharge_custom_amount }}</el-checkbox>
          <!-- /Recharge Custom Amount Checkbox -->

          <!-- Recharge Custom Amount Input -->
          <transition name="fade">
            <div class="am-sms-custom-amount" v-if="customAmountEnabled">
              <el-input-number :min="minAmount" v-model="paymentCheckout.amount"></el-input-number>
            </div>
          </transition>
          <!-- /Recharge Custom Amount Input -->

        </div>
        <!-- /Recharge Custom Amount -->

        <!-- Total Price -->
        <span class="am-sms-money-total-price">
          {{ $root.labels.total_price_colon }} ${{ getFormattedMessagePrice(paymentCheckout.amount + paymentCheckout.amount * 0.029 + 0.3) }} ({{ $root.labels.pay_pal_fee }} 2.9% + $0.30)
        </span>
        <!-- /Total Price -->

        <!-- Recharge Balance Button -->
        <el-button @click="onCheckoutClick" type="primary" :loading="paymentCheckoutLoading">
          {{ $root.labels.recharge }}
        </el-button>
        <!-- /Recharge Balance Button -->

      </div>
      <!-- /Dialog Body -->


    </div>
  </div>
</template>

<script>
  import imageMixin from '../../../../../js/common/mixins/imageMixin'
  import ElButton from 'element-ui/packages/button/src/button.vue'
  import notificationMixin from '../../../../../js/backend/mixins/notificationMixin'
  import notifyMixin from '../../../../../js/backend/mixins/notifyMixin'

  export default {
    mixins: [imageMixin, notificationMixin, notifyMixin],

    data () {
      return {
        customAmountEnabled: false,
        minAmount: 10,
        paymentCheckout: {
          amount: 10
        },
        paymentCheckoutLoading: false
      }
    },

    mounted () {
      this.inlineSVG()
    },

    methods: {
      closeDialog () {
        this.$emit('closeDialogRechargeBalance')
      },

      changeAmount (amount) {
        this.customAmountEnabled = false
        this.paymentCheckout.amount = amount
      },

      onCheckoutClick () {
        this.paymentCheckoutLoading = true
        this.sendAmeliaSmsApiRequest('paymentCheckout', this.onCheckoutSuccess, this.onCheckoutError)
      },

      onCheckoutSuccess (data) {
        window.location.href = data.redirectUrl
      },

      onCheckoutError () {
        this.notify(this.$root.labels.error, this.$root.labels.cant_checkout, 'error')
        this.paymentCheckoutLoading = false
      }
    },

    components: {
      ElButton
    }
  }
</script>