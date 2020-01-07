<template>
  <div class="am-dialog-table">
    <div v-if="appointment.bookings.length > 0">
      <div v-for="booking in appointment.bookings" class="am-customer-extras">
        <div v-for="payment in booking.payments">
          <el-row class="am-customer-extras-data">
            <el-col>
              <h3>{{ booking.customer.firstName }} {{ booking.customer.lastName }}</h3>
              <span>{{ booking.customer.email }}</span>
            </el-col>
          </el-row>

          <el-row class="am-customer-extras-payment" :gutter="10">
            <el-col :span="12">
              <span class="am-strong">{{ $root.labels.payment }}</span>
            </el-col>
            <el-col :span="12">
              <span class="am-link" @click="showDialogEditPayment(payment.id)">
                {{ $root.labels.view_payment_details }}
              </span>
            </el-col>
            <el-col :span="12">
              <span>{{ $root.labels.date }}:</span>
            </el-col>
            <el-col :span="12">
              <span class="am-semi-strong">{{ getFrontedFormattedDate(payment.dateTime) }}</span>
            </el-col>
            <el-col :span="12">
              <span>{{ $root.labels.payment_method }}:</span>
            </el-col>
            <el-col :span="12">
                      <span class="am-semi-strong">
                        <img class="svg" width="14px"
                             :src="$root.getUrl + 'public/img/payments/' + payment.gateway + '.svg'"/> {{ getPaymentGatewayNiceName(payment) }}
                      </span>
            </el-col>
            <el-col :span="12">
              <span>{{ $root.labels.status }}:</span>
            </el-col>
            <el-col :span="12">
                        <span class="am-semi-strong"><i
                            :class="{'el-icon-circle-check':payment.status === 'paid','el-icon-refresh':payment.status !== 'paid'}"></i> {{ getPaymentStatus(payment.status) }}</span>
            </el-col>
          </el-row>

          <el-row :gutter="10" class="subtotal">
            <el-col :span="14" class="align-right">
              {{ $root.labels.service_price }}:
            </el-col>
            <el-col :span="10" class="align-right">
              {{ getFormattedPrice(booking.serviceTotalPrice) }}
            </el-col>

            <el-col :span="14" class="align-right">
              {{ $root.labels.extras }}:
            </el-col>
            <el-col :span="10" class="align-right">
              {{ getFormattedPrice(booking.extrasTotalPrice) }}
            </el-col>

            <el-col :span="14" class="align-right">
              {{ $root.labels.discount_amount }}:
            </el-col>
            <el-col :span="10" class="align-right">
              {{ getFormattedPrice(booking.discountTotalPrice) }}
            </el-col>

            <el-col :span="14" class="align-right">
              <span class="am-strong">{{ $root.labels.subtotal }}:</span>
            </el-col>
            <el-col :span="10" class="align-right">
              <span class="am-strong">{{ getFormattedPrice(getBookingSubtotal(booking)) }}</span>
            </el-col>
          </el-row>
        </div>
      </div>
    </div>
    <div v-else>
      <p align="center">{{ $root.labels.no_selected_customers }}</p>
    </div>
  </div>
</template>

<script>
  import dateMixin from '../../../js/common/mixins/dateMixin'
  import priceMixin from '../../../js/common/mixins/priceMixin'
  import paymentMixin from '../../../js/backend/mixins/paymentMixin'

  export default {
    mixins: [dateMixin, priceMixin, paymentMixin],

    props: {
      appointment: {
        default: () => {},
        type: Object
      }
    },

    data () {
      return {
        paymentStatuses: [
          {
            value: 'paid',
            label: this.$root.labels.paid

          }, {
            value: 'pending',
            label: this.$root.labels.pending

          }
        ]
      }
    },

    methods: {
      showDialogEditPayment (paymentId) {
        this.$emit('editPayment', {paymentId: paymentId, appointment: this.appointment})
      },

      getPaymentStatus (status) {
        let statusLabel = ''

        this.paymentStatuses.forEach(function (statItem) {
          if (statItem.value === status) {
            statusLabel = statItem.label
          }
        })

        return statusLabel
      },

      getBookingSubtotal (booking) {
        let subtotal = booking.serviceTotalPrice + booking.extrasTotalPrice - booking.discountTotalPrice

        return subtotal >= 0 ? subtotal : 0
      }
    }
  }
</script>