<template>
  <div id="am-confirm-booking" :class="dialogClass" class="am-confirmation-booking">

    <!-- Confirm Booking Form -->
    <div v-show="fetched">

      <!-- Header Error-->
      <div class="am-payment-error">
        <el-alert
            :title="headerErrorMessage !== '' ? headerErrorMessage : $root.labels.payment_error"
            type="warning"
            v-show="headerErrorShow"
            show-icon
        >
        </el-alert>
      </div>
      <!-- /Header Error-->

      <!-- Confirm Dialog Header -->
      <div class="am-confirmation-booking-header" v-show="fetched" v-if="bookableType === 'appointment'">
        <img :src="pictureLoad(bookable, false)" @error="imageLoadError(bookable, false)" :alt="bookable.name"/>
        <h2>{{ bookable.name }}</h2>
      </div>
      <!-- /Confirm Dialog Header -->

      <!-- Confirm Dialog Body -->
      <el-form
          :model="appointment.bookings[0]"
          ref="booking"
          :rules="rules"
          label-position="top"
          @submit.prevent="onSubmit"
          class="am-confirm-booking-form"
      >
        <el-row class="am-confirm-booking-data" :gutter="24">

          <!-- Booking Data -->
          <el-col :sm="24">
            <div class="am-confirmation-booking-details" v-if="bookableType === 'appointment'">

              <!-- Employee -->
              <div>
                <p>{{ capitalizeFirstLetter($root.labels.employee) }}:</p>
                <p class="am-semi-strong">
                  <img
                      class="am-employee-photo"
                      :src="pictureLoad(provider, true)"
                      @error="imageLoadError(provider, true)"
                      alt="provider.firstName + ' ' + provider.lastName"
                  />
                  {{ provider.firstName + ' ' + provider.lastName }}
                </p>
              </div>
              <!-- /Employee -->

              <!-- Date -->
              <div>
                <p>{{ $root.labels.date_colon }}</p>
                <p class="am-semi-strong">
                  {{ getAppointmentDate() }}
                </p>
              </div>
              <!-- /Date -->

              <!-- Time -->
              <div>
                <p>{{ $root.labels.time_colon }}</p>
                <p class="am-semi-strong">
                  {{ getAppointmentTime() }}
                </p>
              </div>
              <!-- /Time -->

              <!-- Location -->
              <div>
                <p v-if="location !== null">{{ $root.labels.location_colon }}</p>
                <p class="am-semi-strong">{{ location ? location.name : '' }}</p>
              </div>
              <!-- /Location -->

            </div>
          </el-col>
          <!-- /Booking Data -->

          <!-- Customer First Name -->
          <el-col :sm="columnsLg">
            <el-form-item :label="$root.labels.first_name_colon" prop="customer.firstName">
              <el-input
                  v-model="appointment.bookings[0].customer.firstName"
                  @keyup.native="validateFieldsForPayPal"
                  @input="clearValidation()"
                  :disabled="!!appointment.bookings[0].customer.firstName && !!appointment.bookings[0].customer.id"
                  autocomplete="new-password"
              >
              </el-input>
            </el-form-item>
          </el-col>
          <!-- /Customer First Name -->

          <!-- Customer Last Name -->
          <el-col :sm="columnsLg">
            <el-form-item :label="$root.labels.last_name_colon" prop="customer.lastName">
              <el-input
                  v-model="appointment.bookings[0].customer.lastName"
                  @keyup.native="validateFieldsForPayPal"
                  @input="clearValidation()"
                  :disabled="!!appointment.bookings[0].customer.lastName && !!appointment.bookings[0].customer.id"
                  autocomplete="new-password"
              >
              </el-input>
            </el-form-item>
          </el-col>
          <!-- /Customer Last Name -->

          <!-- Customer Email -->
          <el-col :sm="columnsLg">
            <el-form-item :label="$root.labels.email_colon" prop="customer.email" :error="errors.email">
              <el-input
                  v-model="appointment.bookings[0].customer.email"
                  @keyup.native="validateFieldsForPayPal"
                  @input="clearValidation()"
                  :disabled="!!appointment.bookings[0].customer.email && !!appointment.bookings[0].customer.id"
                  :placeholder="$root.labels.email_placeholder"
                  autocomplete="new-password"
              >
              </el-input>
            </el-form-item>
          </el-col>
          <!-- /Customer Email -->

          <!-- User Phone -->
          <el-col :sm="columnsLg">
            <el-form-item :label="$root.labels.phone_colon" prop="customer.phone" :error="errors.phone">
              <phone-input
                  :savedPhone="appointment.bookings[0].customer.phone"
                  :disabled="!!appointment.bookings[0].customer.id"
                  @keyup.native="validateFieldsForPayPal"
                  v-on:phoneFormatted="phoneFormatted"
              >
              </phone-input>
            </el-form-item>
          </el-col>
          <!-- /User Phone -->

          <!-- Custom Fields -->
          <div class="am-custom-fields" v-if="customFields.length">
            <el-row :gutter="24">
              <el-col
                  :sm="columnsLg"
                  v-for="customField, key in customFields"
                  :key="customField.id"
                  v-if="isCustomFieldVisible(customField, bookableType, bookable.id)"
              >
                <el-form-item
                    :label="customField.type !== 'content' ? customField.label + ':' : ''"
                    :prop="customField.required === true && customField.type !== 'content' && customField.type !== 'file' ? 'customFields.' + customField.id + '.value' : (customField.required === true && customField.type !== 'file' ? 'inputFile' : null)"
                    :error="errors.files && customField.type === 'file' && customField.required ? errors.files['files' + customField.id] : null"
                    :class="{'is-required': customField.type === 'file' && customField.required}"
                >

                  <!-- Text Field -->
                  <el-input
                      v-if="customField.type === 'text'"
                      placeholder=""
                      v-model="appointment.bookings[0].customFields[customField.id].value"
                      @input="clearValidation()"
                  >
                  </el-input>
                  <!-- /Text Field -->

                  <!-- Text Area -->
                  <el-input
                      v-else-if="customField.type === 'text-area'"
                      class="am-front-texarea"
                      placeholder=""
                      v-model="appointment.bookings[0].customFields[customField.id].value"
                      type="textarea"
                      :rows="3"
                      @input="clearValidation()"
                  >
                  </el-input>
                  <!-- /Text Area -->

                  <!-- Text Content -->
                  <div v-else-if="customField.type === 'content'" class="am-text-content">
                    <i class="el-icon-info"></i>
                    <p style='display: inline;' v-html="customField.label"></p>
                  </div>
                  <!-- /Text Content -->

                  <!-- Selectbox -->
                  <el-select
                      v-else-if="customField.type === 'select'"
                      placeholder=""
                      v-model="appointment.bookings[0].customFields[customField.id].value"
                      clearable
                      @change="clearValidation()"
                  >
                    <el-option
                        v-for="(option, index) in getCustomFieldOptions(customField.options)"
                        :key="index"
                        :value="option"
                        :label="option"
                    >
                    </el-option>
                  </el-select>
                  <!-- /Selectbox -->

                  <!-- Checkbox -->
                  <el-checkbox-group
                      v-else-if="customField.type === 'checkbox'"
                      v-model="appointment.bookings[0].customFields[customField.id].value"
                      @change="clearValidation()"
                  >
                    <el-checkbox
                        v-for="(option, index) in getCustomFieldOptions(customField.options)"
                        :key="index"
                        :label="option"
                    >
                    </el-checkbox>
                  </el-checkbox-group>
                  <!-- /Checkbox -->

                  <!-- Radio Buttons -->
                  <el-radio-group
                      v-else-if="customField.type === 'radio'"
                      v-model="appointment.bookings[0].customFields[customField.id].value"
                      @change="clearValidation()">
                    <el-radio
                        v-for="(option, index) in getCustomFieldOptions(customField.options)"
                        :key="index"
                        :label="option"
                        ref="customFieldsRadioButtons"
                    >
                    </el-radio>
                  </el-radio-group>
                  <!-- /Radio Buttons -->

                  <!-- Upload Files -->
                  <div v-if="customField.type === 'file'">
                    <el-upload
                        :auto-upload="false"
                        action=""
                        drag
                        ref="customFieldsFiles"
                        :accept="$root.fileUploadExtensions.join(',')"
                        :on-change="onSelectFiles"
                        multiple>
                      <i class="el-icon-upload"></i><span>{{$root.labels.file_upload}}</span>
                    </el-upload>
                  </div>
                  <!-- /Upload Files -->

                </el-form-item>
              </el-col>
            </el-row>
          </div>
          <!-- /Custom Fields -->

        </el-row>

        <!-- Payment Method & Stripe Card -->
        <el-row :gutter="24" class="am-confirm-booking-payment">

          <!-- Payment Method -->
          <el-col :sm="columnsLg" v-show="paymentOptions.length > 1">
            <transition name="fade">
              <el-form-item
                  :label="$root.labels.payment_method_colon"
                  v-if="getTotalPrice() > 0 && !this.$root.settings.payments.wc.enabled"
              >
                <el-select
                    v-model="appointment.payment.gateway"
                    placeholder=""
                    :disabled="paymentOptions.length === 1"
                    @change="clearValidation()"
                >
                  <el-option
                      v-for="item in paymentOptions"
                      :key="item.value"
                      :label="item.label"
                      :value="item.value"
                  >
                  </el-option>
                </el-select>
              </el-form-item>
            </transition>
          </el-col>
          <!-- /Payment Method -->

          <!-- Stripe Card -->
          <el-col :sm="columnsLg">
            <transition name="fade">
              <el-form-item
                  :label="$root.labels.credit_or_debit_card_colon"
                  v-show="appointment.payment.gateway === 'stripe' && getTotalPrice() > 0"
                  :error="errors.stripe"
              >
                <div :id="'card-element-' + this.$root.shortcodeData.counter"></div>
              </el-form-item>
            </transition>
          </el-col>
          <!-- /Stripe Card -->

        </el-row>
        <!-- /Payment Method & Stripe Card -->

        <!-- Appointment Data -->
        <el-row>
          <el-col :sm="24">

            <!-- Payment Data -->
            <div class="am-confirmation-booking-cost">

              <!-- Number Of Persons -->
              <el-row :gutter="24" v-if="bookable.maxCapacity > 1">
                <el-col :span="12">
                  <p>{{ $root.labels.total_number_of_persons }}</p>
                </el-col>
                <el-col :span="12">
                  <p class="am-semi-strong am-align-right">
                    {{ appointment.bookings[0].persons }}
                  </p>
                </el-col>
              </el-row>
              <!-- /Number Of Persons -->

              <!-- Appointment Price -->
              <el-row :gutter="24" v-if="bookable.price">
                <el-col :span="8">
                  <p>{{ $root.labels.base_price_colon }}</p>
                </el-col>
                <el-col :span="16">
                  <p class="am-semi-strong am-align-right">
                    {{ getBookingPrice() }}
                  </p>
                </el-col>
              </el-row>
              <!-- /Appointment Price -->

              <!-- Extras Price -->
              <el-row
                  class="am-confirmation-extras-cost" :gutter="24"
                  v-if="appointment.bookings[0].extras.length > 0 && getTotalPrice() > 0"
              >
                <el-collapse accordion v-if="selectedExtras.length > 0">
                  <el-collapse-item name="1">
                    <template slot="title">
                      <div class="am-extras-title">{{ $root.labels.extras_costs_colon }}</div>
                      <div class="am-extras-total-cost am-semi-strong" :style="bookableType === 'event' ? getBookableColor : {}">{{ getFormattedPrice(getExtrasPrice()) }}</div>
                    </template>
                    <div v-for="extra in selectedExtras">
                      <div class="am-extras-details"> {{ getSelectedExtraDetails(extra) }}</div>
                      <div class="am-extras-cost">{{ getSelectedExtraPrice(extra) }}</div>
                    </div>
                  </el-collapse-item>
                </el-collapse>
                <div v-else>
                  <el-col :span="12">
                    <p>{{ $root.labels.extras_costs_colon }}</p>
                  </el-col>
                  <el-col :span="12">
                    <p class="am-semi-strong am-align-right">{{ getFormattedPrice(getExtrasPrice()) }}</p>
                  </el-col>
                </div>
              </el-row>
              <!-- /Extras Price -->

              <!-- Subtotal Price -->
              <el-row :gutter="24" v-if="appointment.bookings[0].extras.length > 0 && bookable.price">
                <el-col :span="8">
                  <p>{{ $root.labels.subtotal_colon }}</p>
                </el-col>
                <el-col :span="16">
                  <p class="am-semi-strong am-align-right">
                    {{ getFormattedPrice(getSubtotalPrice()) }}
                  </p>
                </el-col>
              </el-row>
              <!-- /Subtotal Price -->

              <!-- Discount Price -->
              <el-row :gutter="24" v-if="$root.settings.payments.coupons && bookable.price > 0">
                <el-col :span="8">
                  <p>{{ $root.labels.discount_amount_colon }}</p>
                </el-col>
                <el-col :span="16">
                  <p class="am-semi-strong am-align-right">
                    {{ getFormattedPrice(getDiscountPrice()) }}
                  </p>
                </el-col>
              </el-row>
              <!-- /Discount Price -->

              <!-- Coupon -->
              <el-row
                  :gutter="0" class="am-add-coupon am-flex-row-middle-align"
                  v-if="$root.settings.payments.coupons && bookable.price > 0"
              >

                <!-- Coupon Label -->
                <el-col :sm="10" :xs="10">
                  <img :src="$root.getUrl+'public/img/coupon.svg'" class="svg" alt="add-coupon">
                  <span>{{ $root.labels.add_coupon }}</span>
                </el-col>
                <!-- /Coupon Label -->

                <!-- Coupon Input -->
                <el-col :sm="14" :xs="14">
                  <el-form
                      :model="appointment.bookings[0].customer"
                      ref="coupon"
                      :rules="rules"
                      label-position="top"
                      @submit.prevent="onSubmit"
                      status-icon
                  >
                    <el-form-item prop="couponCode" :error="errors.coupon">
                      <el-input
                          v-model="coupon.code"
                          @input="clearValidation()"
                          type="text"
                          size="small"
                          class="am-add-coupon-field"
                      >

                        <!-- Coupon Button -->
                        <el-button
                            slot="append"
                            size="mini"
                            icon="el-icon-check" @click="checkCoupon"
                            :disabled="coupon.code === ''"
                        >
                        </el-button>
                        <!-- /Coupon Button -->

                      </el-input>
                    </el-form-item>
                  </el-form>
                </el-col>
                <!-- /Coupon Input -->

              </el-row>
              <!-- /Coupon -->

              <!-- Total Price -->
              <el-row class="am-confirmation-total" :gutter="24" v-if="bookable.price > 0"  :style="{'color': bookable.color, 'background-color': bookableType === 'event' ? '#E8E8E8' : ''}">
                <el-col :span="12">
                  <p>
                    {{ $root.labels.total_cost_colon }}
                  </p>
                </el-col>
                <el-col :span="12">
                  <p class="am-semi-strong am-align-right" :style="{'color': bookable.color}">
                    {{ getFormattedPrice(getTotalPrice()) }}
                  </p>
                </el-col>
              </el-row>
              <!-- /Total Price -->

            </div>
            <!-- /Payment Data -->

          </el-col>
        </el-row>
        <!-- /Appointment Data -->

      </el-form>
      <!-- /Confirm Dialog Body -->

      <!-- Confirm Dialog Footer -->
      <div class="dialog-footer payment-dialog-footer"
           slot="footer">
        <div class="el-button el-button--default"
             @mouseover="setBookableCancelStyle(true)"
             @mouseleave="setBookableCancelStyle(false)"
             :style="bookableCancelStyle"
             @click="cancelBooking()">
          <span :style="bookableCancelSpanStyle">{{ $root.labels.cancel }}</span>
        </div>

        <div class="paypal-button el-button el-button--primary"
             @mouseover="setBookableConfirmStyle(true)"
             @mouseleave="setBookableConfirmStyle(false)"
             :style="bookableConfirmStyle"
             v-show="$root.settings.payments.payPal.enabled && appointment.payment.gateway === 'payPal' && getTotalPrice() > 0">
          <div id="am-paypal-button-container"></div>
          <span>{{ $root.labels.confirm }}</span>
        </div>

        <div class="el-button el-button--primary"
             @mouseover="setBookableConfirmStyle(true)"
             @mouseleave="setBookableConfirmStyle(false)"
             :style="bookableConfirmStyle"
             v-show="showConfirmBookingButton"
             @click="confirmBooking()">
          <span>{{ $root.labels.confirm }}</span>
        </div>
      </div>
      <!-- /Confirm Dialog Footer -->

    </div>
    <!-- /Confirm Booking Form -->

    <!-- Spinner & Waiting For Payment -->
    <div id="am-spinner" class="am-booking-fetched" v-show="!fetched">
      <h4 v-if="appointment.payment.gateway === 'payPal'">{{ $root.labels.waiting_for_payment }}</h4>
      <h4 v-else>{{ $root.labels.please_wait }}</h4>
      <div class="am-svg-wrapper">

        <!-- Oval Spinner -->
        <span v-if="bookableType === 'event'">
          <svg width="160" height="160" class="am-spin" viewBox="0 0 38 38" xmlns="http://www.w3.org/2000/svg"  stroke="#7F8FA4">
            <g fill="none" fill-rule="evenodd">
              <g transform="translate(1 1)" stroke-width="2">
                <path d="M36 18c0-9.94-8.06-18-18-18" :style="{'stroke':bookable.color}" :stroke="bookable.color">
                  <animateTransform
                      attributeName="transform"
                      type="rotate"
                      from="0 18 18"
                      to="360 18 18"
                      dur="1s"
                      repeatCount="indefinite"/>
                </path>
              </g>
            </g>
          </svg>

          <!-- HourGlass -->
          <svg width="12px" height="16px" class="am-hourglass" viewBox="0 0 12 16" version="1.1" xmlns="http://www.w3.org/2000/svg">
            <g id="Icons" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"
               transform="translate(-2.000000, 0.000000)">
              <g id="sat" transform="translate(2.000000, 0.000000)" fill="#303C42">
                <path :style="{'fill':bookable.color}" :fill="bookable.color" d="M8.37968,4.8 L3.32848,4.8 C3.22074667,4.8 3.12368,4.86506667 3.08208,4.9648 C3.04101333,5.06453333 3.06394667,5.1792 3.14021333,5.25546667 L5.67834667,7.79093333 C5.72794667,7.84106667 5.79621333,7.86933333 5.86661333,7.86933333 C5.95941333,7.8672 6.00634667,7.84106667 6.05594667,7.7904 L8.56901333,5.2544 C8.64474667,5.1776 8.66714667,5.06346667 8.62554667,4.96426667 C8.58448,4.86453333 8.48741333,4.8 8.37968,4.8" id="Fill-694"></path>
                <path :style="{'fill':bookable.color}"  :fill="bookable.color" d="M6.82293333,7.62293333 C6.6144,7.83146667 6.6144,8.16853333 6.82293333,8.37706667 L9.04,10.5941333 C9.74506667,11.2992 10.1333333,12.2368 10.1333333,13.2341333 L10.1333333,14.4 L9.2,14.4 L6.08,10.24 C5.9792,10.1056 5.75413333,10.1056 5.65333333,10.24 L2.53333333,14.4 L1.6,14.4 L1.6,13.2341333 C1.6,12.2368 1.98826667,11.2992 2.69333333,10.5941333 L4.9104,8.37706667 C5.11893333,8.16853333 5.11893333,7.83146667 4.9104,7.62293333 L2.69333333,5.40586667 C1.98826667,4.7008 1.6,3.7632 1.6,2.7664 L1.6,1.6 L10.1333333,1.6 L10.1333333,2.7664 C10.1333333,3.7632 9.74506667,4.7008 9.04,5.40586667 L6.82293333,7.62293333 Z M11.2,2.7664 L11.2,1.45173333 C11.5173333,1.26666667 11.7333333,0.9264 11.7333333,0.533333333 L11.7333333,0.266666667 C11.7333333,0.119466667 11.6138667,0 11.4666667,0 L0.266666667,0 C0.119466667,0 0,0.119466667 0,0.266666667 L0,0.533333333 C0,0.9264 0.216,1.26666667 0.533333333,1.45173333 L0.533333333,2.7664 C0.533333333,4.048 1.03253333,5.25386667 1.9392,6.16 L3.7792,8 L1.9392,9.84 C1.03253333,10.7461333 0.533333333,11.952 0.533333333,13.2341333 L0.533333333,14.5482667 C0.216,14.7333333 0,15.0736 0,15.4666667 L0,15.7333333 C0,15.8805333 0.119466667,16 0.266666667,16 L11.4666667,16 C11.6138667,16 11.7333333,15.8805333 11.7333333,15.7333333 L11.7333333,15.4666667 C11.7333333,15.0736 11.5173333,14.7333333 11.2,14.5482667 L11.2,13.2341333 C11.2,11.952 10.7008,10.7461333 9.79413333,9.84 L7.95413333,8 L9.79413333,6.16 C10.7008,5.25386667 11.2,4.048 11.2,2.7664 L11.2,2.7664 Z" id="Fill-696"></path>
              </g>
            </g>
          </svg>
        </span>

        <span v-else>
          <img class="svg am-spin" :src="$root.getUrl+'public/img/oval-spinner.svg'"/>
          <img class="svg am-hourglass" :src="$root.getUrl+'public/img/hourglass.svg'"/>
        </span>
      </div>
    </div>
    <!-- /Spinner & Waiting For Payment -->

  </div>
