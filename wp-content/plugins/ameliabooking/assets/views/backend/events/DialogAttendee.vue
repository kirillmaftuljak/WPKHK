<template>
  <div>
    <!-- Dialog Loader -->
    <div class="am-dialog-loader" v-show="dialogLoading">
      <div class="am-dialog-loader-content">
        <img :src="$root.getUrl+'public/img/spinner.svg'" class=""/>
        <p>{{ $root.labels.loader_message }}</p>
      </div>
    </div>

    <!-- Dialog Content -->
    <div class="am-dialog-scrollable" :class="{'am-edit':eventBooking.id !== 0}" v-if="eventBooking && !dialogLoading" style="overflow-x: hidden;">

      <!-- Dialog Header -->
      <div class="am-dialog-header">
        <el-row>
          <el-col :span="18">
            <h2>{{ $root.labels.event_edit_attendee }}</h2>
          </el-col>
          <el-col :span="6" class="align-right">
            <el-button @click="closeDialog" class="am-dialog-close" size="small" icon="el-icon-close">
            </el-button>
          </el-col>
        </el-row>
      </div>

      <!-- Form -->
      <el-form v-if="mounted" :model="{bookings: [eventBooking]}" ref="appointment" :rules="rules" label-position="top">
        <!-- Customer -->
        <el-form-item :label="$root.labels.customer + ':'" prop="bookings">
          <el-select
              v-model="eventBooking.customerId"
              filterable
              clearable
              :placeholder="$root.labels.customer"
          >
            <div class="am-drop">
              <div class="am-drop-create-item" @click="showDialogNewCustomer">
                {{ $root.labels.create_new }}
              </div>
              <el-option
                  v-for="item in customers"
                  :key="item.id"
                  :label="(user = getCustomerInfo({customerId: item.id})) !== null ? user.firstName + ' ' + user.lastName : ''"
                  :value="item.id"
                  class="am-has-option-meta"
              >
                <span class="am-drop-item-name">{{ item.firstName }} {{ item.lastName }}</span>
                <span class="am-drop-item-meta">{{ item.email }}</span>
              </el-option>
              <el-option
                  v-if="customers.length === 0"
                  v-for="item in [{customer: {id: 0, firstName: '', lastName: '', email: '', info: JSON.stringify({firstName: '', lastName: '', email: '', phone: ''})}}]"
                  :key="item.customer.id"
                  :label="(user = getCustomerInfo(item)) !== null ? user.firstName + ' ' + user.lastName : ''"
                  :value="item"
                  class="am-has-option-meta"
                  :style="{'display': 'none'}"
              >
              </el-option>
            </div>
          </el-select>
        </el-form-item>

        <el-form-item :label="$root.labels.event_book_persons + ':'">
          <el-input-number v-model="eventBooking.persons" :min="1">
          </el-input-number>
        </el-form-item>

        <dialog-custom-fields
            :appointment="{bookings: [eventBooking]}"
            :entityId="eventId"
            entityType="event"
            :customFields="options.entities.customFields"
            @clearValidation="clearValidation"
            :showCustomerInfo="false"
        >
        </dialog-custom-fields>
      </el-form>
    </div>

    <!-- Dialog Actions -->
    <dialog-actions
        v-if="eventBooking && !dialogLoading"
        formName="appointment"
        :urlName="eventBooking.id !== 0 ? 'events/bookings' : 'bookings'"
        :isNew="eventBooking.id === 0"
        :entity="eventBooking"
        :getParsedEntity="getParsedEntity"
        @errorCallback="errorCallback"
        @validationBookingsFailCallback="validationBookingsFailCallback"
        :hasIcons="true"

        :status="{
          on: 'visible',
          off: 'hidden'
        }"

        :action="{
          haveAdd: true,
          haveEdit: true,
          haveStatus: false,
          haveRemove: false,
          haveRemoveEffect: false,
          haveDuplicate: false
        }"

        :message="{
          success: {
            save: $root.labels.event_attendee_saved,
            remove: '',
            show: '',
            hide: ''
          },
          confirm: {
            remove: '',
            show: '',
            hide: '',
            duplicate: ''
          }
        }"
    >
    </dialog-actions>

    <div>
      <!-- Dialog Loader -->
      <div class="am-dialog-loader" v-show="dialogLoading">
        <div class="am-dialog-loader-content">
          <img :src="$root.getUrl+'public/img/spinner.svg'" class="">
          <p>{{ $root.labels.loader_message }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import DialogActions from '../parts/DialogActions.vue'
  import imageMixin from '../../../js/common/mixins/imageMixin'
  import dateMixin from '../../../js/common/mixins/dateMixin'
  import notifyMixin from '../../../js/backend/mixins/notifyMixin'
  import entitiesMixin from '../../../js/common/mixins/entitiesMixin'
  import appointmentPriceMixin from '../../../js/backend/mixins/appointmentPriceMixin'
  import priceMixin from '../../../js/common/mixins/priceMixin'
  import DialogCustomFields from '../parts/DialogCustomFields'
  import customFieldMixin from '../../../js/common/mixins/customFieldMixin'

  export default {

    mixins: [entitiesMixin, imageMixin, dateMixin, notifyMixin, priceMixin, customFieldMixin, appointmentPriceMixin],

    props: {
      eventBooking: null,
      eventId: null,
      eventMaxCapacity: null,
      options: null,
      customerCreatedCount: 0
    },

    data () {
      return {
        customers: [],
        appointment: null,
        dialogLoading: true,
        executeUpdate: true,
        mounted: false,
        statusMessage: '',
        rules: {
          bookings: []
        }
      }
    },

    mounted () {
      this.customers = this.options.entities.customers
      this.appointment = {
        bookings: [this.eventBooking]
      }
      this.instantiateDialog()
      this.setBookingCustomFields()
      this.addCustomFieldsValidationRules()
    },

    updated () {
      this.instantiateDialog()
    },

    methods: {
      showDialogNewCustomer () {
        this.$emit('showDialogNewCustomer')
      },

      instantiateDialog () {
        if ((this.eventBooking !== null || (this.eventBooking !== null && this.eventBooking.id === 0)) && this.executeUpdate === true) {
          this.mounted = true
          this.executeUpdate = false
          this.dialogLoading = false
        }
      },

      closeDialog () {
        this.$emit('closeDialog')
      },

      getParsedEntity () {
        return {
          type: 'event',
          eventId: this.eventId,
          bookings: [
            {
              customFields: this.eventBooking.customFields,
              persons: this.eventBooking.persons,
              customerId: this.eventBooking.customerId,
              customer: this.getCustomerById(this.eventBooking.customerId)
            }
          ],
          payment: {
            gateway: 'onSite'
          }
        }
      },

      clearValidation () {
        if (typeof this.$refs.eventBooking !== 'undefined') {
          this.$refs.eventBooking.clearValidate()
        }
      },

      errorCallback (responseData) {
      },

      addCustomFieldsValidationRules () {
        // Go through all custom fields
        for (let j = 0; j < this.options.entities.customFields.length; j++) {
          // Check if custom fields is assigned to selected service
          if (this.isCustomFieldVisible(this.options.entities.customFields[j], 'event', this.eventId)) {
            if (typeof this.rules.bookings[0] === 'undefined') {
              this.$set(this.rules.bookings, 0, {type: 'array'})
            }

            if (typeof this.rules.bookings[0].customFields === 'undefined') {
              this.$set(this.rules.bookings[0], 'customFields', {})
            }

            this.rules.bookings[0].customFields[this.options.entities.customFields[j].id] = {
              value: [
                {required: true, message: this.$root.labels.required_field, trigger: 'submit'}
              ]
            }
          }
        }
      },

      showCustomFieldsTab () {
        let eventsIdsWithCustomField = Array.prototype.concat.apply(
          [], this.options.entities.customFields.map(customField => customField.events.map(event => event.id))
        )

        return this.options.entities.customFields.length > 0 &&
          this.booking !== null &&
          this.eventId &&
          eventsIdsWithCustomField.includes(this.eventId)
      },

      validationBookingsFailCallback () {
      }
    },

    computed: {
    },

    watch: {
      'customerCreatedCount' () {
        this.customers = this.options.entities.customers

        this.eventBooking.customerId = this.customers[this.customers.length - 1].id

        this.customers.sort(function (a, b) {
          return (a.firstName + ' ' + a.lastName).localeCompare((b.firstName + ' ' + b.lastName))
        })
      }
    },

    components: {
      DialogCustomFields,
      DialogActions
    }

  }
</script>
