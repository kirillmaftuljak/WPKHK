<template>
  <div>
    <div class="am-dialog-scrollable">

      <!-- Dialog Header -->
      <div class="am-dialog-header">
        <el-row>
          <el-col :span="20">
            <h2>{{ $root.labels.payments_settings }}</h2>
          </el-col>
          <el-col :span="4" class="align-right">
            <el-button @click="closeDialog" class="am-dialog-close" size="small" icon="el-icon-close"></el-button>
          </el-col>
        </el-row>
      </div>

      <!-- Form -->
      <el-form :model="settings" ref="settings" :rules="rules" label-position="top" @submit.prevent="onSubmit">

        <el-row :gutter="24">

          <!-- Currency -->
          <el-col :span="12">
            <el-form-item :label="$root.labels.currency + ':'">
              <el-select v-model="settings.currency" filterable @change="clearValidation()">
                <el-option
                    v-for="item in currencies"
                    :key="item.code"
                    :label="item.name"
                    :value="item.code"
                >
                  <span :class="'am-flag am-flag-' + item.iso">
                  </span>
                  <span class="am-payment-settings-currency-name">{{ item.name }}</span>
                  <span class="am-payment-settings-currency-symbol">{{ item.symbol }}</span>
                </el-option>
              </el-select>
            </el-form-item>
          </el-col>

          <!-- Price Symbol Position -->
          <el-col :span="12">
            <el-form-item :label="$root.labels.price_symbol_position + ':'">
              <el-select v-model="settings.priceSymbolPosition" @change="clearValidation()">
                <el-option
                    v-for="item in options.priceSymbolPositions"
                    :key="item.value"
                    :label="item.label"
                    :value="item.value">
                  <span style="float: left">{{ item.label }}</span>
                  <span style="float: right; color: #7F8BA4; font-size: 13px">{{ item.example }}</span>
                </el-option>
              </el-select>
            </el-form-item>
          </el-col>

        </el-row>

        <el-row :gutter="24">

          <!-- Price Separator -->
          <el-col :span="12">
            <el-form-item :label="$root.labels.price_separator + ':'">
              <el-select v-model="settings.priceSeparator" @change="clearValidation()">
                <el-option
                    v-for="item in options.priceSeparators"
                    :key="item.value"
                    :label="item.label"
                    :value="item.value">
                  <span style="float: left">{{ item.label }}</span>
                  <span style="float: right; color: #7F8BA4; font-size: 13px">{{ item.example }}</span>
                </el-option>
              </el-select>
            </el-form-item>
          </el-col>

          <!-- Price Number Of Decimals -->
          <el-col :span="12">
            <el-form-item :label="$root.labels.price_number_of_decimals + ':'">
              <el-input-number
                  v-model="settings.priceNumberOfDecimals"
                  :min="0"
                  :max="5"
                  @input="clearValidation()"
              >
              </el-input-number>
            </el-form-item>
          </el-col>

        </el-row>

        <!-- Default Payment Method -->
        <el-form-item
            label="placeholder" :label="$root.labels.default_payment_method + ':'"
            v-if="!settings.wc.enabled"
        >
          <el-select v-model="settings.defaultPaymentMethod">
            <el-option
                v-for="item in defaultPaymentMethods"
                :key="item.value"
                :label="item.label"
                :value="item.value"
            >
            </el-option>
          </el-select>
        </el-form-item>

        <el-alert
            v-if="hasDisabledBookablePaymentMethod"
            type="warning"
            show-icon
            title=""
            :description="$root.labels.payment_warning_settings"
            :closable="false"
        >
        </el-alert>

        <!-- Coupons -->
        <el-popover :disabled="!$root.isLite" ref="couponsPop" v-bind="$root.popLiteProps"><PopLite/></el-popover>
        <div class="am-setting-box am-switch-box" v-popover:couponsPop :class="{'am-lite-disabled': ($root.isLite)}">
          <el-row type="flex" align="middle" :gutter="24">
            <el-col :span="16">
              <p>{{ $root.labels.coupons }}</p>
            </el-col>
            <el-col :span="8" class="align-right">
              <el-switch
                  v-model="settings.coupons"
                  active-text=""
                  inactive-text=""
                  @change="clearValidation()"
                  :disabled="$root.isLite"
              >
              </el-switch>
            </el-col>
          </el-row>
        </div>

        <!-- Service Paid On-site -->
        <div class="am-setting-box am-switch-box">
            <el-row type="flex" align="middle" :gutter="24">
              <el-col :span="16">
              <p>{{ $root.labels.on_site }}</p>
              </el-col>
              <el-col :span="8" class="align-right">
                <el-switch
                    v-model="settings.onSite"
                  :disabled="(!this.settings.payPal.enabled && !this.settings.stripe.enabled && !this.settings.wc.enabled) || this.settings.wc.enabled"
                    active-text=""
                    inactive-text=""
                  @change="toggleOnSite"
                >
                </el-switch>
              </el-col>
            </el-row>
        </div>

        <!-- Service WooCommerce -->
        <el-popover :disabled="!$root.isLite" ref="wooCommercePop" v-bind="$root.popLiteProps"><PopLite/></el-popover>
        <div class="am-setting-box am-switch-box" v-popover:wooCommercePop :class="{'am-lite-disabled': ($root.isLite)}">
          <el-row type="flex" align="middle" :gutter="24">
            <el-col :span="16">
              <img id="am-woocommerce" class="svg" :src="this.$root.getUrl + 'public/img/payments/woocommerce.svg'">
            </el-col>
            <el-col :span="8" class="align-right">
              <el-switch
                  v-model="settings.wc.enabled"
                  active-text=""
                  inactive-text=""
                  @change="toggleWooCommerce"
                  :disabled="$root.isLite"
              >
              </el-switch>
            </el-col>
          </el-row>
        </div>

        <!-- PayPal -->
        <el-popover :disabled="!$root.isLite" ref="payPalPop" v-bind="$root.popLiteProps"><PopLite/></el-popover>
        <el-collapse v-model="payPalCollapse" v-popover:payPalPop :class="{'am-lite-disabled': ($root.isLite)}">
          <el-collapse-item class="am-setting-box" name="payPal">
            <!-- PayPal Title -->
            <template slot="title">
              <img class="svg" width="60px" :src="this.$root.getUrl + 'public/img/payments/paypal-light.svg'">
              <i v-show="settings.payPal.enabled" class="el-icon-circle-check"></i>
            </template>

            <!-- PayPal Toggle -->
            <el-row type="flex" align="middle" :gutter="24">
              <el-col :span="16">
                <p>{{ $root.labels.payPal_service }}:</p>
              </el-col>
              <el-col :span="8" class="align-right">
                <el-switch
                    v-model="settings.payPal.enabled"
                    @change="togglePayPal"
                    active-text=""
                    inactive-text=""
                    :disabled="this.settings.wc.enabled || $root.isLite"
                >
                </el-switch>
              </el-col>
            </el-row>

            <!-- PayPal Test Mode -->
            <el-row type="flex" align="middle" :gutter="24" v-show="settings.payPal.enabled === true">
              <el-col :span="16">
                <p>{{ $root.labels.sandbox_mode }}:</p>
              </el-col>
              <el-col :span="8" class="align-right">
                <el-switch
                    v-model="settings.payPal.sandboxMode"
                    @change="handlePayPalValidationRules"
                    active-text=""
                    inactive-text=""
                >
                </el-switch>
              </el-col>
            </el-row>

            <!-- PayPal Live Client ID -->
            <el-form-item
                :label="$root.labels.live_client_id + ':'"
                prop="payPal.liveApiClientId"
                v-show="settings.payPal.enabled === true && settings.payPal.sandboxMode === false"
            >
              <el-input v-model.trim="settings.payPal.liveApiClientId" auto-complete="off"></el-input>
            </el-form-item>

            <!-- PayPal Live Secret -->
            <el-form-item
                :label="$root.labels.live_secret + ':'"
                prop="payPal.liveApiSecret"
                v-show="settings.payPal.enabled === true && settings.payPal.sandboxMode === false"
            >
              <el-input v-model.trim="settings.payPal.liveApiSecret" auto-complete="off"></el-input>
            </el-form-item>

            <!-- PayPal Test Client ID -->
            <el-form-item
                :label="$root.labels.test_client_id + ':'"
                prop="payPal.testApiClientId"
                v-show="settings.payPal.enabled === true && settings.payPal.sandboxMode === true"
            >
              <el-input v-model.trim="settings.payPal.testApiClientId" auto-complete="off"></el-input>
            </el-form-item>

            <!-- PayPal Test Secret -->
            <el-form-item
                :label="$root.labels.test_secret + ':'"
                prop="payPal.testApiSecret"
                v-show="settings.payPal.enabled === true && settings.payPal.sandboxMode === true"
            >
              <el-input v-model.trim="settings.payPal.testApiSecret" auto-complete="off"></el-input>
            </el-form-item>

          </el-collapse-item>
        </el-collapse>

        <!-- Stripe -->
        <el-popover :disabled="!$root.isLite" ref="stripePop" v-bind="$root.popLiteProps"><PopLite/></el-popover>
        <el-collapse v-model="stripeCollapse" v-popover:stripePop :class="{'am-lite-disabled': ($root.isLite)}">
          <el-collapse-item class="am-setting-box" name="stripe">

            <el-alert
                v-if="showStripeAlert"
                type="warning"
                show-icon
                title=""
                :description="$root.labels.stripe_ssl_warning"
                :closable="false"
            >
            </el-alert>

            <!-- Stripe Title -->
            <template slot="title">
              <img class="svg" width="40px" :src="this.$root.getUrl + 'public/img/payments/stripe.svg'">
              <i v-show="settings.stripe.enabled" class="el-icon-circle-check"></i>
            </template>

            <!-- Stripe Toggle -->
            <el-row type="flex" align="middle" :gutter="24">
              <el-col :span="16">
                <p>{{ $root.labels.stripe_service }}:</p>
              </el-col>
              <el-col :span="8" class="align-right">
                <el-switch
                    v-model="settings.stripe.enabled"
                    @change="toggleStripe"
                    active-text=""
                    inactive-text=""
                    :disabled="this.settings.wc.enabled || $root.isLite"
                >
                </el-switch>
              </el-col>
            </el-row>

            <!-- Stripe Test Mode -->
            <el-row type="flex" align="middle" :gutter="24" v-show="settings.stripe.enabled === true">
              <el-col :span="16">
                <p>{{ $root.labels.test_mode }}:</p>
              </el-col>
              <el-col :span="8" class="align-right">
                <el-switch
                    v-model="settings.stripe.testMode"
                    @change="handleStripeValidationRules"
                    active-text=""
                    inactive-text=""
                >
                </el-switch>
              </el-col>
            </el-row>

            <!-- Stripe Live Publishable Key -->
            <el-form-item
                :label="$root.labels.live_publishable_key + ':'"
                prop="stripe.livePublishableKey"
                v-show="settings.stripe.enabled === true && settings.stripe.testMode === false"
            >
              <el-input v-model.trim="settings.stripe.livePublishableKey" auto-complete="off"></el-input>
            </el-form-item>

            <!-- Stripe Live Secret Key -->
            <el-form-item
                :label="$root.labels.live_secret_key + ':'"
                prop="stripe.liveSecretKey"
                v-show="settings.stripe.enabled === true && settings.stripe.testMode === false"
            >
              <el-input v-model.trim="settings.stripe.liveSecretKey" auto-complete="off"></el-input>
            </el-form-item>

            <!-- Stripe Test Publishable Key -->
            <el-form-item
                :label="$root.labels.test_publishable_key + ':'"
                prop="stripe.testPublishableKey"
                v-show="settings.stripe.enabled === true && settings.stripe.testMode === true"
            >
              <el-input v-model.trim="settings.stripe.testPublishableKey" auto-complete="off"></el-input>
            </el-form-item>

            <!-- Stripe Test Secret Key -->
            <el-form-item
                :label="$root.labels.test_secret_key + ':'"
                prop="stripe.testSecretKey"
                v-show="settings.stripe.enabled === true && settings.stripe.testMode === true"
            >
              <el-input v-model.trim="settings.stripe.testSecretKey" auto-complete="off"></el-input>
            </el-form-item>

          </el-collapse-item>
        </el-collapse>

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
  import priceMixin from '../../../js/common/mixins/priceMixin'

  export default {

    mixins: [imageMixin, priceMixin],

    props: {
      payments: {
        type: Object
      }
    },

    data () {
      return {
        settings: Object.assign({}, this.payments),
        options: {
          priceSeparators: [
            {
              label: this.$root.labels.comma_dot,
              value: 1,
              example: '15,000.00'
            },
            {
              label: this.$root.labels.dot_comma,
              value: 2,
              example: '15.000,00'
            },
            {
              label: this.$root.labels.space_dot,
              value: 3,
              example: '15 000.00'
            },
            {
              label: this.$root.labels.space_comma,
              value: 4,
              example: '15 000,00'
            }
          ],
          priceSymbolPositions: [
            {
              label: this.$root.labels.before,
              value: 'before',
              example: '$100'
            },
            {
              label: this.$root.labels.before_with_space,
              value: 'beforeWithSpace',
              example: '$ 100'
            },
            {
              label: this.$root.labels.after,
              value: 'after',
              example: '100$'
            },
            {
              label: this.$root.labels.after_with_space,
              value: 'afterWithSpace',
              example: '100 $'
            }
          ],
          sandboxMode: [
            {
              label: this.$root.labels.disabled,
              value: false
            },
            {
              label: this.$root.labels.enabled,
              value: true
            }
          ]
        },
        hasDisabledBookablePaymentMethod: false,
        rules: {
          stripe: {},
          payPal: {}
        },
        stripeCollapse: '',
        payPalCollapse: ''
      }
    },

    mounted () {
      this.inspectBookableSettingsPayments()
      this.handleStripeValidationRules()
      this.handlePayPalValidationRules()

      // Fallback for users that don't have enabled "On-site" option enabled. Remove in future versions.
      let paymentOption = this.defaultPaymentMethods.find(option => option.value === this.settings.defaultPaymentMethod)
      this.settings.defaultPaymentMethod = paymentOption ? paymentOption.value : this.defaultPaymentMethods[0].value
    },

    methods: {
      inspectBookableSettingsPayments () {
        if (this.settings.wc.enabled) {
          return
        }

        this.$http.get(`${this.$root.getAjaxUrl}/entities`, {
          params: {
            types: ['categories', 'events']
          }
        })
          .then(response => {
            let hasDisabledBookablePaymentMethod = false

            let $this = this

            response.data.data.categories.forEach(function (category) {
              category.serviceList.forEach(function (service) {
                let serviceSettings = JSON.parse(service.settings)

                if (!serviceSettings || (serviceSettings && !('payments' in serviceSettings))) {
                  return
                }

                let enabledOnSite = $this.settings.onSite
                let enabledPayPal = $this.settings.payPal.enabled
                let enabledStripe = $this.settings.stripe.enabled

                if (enabledOnSite && 'onSite' in serviceSettings.payments && !serviceSettings.payments.onSite) {
                  enabledOnSite = false
                }

                if (enabledPayPal && 'payPal' in serviceSettings.payments && !serviceSettings.payments.payPal.enabled) {
                  enabledPayPal = false
                }

                if (enabledStripe && 'stripe' in serviceSettings.payments && !serviceSettings.payments.stripe.enabled) {
                  enabledStripe = false
                }

                if (!enabledOnSite && !enabledPayPal && !enabledStripe) {
                  hasDisabledBookablePaymentMethod = true
                }
              })
            })

            response.data.data.events.forEach(function (event) {
              let eventSettings = JSON.parse(event.settings)

              if (!eventSettings || (eventSettings && !('payments' in eventSettings))) {
                return
              }

              let enabledOnSite = $this.settings.onSite
              let enabledPayPal = $this.settings.payPal.enabled
              let enabledStripe = $this.settings.stripe.enabled

              if (enabledOnSite && 'onSite' in eventSettings.payments && !eventSettings.payments.onSite) {
                enabledOnSite = false
              }

              if (enabledPayPal && 'payPal' in eventSettings.payments && !eventSettings.payments.payPal.enabled) {
                enabledPayPal = false
              }

              if (enabledStripe && 'stripe' in eventSettings.payments && !eventSettings.payments.stripe.enabled) {
                enabledStripe = false
              }

              if (!enabledOnSite && !enabledPayPal && !enabledStripe) {
                hasDisabledBookablePaymentMethod = true
              }
            })

            this.hasDisabledBookablePaymentMethod = hasDisabledBookablePaymentMethod
          })
          .catch(e => {
            console.log(e.message)
          })
      },

      closeDialog () {
        this.$emit('closeDialogSettingsPayments')
      },

      onSubmit () {
        this.$refs.settings.validate((valid) => {
          if (valid) {
            this.$emit('closeDialogSettingsPayments')
            this.$emit('updateSettings', {'payments': this.settings})
          } else {
            if (this.settings.stripe.enabled) {
              if (!this.settings.stripe.testMode && (this.settings.stripe.livePublishableKey === '' || this.settings.stripe.liveSecretKey === '')) {
                this.stripeCollapse = 'stripe'
              }

              if (this.settings.stripe.testMode && (this.settings.stripe.testPublishableKey === '' || this.settings.stripe.testSecretKey === '')) {
                this.stripeCollapse = 'stripe'
              }
            }

            if (this.settings.payPal.enabled) {
              if (!this.settings.payPal.sandboxMode && (this.settings.payPal.liveApiClientId === '' || this.settings.payPal.liveApiSecret === '')) {
                this.payPalCollapse = 'payPal'
              }

              if (this.settings.payPal.sandboxMode && (this.settings.payPal.testApiClientId === '' || this.settings.payPal.testApiSecret === '')) {
                this.payPalCollapse = 'payPal'
              }
            }

            return false
          }
        })
      },

      checkOnSitePayment () {
        if (this.settings.payPal.enabled === false && this.settings.stripe.enabled === false && this.settings.wc.enabled === false) {
          this.settings.onSite = true
        }
      },

      toggleOnSite () {
        this.inspectBookableSettingsPayments()
        this.clearValidation()
        if (this.settings.defaultPaymentMethod === 'onSite' && this.settings.onSite === false) {
          this.settings.defaultPaymentMethod = this.defaultPaymentMethods[0].value
        }
      },

      toggleStripe () {
        this.inspectBookableSettingsPayments()
        this.checkOnSitePayment()
        this.handleStripeValidationRules()

        if (this.settings.stripe.enabled === false) {
          this.stripeCollapse = ''
        }

        if (this.settings.defaultPaymentMethod === 'stripe' && this.settings.stripe.enabled === false) {
          this.settings.defaultPaymentMethod = this.defaultPaymentMethods[0].value
        }
      },

      togglePayPal () {
        this.inspectBookableSettingsPayments()
        this.checkOnSitePayment()
        this.handlePayPalValidationRules()

        if (this.settings.payPal.enabled === false) {
          this.payPalCollapse = ''
        }

        if (this.settings.defaultPaymentMethod === 'payPal' && this.settings.payPal.enabled === false) {
          this.settings.defaultPaymentMethod = this.defaultPaymentMethods[0].value
        }
      },

      toggleWooCommerce () {
        this.settings.onSite = !this.settings.wc.enabled
        this.settings.stripe.enabled = false
        this.settings.payPal.enabled = false
        if (this.settings.defaultPaymentMethod === 'wc' && this.settings.wc.enabled === false) {
          this.settings.defaultPaymentMethod = this.defaultPaymentMethods[0].value
        }
      },

      handleStripeValidationRules () {
        this.clearValidation()
        if (this.settings.stripe.enabled === true) {
          if (this.settings.stripe.testMode === true) {
            this.rules.stripe = {
              testPublishableKey: [
                {required: true, message: this.$root.labels.stripe_test_publishable_key_error, trigger: 'submit'}
              ],
              testSecretKey: [
                {required: true, message: this.$root.labels.stripe_test_secret_key_error, trigger: 'submit'}
              ]
            }
          } else {
            this.rules.stripe = {
              livePublishableKey: [
                {required: true, message: this.$root.labels.stripe_live_publishable_key_error, trigger: 'submit'}
              ],
              liveSecretKey: [
                {required: true, message: this.$root.labels.stripe_live_secret_key_error, trigger: 'submit'}
              ]
            }
          }
        } else {
          this.rules.stripe = {}
        }
      },

      handlePayPalValidationRules () {
        this.clearValidation()
        if (this.settings.payPal.enabled === true) {
          if (this.settings.payPal.sandboxMode === true) {
            this.rules.payPal = {
              testApiClientId: [
                {required: true, message: this.$root.labels.payPal_test_client_id_error, trigger: 'submit'}
              ],
              testApiSecret: [
                {required: true, message: this.$root.labels.payPal_test_secret_error, trigger: 'submit'}
              ]
            }
          } else {
            this.rules.payPal = {
              liveApiClientId: [
                {required: true, message: this.$root.labels.payPal_live_client_id_error, trigger: 'submit'}
              ],
              liveApiSecret: [
                {required: true, message: this.$root.labels.payPal_live_secret_error, trigger: 'submit'}
              ]
            }
          }
        } else {
          this.rules.payPal = {}
        }
      },

      clearValidation () {
        if (typeof this.$refs.settings !== 'undefined') {
          this.$refs.settings.clearValidate()
        }
      }
    },

    computed: {
      showStripeAlert () {
        return location.protocol !== 'https:'
      },

      defaultPaymentMethods () {
        let methods = []

        if (this.settings.onSite) {
          methods.push({
            label: this.$root.labels.on_site,
            value: 'onSite'
          })
        }

        if (this.settings.payPal.enabled) {
          methods.push({
            label: this.$root.labels.payPal,
            value: 'payPal'
          })
        }

        if (this.settings.stripe.enabled) {
          methods.push({
            label: this.$root.labels.stripe,
            value: 'stripe'
          })
        }

        if (this.settings.wc.enabled) {
          methods.push({
            label: this.$root.labels.wc,
            value: 'wc'
          })
        }

        return methods
      }
    },

    watch: {
      'settings.currency' () {
        this.settings.symbol = this.currencies.find(currency => currency.code === this.settings.currency).symbol
      }
    },

    components: {}

  }
</script>