</template>

<script>
  import moment from 'moment'
  import imageMixin from '../../../js/common/mixins/imageMixin'
  import PhoneInput from '../../parts/PhoneInput.vue'
  import dateMixin from '../../../js/common/mixins/dateMixin'
  import priceMixin from '../../../js/common/mixins/priceMixin'
  import helperMixin from '../../../js/backend/mixins/helperMixin'
  import customFieldMixin from '../../../js/common/mixins/customFieldMixin'

  export default {
    mixins: [imageMixin, dateMixin, priceMixin, helperMixin, customFieldMixin],

    props: {
      bookableType: null,
      bookable: {
        default: () => {},
        type: Object
      },
      appointment: {
        default: () => {},
        type: Object
      },
      provider: {
        default: () => {},
        type: Object
      },
      location: {
        default: () => {},
        type: Object
      },
      dialogClass: {
        default: '',
        type: String
      },
      customFields: {
        default: () => []
      }
    },

    data () {
      let validateCoupon = (rule, bookings, callback) => {
        let field = document.getElementsByClassName('am-add-coupon-field')[0].getElementsByClassName('el-input__suffix')[0]

        if (this.coupon.code) {
          this.$http.post(`${this.$root.getAjaxUrl}/coupons/validate`, {
            'code': this.coupon.code,
            'id': this.bookable.id,
            'type': this.bookableType,
            'user': this.appointment.bookings[0]['customer']
          }).then(response => {
            this.coupon = response.data.data.coupon
            if (typeof field !== 'undefined') {
              field.style.visibility = 'visible'
            }
            callback()
          }).catch(e => {
            this.coupon.discount = 0
            this.coupon.deduction = 0

            if (e.response.data.data.couponUnknown === true) {
              callback(new Error(this.$root.labels.coupon_unknown))
            } else if (e.response.data.data.couponInvalid === true) {
              callback(new Error(this.$root.labels.coupon_invalid))
            } else {
              callback()
            }

            if (typeof field !== 'undefined') {
              field.style.visibility = 'hidden'
            }
          })
        } else {
          if (typeof field !== 'undefined') {
            field.style.visibility = 'hidden'
          }
          callback()
        }
      }

      let validatePhone = (rule, input, callback) => {
        if (input && input !== '' && !input.startsWith('+')) {
          callback(new Error(this.$root.labels.enter_valid_phone_warning))
        } else {
          callback()
        }
      }

      return {
        customFieldsFiles: [],
        stripePayment: {
          stripe: null,
          cardElement: null
        },
        hoverConfirm: false,
        hoverCancel: false,
        columnsLg: 12,
        coupon: {
          code: '',
          discount: 0,
          deduction: 0
        },
        clearValidate: true,
        errors: {
          email: '',
          coupon: '',
          stripe: '',
          files: {}
        },
        fetched: true,
        headerErrorMessage: '',
        headerErrorShow: false,
        payPalActions: null,
        rules: {
          'customer.firstName': [
            {required: true, message: this.$root.labels.enter_first_name_warning, trigger: 'submit'}
          ],
          'customer.lastName': [
            {required: true, message: this.$root.labels.enter_last_name_warning, trigger: 'submit'}
          ],
          'customer.email': [
            {required: this.$root.settings.general.requiredEmailField, message: this.$root.labels.enter_email_warning, trigger: 'submit'},
            {type: 'email', message: this.$root.labels.enter_valid_email_warning, trigger: 'submit'}
          ],
          'customer.phone': [
            {
              required: this.$root.settings.general.requiredPhoneNumberField && !this.appointment.bookings[0].customer.id,
              message: this.$root.labels.enter_phone_warning,
              trigger: 'submit'
            },
            {validator: validatePhone, trigger: 'submit'}
          ],
          couponCode: [
            {validator: validateCoupon, trigger: 'submit'}
          ]
        }
      }
    },

    created () {
      this.inlineSVG()
      window.addEventListener('resize', this.handleResize)
    },

    mounted () {
      this.setBookableConfirmStyle(false)
      this.setBookableCancelStyle(false)

      this.inlineSVG()

      // Get Default Payment Option
      let paymentOption = this.paymentOptions.find(option => option.value === this.$root.settings.payments.defaultPaymentMethod)
      this.appointment.payment.gateway = paymentOption ? paymentOption.value : this.paymentOptions[0].value

      if (this.bookableType === 'appointment') {
        this.saveStats()
      }

      this.addCustomFieldsValidationRules()
      if (this.$root.settings.payments.payPal.enabled) {
        this.payPalInit()
      }

      // Customization hook
      if ('beforeConfirmBookingLoaded' in window) {
        window.beforeConfirmBookingLoaded(this.appointment, this.bookable, this.provider, this.location)
      }

      let $this = this

      if (this.$root.settings.payments.stripe.enabled) {
        this.stripeInit()
      }

      if (this.bookableType === 'event') {
        setTimeout(() => {
          $this.scrollView('am-confirm-booking')
        }, 1200)
      }
    },

    updated () {
      if (this.clearValidate === true) {
        this.clearValidation()
        this.clearValidate = false
      }
      this.handleResize()
    },

    methods: {
      stripeInit () {
        this.stripePayment.stripe = Stripe(this.getStripePublishableKey())

        let elements = this.stripePayment.stripe.elements()
        this.stripePayment.cardElement = elements.create('card')
        this.stripePayment.cardElement.mount('#card-element-' + this.$root.shortcodeData.counter)
      },

      setBookableConfirmStyle (isHover) {
        switch (this.bookableType) {
          case ('appointment'):
            break

          case ('event'):
            this.hoverConfirm = isHover
        }
      },

      setBookableCancelStyle (isHover) {
        switch (this.bookableType) {
          case ('appointment'):
            break

          case ('event'):
            this.hoverCancel = isHover
        }
      },

      saveStats: function () {},

      handleServerResponse (response) {
        let $this = this

        if (response.requiresAction) {
          $this.stripePayment.stripe.handleCardAction(
            response.paymentIntentClientSecret
          ).then(function (result) {
            if (result.error) {
              $this.headerErrorShow = true
              $this.headerErrorMessage = $this.$root.labels.payment_error
              $this.fetched = true
            } else {
              let requestData = $this.getRequestData({
                'paymentIntentId': result.paymentIntent.id
              })

              $this.saveBooking(requestData)
            }
          })
        } else {
          if (response) {
            $this.$emit('confirmedBooking', Object.assign(response, {color: $this.bookable.color, type: $this.bookableType}))
          } else {
            $this.fetched = true
          }
        }
      },

      cancelBooking () {
        this.$emit('cancelBooking')
      },

      onSelectFiles () {
        this.clearValidation()
      },

      validateUploadedFiles () {
        let isValid = true

        let bookings = this.appointment.bookings

        let customFieldFilesIndexes = {}

        let filesIndex = 0

        for (let i = 0; i < this.customFields.length; i++) {
          if (this.customFields[i].type === 'file') {
            customFieldFilesIndexes[this.customFields[i].id] = filesIndex
            filesIndex++
          }
        }

        for (let key in bookings[0].customFields) {
          if (!bookings[0].customFields.hasOwnProperty(key)) {
            continue
          }

          let customField = this.customFields.find(field => parseInt(field.id) === parseInt(key))

          if (this.isCustomFieldVisible(customField, this.bookableType, this.bookable.id) && customField.type === 'file' && customField.required) {
            let uploadedFilesIndex = key in customFieldFilesIndexes && customFieldFilesIndexes[key] in this.$refs.customFieldsFiles ? customFieldFilesIndexes[key] : null

            if (uploadedFilesIndex !== null && this.$refs.customFieldsFiles[uploadedFilesIndex].uploadFiles.length === 0) {
              this.errors.files['files' + customField.id] = this.$root.labels.file_upload_error
              isValid = false
            }
          }
        }

        return isValid
      },

      confirmBooking () {
        if (!this.fetched) {
          return
        }

        let $this = this

        this.headerErrorShow = false
        this.errors.email = ''
        this.errors.coupon = ''
        this.clearValidation()
        // Validate Form
        this.$refs.booking.validate((valid) => {
          if (this.validateUploadedFiles() && valid && this.errors.stripe === '' && this.errors.coupon === '') {
            // Customization hook
            if ('afterConfirmBooking' in window) {
              window.afterConfirmBooking(this.appointment, this.bookable, this.provider, this.location)
            }

            this.fetched = false
            this.inlineSVG()

            if (this.getTotalPrice() === 0 &&
              (
                this.appointment.payment.gateway === 'payPal' ||
                this.appointment.payment.gateway === 'stripe' ||
                ('ameliaBooking' in window && 'wc' in window.ameliaBooking && 'bookIfPriceIsZero' in window.ameliaBooking.wc && window.ameliaBooking.wc.bookIfPriceIsZero === true)
              )
            ) {
              this.appointment.payment.gateway = 'onSite'
            }

            switch (this.appointment.payment.gateway) {
              case 'stripe':
                $this.stripePayment.stripe.createPaymentMethod('card', $this.stripePayment.cardElement, {
                }).then(function (result) {
                  if (result.error) {
                    $this.headerErrorShow = true
                    $this.headerErrorMessage = $this.$root.labels.payment_error
                    $this.fetched = true
                  } else {
                    let requestData = $this.getRequestData({
                      'paymentMethodId': result.paymentMethod.id
                    })

                    $this.$http.post(`${$this.$root.getAjaxUrl}/bookings`, requestData.data, requestData.options
                    ).then(response => {
                      if (response.data.data) {
                        $this.handleServerResponse(response.data.data)
                      }
                    }).catch(e => {
                      $this.handleSaveBookingErrors(e.response.data)
                    })
                  }
                })
                break
              case 'onSite':
                this.saveBooking(this.getRequestData())
                break
              case 'wc':
                this.addToWooCommerceCart()
                break
            }
            this.scrollView('am-spinner')
          } else {
            this.fetched = true
            return false
          }
        })
      },

      buildFormData (formData, data, parentKey) {
        let $this = this

        if (data && typeof data === 'object' && !(data instanceof Date) && !(data instanceof File)) {
          Object.keys(data).forEach(key => {
            $this.buildFormData(formData, data[key], parentKey ? `${parentKey}[${key}]` : key)
          })
        } else {
          formData.append(parentKey, data !== null ? data : '')
        }
      },

      saveBooking (requestData) {
        this.$http.post(`${this.$root.getAjaxUrl}/bookings`, requestData.data, requestData.options).then(response => {
          if (response.data.data) {
            this.$emit('confirmedBooking', Object.assign(response.data.data, {color: this.bookable.color, type: this.bookableType}))
          } else {
            this.fetched = true
          }
        }).catch(e => {
          this.handleSaveBookingErrors(e.response.data)
        })
      },

      addToWooCommerceCart: function () {},

      getAppointmentDate () {
        return this.getFrontedFormattedDate(
          moment(this.bookable.bookingStart, 'YYYY-MM-DD HH:mm:ss').format('YYYY-MM-DD')
        )
      },

      getAppointmentTime () {
        return this.getFrontedFormattedTime(this.bookable.bookingStartTime)
      },

      getBookingPrice () {
        let priceFormatted = this.getFormattedPrice(this.bookable.price)
        let totalPrice = this.getPriceMultipleValue() * this.bookable.price
        let totalPriceFormatted = this.getFormattedPrice(totalPrice)

        return this.getPriceMultipleValue() > 1 ? this.getPriceMultipleValue() + ' ' + this.$root.labels.persons + ' x ' + priceFormatted + ' = ' + totalPriceFormatted : totalPriceFormatted
      },

      getExtrasPrice: function () {
        return 0
      },

      getSubtotalPrice () {
        let appointmentPrice = this.getPriceMultipleValue() * this.bookable.price

        return appointmentPrice + this.getExtrasPrice()
      },

      getDiscountPrice: function () {
        return 0
      },

      getTotalPrice () {
        let totalPrice = this.getSubtotalPrice() - this.getDiscountPrice()
        return totalPrice > 0 ? totalPrice : 0
      },

      getSelectedExtraDetails: !AMELIA_LITE_VERSION ? function (extra) {
        let result = ''

        if (this.getPriceMultipleValue() > 1) {
          result += this.getPriceMultipleValue() + ' ' + this.$root.labels.persons + ' x '
        }

        if (extra.quantity > 1) {
          result += extra.quantity + ' ' + ' x '
        }

        result += extra.name

        return result
      } : function () {},

      getSelectedExtraPrice: function () {},

      getPriceMultipleValue () {
        return this.bookable.aggregatedPrice ? this.appointment.bookings[0].persons : 1
      },

      checkCoupon: function () {},

      getExtras () {
        let extras = JSON.parse(JSON.stringify(this.selectedExtras))

        extras.forEach(function (extItem) {
          extItem.extraId = extItem.id
          extItem.id = null
        })

        return extras
      },

      getRequestData (paymentData) {
        let customFieldFilesIndexes = {}

        let filesIndex = 0

        for (let i = 0; i < this.customFields.length; i++) {
          if (this.customFields[i].type === 'file') {
            customFieldFilesIndexes[this.customFields[i].id] = filesIndex
            filesIndex++
          }
        }

        this.appointment.payment.amount = this.getFormattedAmount()

        let bookings = JSON.parse(JSON.stringify(this.appointment.bookings))
        bookings[0].extras = this.getExtras()

        let customFields = {}

        for (let key in bookings[0].customFields) {
          if (!bookings[0].customFields.hasOwnProperty(key)) {
            continue
          }

          let customField = this.customFields.find(field => parseInt(field.id) === parseInt(key))

          if (this.isCustomFieldVisible(customField, this.bookableType, this.bookable.id)) {
            let uploadedFilesIndex = key in customFieldFilesIndexes && customFieldFilesIndexes[key] in this.$refs.customFieldsFiles ? customFieldFilesIndexes[key] : null

            if (uploadedFilesIndex !== null) {
              let uploadCustomField = {
                label: bookings[0].customFields[key].label,
                value: []
              }

              for (let i = 0; i < this.$refs.customFieldsFiles[uploadedFilesIndex].uploadFiles.length; i++) {
                uploadCustomField.value.push({
                  name: this.$refs.customFieldsFiles[uploadedFilesIndex].uploadFiles[i].name
                })
              }

              customFields[key] = (uploadCustomField)
            } else {
              customFields[key] = (bookings[0].customFields[key])
            }

            customFields[key].type = customField.type
          }
        }

        bookings[0].customFields = customFields

        let bookingDateTime = this.bookable.bookingStart

        bookings[0].utcOffset = null

        if (this.$root.settings.general.showClientTimeZone) {
          bookingDateTime = moment(bookingDateTime, 'YYYY-MM-DD HH:mm').utc().format('YYYY-MM-DD HH:mm')

          bookings[0].utcOffset = this.getClientUtcOffset(bookingDateTime)
        }

        let jsonData = {
          'type': this.bookableType,
          'bookings': bookings,
          'payment': Object.assign(this.appointment.payment, {data: paymentData}),
          'couponCode': this.coupon.code
        }

        switch (this.bookableType) {
          case ('appointment'):
            jsonData = Object.assign(jsonData, {
              'bookingStart': bookingDateTime,
              'notifyParticipants': this.appointment.notifyParticipants ? 1 : 0,
              'locationId': this.appointment.locationId,
              'providerId': this.appointment.providerId,
              'serviceId': this.bookable.id
            })

            break
          case ('event'):
            jsonData = Object.assign(jsonData, {
              'eventId': this.bookable.id
            })
        }

        let bookingData = null
        let requestOptions = null

        let hasUploadedFiles = false

        if (this.$refs.customFieldsFiles) {
          for (let i = 0; i < this.$refs.customFieldsFiles.length; i++) {
            if (this.$refs.customFieldsFiles[i].uploadFiles.length > 0) {
              hasUploadedFiles = true
              break
            }
          }
        }

        if (hasUploadedFiles) {
          bookingData = new FormData()

          this.buildFormData(bookingData, jsonData)

          for (let key in customFieldFilesIndexes) {
            let index = customFieldFilesIndexes[key]

            if (!customFieldFilesIndexes.hasOwnProperty(key) || !(index in this.$refs.customFieldsFiles)) {
              continue
            }

            for (let i = 0; i < this.$refs.customFieldsFiles[index].uploadFiles.length; i++) {
              bookingData.append('files[' + key + '][' + i + ']', this.$refs.customFieldsFiles[index].uploadFiles[i].raw)
            }
          }

          requestOptions = {
            headers: {
              'Content-Type': 'multipart/form-data'
            }
          }
        } else {
          bookingData = jsonData

          requestOptions = {}
        }

        return {
          data: bookingData,
          options: requestOptions
        }
      },

      getFormattedAmount () {
        return this.getTotalPrice().toFixed(2).toString()
      },

      handleSaveBookingErrors (response) {
        if ('data' in response) {
          if ('onSitePayment' in response.data && response.data.onSitePayment) {
            this.appointment.payment.gateway = 'onSite'
            this.saveBooking(this.getRequestData())
          }
          if ('customerAlreadyBooked' in response.data && response.data.customerAlreadyBooked === true) {
            this.headerErrorShow = true
            this.headerErrorMessage = this.$root.labels.customer_already_booked
          }
          if ('timeSlotUnavailable' in response.data && response.data.timeSlotUnavailable === true) {
            this.headerErrorShow = true
            switch (this.bookableType) {
              case 'appointment':
                this.headerErrorMessage = this.$root.labels.time_slot_unavailable
                break
              case 'event':
                this.headerErrorMessage = this.$root.labels.maximum_capacity_reached
                break
            }
          } else if ('emailError' in response.data && response.data.emailError === true) {
            this.errors.email = this.$root.labels.email_exist_error
          } else if ('couponUnknown' in response.data && response.data.couponUnknown === true) {
            this.errors.coupon = this.$root.labels.coupon_unknown
          } else if ('couponInvalid' in response.data && response.data.couponInvalid === true) {
            this.errors.coupon = this.$root.labels.coupon_invalid
          } else if ('couponMissing' in response.data && response.data.couponMissing === true) {
            this.errors.coupon = this.$root.labels.coupon_missing
          } else if ('paymentSuccessful' in response.data && response.data.paymentSuccessful === false) {
            this.headerErrorShow = true
            this.headerErrorMessage = this.$root.labels.payment_error
          } else if ('bookingAlreadyInWcCart' in response.data && response.data.bookingAlreadyInWcCart === true) {
            this.headerErrorShow = true
            this.headerErrorMessage = this.$root.labels.booking_already_in_wc_cart
          } else if ('wcError' in response.data && response.data.wcError === true) {
            this.headerErrorShow = true
            this.headerErrorMessage = this.$root.labels.wc_error
          }
        }

        this.fetched = true
      },

      validateFieldsForPayPal () {
        if (this.payPalActions === null || this.appointment.payment.gateway !== 'payPal') {
          return
        }

        let validCustomFields = true

        for (let key in this.appointment.bookings[0].customFields) {
          if (!this.appointment.bookings[0].customFields.hasOwnProperty(key)) {
            continue
          }

          let customField = this.customFields.find(field => parseInt(field.id) === parseInt(key))

          if (this.isCustomFieldVisible(customField, this.bookableType, this.bookable.id) && customField.required && customField.type !== 'file' &&
            (
              (Array.isArray(this.appointment.bookings[0].customFields[key].value) && this.appointment.bookings[0].customFields[key].value.length === 0) ||
              (!Array.isArray(this.appointment.bookings[0].customFields[key].value) && this.appointment.bookings[0].customFields[key].value.trim() === '')
            )
          ) {
            validCustomFields = false
          }
        }

        if (this.appointment.bookings[0].customer.lastName === '' ||
          this.appointment.bookings[0].customer.firstName === '' ||
          (this.$root.settings.general.requiredEmailField && this.appointment.bookings[0].customer.email === '') ||
          (this.$root.settings.general.requiredEmailField && !(/(.+)@(.+){2,}\.(.+){2,}/.test(this.appointment.bookings[0].customer.email))) ||
          (this.$root.settings.general.requiredPhoneNumberField && !this.appointment.bookings[0].customer.id && this.appointment.bookings[0].customer.phone === '') ||
          !validCustomFields ||
          !this.validateUploadedFiles()) {
          this.payPalActions.disable()
        } else {
          this.payPalActions.enable()
        }
      },

      payPalInit: function () {},

      parseError: function (error) {
        let errorString = error.toString()
        let response = JSON.parse(JSON.stringify(JSON.parse(errorString.substring(errorString.indexOf('{'), errorString.lastIndexOf('}') + 1))))

        if (typeof response === 'object' && response.hasOwnProperty('data')) {
          this.handleSaveBookingErrors(response)
        } else {
          this.headerErrorShow = true
          this.headerErrorMessage = this.$root.labels.payment_error
        }

        this.fetched = true
        this.inlineSVG()
      },

      getStripePublishableKey: function () {},

      clearValidation () {
        this.validateFieldsForPayPal()

        if (typeof this.$refs.booking !== 'undefined') {
          this.$refs.booking.clearValidate()
        }

        if (typeof this.$refs.coupon !== 'undefined') {
          this.$refs.coupon.clearValidate()
        }

        let $this = this

        Object.keys(this.errors.files).forEach(function (key) {
          $this.errors.files[key] = ''
        })

        if (this.errors.files) {
          let firstName = this.appointment.bookings[0].customer.firstName
          this.appointment.bookings[0].customer.firstName = firstName + '_'
          this.appointment.bookings[0].customer.firstName = firstName
        }
      },

      phoneFormatted (phone) {
        this.appointment.bookings[0].customer.phone = phone
        this.clearValidation()
      },

      handleResize () {
        let amContainer = document.getElementById('amelia-app-booking' + this.$root.shortcodeData.counter)
        let amContainerWidth = amContainer.offsetWidth
        if (amContainerWidth < 670) {
          this.columnsLg = 24
        }
      },

      addCustomFieldsValidationRules: function () {}
    },

    computed: {
      bookableConfirmStyle () {
        return this.hoverConfirm ? {
          color: this.bookable.color,
          borderColor: this.bookable.color,
          backgroundColor: this.bookable.color,
          opacity: 0.8
        } : {
          color: '#ffffff',
          backgroundColor: this.bookable.color,
          borderColor: this.bookable.color,
          opacity: 1
        }
      },

      bookableCancelStyle () {
        return this.hoverCancel ? {
          color: this.bookable.color,
          borderColor: this.bookable.color,
          backgroundColor: '',
          opacity: 0.7
        } : {
          color: '',
          backgroundColor: '#ffffff',
          borderColor: '',
          opacity: 1
        }
      },

      bookableCancelSpanStyle () {
        return this.hoverCancel ? {
          color: this.bookable.color,
          borderColor: '',
          backgroundColor: '',
          opacity: 0.9
        } : {
          color: '',
          backgroundColor: '',
          borderColor: '',
          opacity: 1
        }
      },

      selectedExtras: function () {
        return []
      },

      paymentOptions () {
        let paymentOptions = []

        if (this.$root.settings.payments.onSite === true) {
          paymentOptions.push({
            value: 'onSite',
            label: this.$root.labels.on_site
          })
        }

        if (this.$root.settings.payments.payPal.enabled) {
          paymentOptions.push({
            value: 'payPal',
            label: this.$root.labels.pay_pal
          })
        }

        if (this.$root.settings.payments.stripe.enabled) {
          paymentOptions.push({
            value: 'stripe',
            label: this.$root.labels.credit_card
          })
        }

        if (this.$root.settings.payments.wc.enabled) {
          paymentOptions.push({
            value: 'wc',
            label: this.$root.labels.wc
          })
        }

        return paymentOptions
      },

      showConfirmBookingButton () {
        return this.appointment.payment.gateway === 'onSite' ||
          this.appointment.payment.gateway === 'wc' ||
          this.appointment.payment.gateway === 'stripe' ||
          (this.appointment.payment.gateway === 'payPal' && this.getTotalPrice() === 0)
      }
    },

    components: {
      moment,
      PhoneInput
    }
  }
</script>