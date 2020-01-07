<template>
  <div class="am-dialog-table">

    <!-- Form -->
    <div v-for="(booking, key) in appointment.bookings" class="am-customer-extras">

      <!-- Customer Name & Email -->
      <el-row class="am-customer-extras-data">
        <el-col>
          <h3>{{ booking.customer.firstName }} {{ booking.customer.lastName }}</h3>
          <span>{{ booking.customer.email }}</span>
        </el-col>
      </el-row>

      <!-- Custom Fields -->
      <div class="am-custom-fields">
        <el-form-item
            v-for="customField in customFields"
            :key="customField.id"
            :label="customField.label + ':'"
            :prop="customField.required === true && customField.type !== 'content' ? 'bookings.' + key + '.customFields.' + customField.id + '.value' : null"
            v-if="isCustomFieldVisible(customField) && customField.type !== 'content'"
        >

          <!-- Text Field -->
          <el-input
              v-if="customField.type === 'text'"
              placeholder=""
              v-model="appointment.bookings[key].customFields[customField.id].value"
              @input="clearValidation()"
          >
          </el-input>

          <!-- Text Area -->
          <el-input
              v-else-if="customField.type === 'text-area'"
              class="am-front-texarea"
              placeholder=""
              v-model="appointment.bookings[key].customFields[customField.id].value"
              type="textarea"
              :rows="3"
              @input="clearValidation()"
          >
          </el-input>

          <!-- Selectbox -->
          <el-select
              v-else-if="customField.type === 'select'"
              placeholder=""
              v-model="appointment.bookings[key].customFields[customField.id].value"
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

          <!-- Checkbox -->
          <el-checkbox-group
              v-else-if="customField.type === 'checkbox'"
              v-model="appointment.bookings[key].customFields[customField.id].value"
              @change="clearValidation()"
          >
            <el-checkbox
                v-for="(option, index) in getCustomFieldOptions(customField.options)"
                :key="index"
                :label="option"
            >
            </el-checkbox>
          </el-checkbox-group>

          <!-- Radio Buttons -->
          <el-radio-group v-else v-model="appointment.bookings[key].customFields[customField.id].value">
            <el-radio
                v-for="(option, index) in getCustomFieldOptions(customField.options)"
                :key="index"
                :label="option"
                @change="clearValidation()"
            >
            </el-radio>
          </el-radio-group>

        </el-form-item>
      </div>

    </div>
  </div>
</template>

<script>
  import customFieldMixin from '../../../js/common/mixins/customFieldMixin'

  export default {
    mixins: [customFieldMixin],

    props: {
      appointment: {
        default: () => {},
        type: Object
      },
      customFields: {
        default: () => []
      }
    },

    data () {
      return {
        hasPayments: false,
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
      isCustomFieldVisible (customField) {
        return customField.services.map(service => service.id).indexOf(this.appointment.serviceId) !== -1
      },

      clearValidation () {
        this.$emit('clearValidation')
      }
    }
  }
</script>