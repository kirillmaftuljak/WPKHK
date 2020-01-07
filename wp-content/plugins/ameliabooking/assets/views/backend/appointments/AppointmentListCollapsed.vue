<template>
  <div>

    <!-- Collapsed Data For One Booking -->
    <div class="am-appointment-details">
      <el-row>

        <!-- Customer Phone, Customer Email & Custom Fields -->
        <el-row
            v-for="(booking, key) in app.bookings"
            :key="key"
            :class="{ 'has-divider': app.bookings.length > 1 && (app.bookings.length !== key + 1 || app.internalNotes) }"
        >

          <!-- Customer Name -->
          <h3 v-if="app.bookings.length > 1">
            {{ ((user = getCustomerById(booking.customerId)) !== null ? user.firstName + ' ' +
            user.lastName : '') }}
          </h3>

          <!-- Customer Email -->
          <div v-if="$root.settings.role !== 'customer'" class="am-appointment-collapsed-field">

            <!-- Customer Email Label -->
            <el-col :sm="5">
              <p class="am-data">{{ $root.labels.customer_email }}:</p>
            </el-col>

            <!-- Customer Email Value -->
            <el-col :sm="7">
              <p class="am-value">
                {{ ((user = getCustomerById(app.bookings[key].customerId)) !== null ? user.email : '') }}
              </p>
            </el-col>

          </div>

          <!-- Customer Phone -->
          <div
              v-if="getCustomerInfo(app.bookings[key]) && getCustomerInfo(app.bookings[key]).phone && $root.settings.role !== 'customer'"
              class="am-appointment-collapsed-field"
          >

            <!-- Customer Phone Label -->
            <el-col :sm="5">
              <p class="am-data">{{ $root.labels.customer_phone }}:</p>
            </el-col>

            <!-- Customer Phone Value -->
            <el-col :sm="7">
              <p class="am-value">
                {{ getCustomerInfo(app.bookings[key]).phone }}
              </p>
            </el-col>

          </div>

          <!-- Custom Fields -->
          <div v-for="(customField, customFieldId) in JSON.parse(app.bookings[key].customFields)"
               v-if="customField && showCustomField(customField.value)"
               class="am-appointment-collapsed-field"
          >

            <!-- Custom Field Label -->
            <el-col :sm="5">
              <p class="am-data">{{ getCustomFieldLabel(customField, customFieldId) }}:</p>
            </el-col>

            <!-- Custom Field Value -->
            <el-col :sm="7">
              <p v-if="customField.type !== 'file'" class="am-value">
                {{ customField ? getCustomFieldValue(customField.value) : '' }}
              </p>
              <div v-else>
                <p v-for="(fileInfo, index) in customField.value" class="am-link">
                  <a
                      :key="index"
                      :href="$root.useUploadsAmeliaPath ? $root.getAjaxUrl + '/fields/' + customFieldId + '/' + app.bookings[key].id + '/' + index : $root.getUploadsAmeliaUrl + app.bookings[key].id + '_' + fileInfo.fileName"
                      target="_blank"
                  >
                    {{fileInfo.name}}
                  </a>
                </p>
              </div>
            </el-col>

          </div>

        </el-row>

        <!-- Note -->
        <el-row v-if="app.internalNotes && $root.settings.role !== 'customer'">
          <el-col :sm="12">
            <el-row>

              <!-- Note Label -->
              <el-col :sm="10">
                <p class="am-data">{{ $root.labels.note }}:</p>
              </el-col>

              <!-- Note Value -->
              <el-col :sm="14">
                <p class="am-value">{{ app.internalNotes }}</p>
              </el-col>

            </el-row>
          </el-col>
        </el-row>

      </el-row>
    </div>

  </div>
</template>

<script>
  import entitiesMixin from '../../../js/common/mixins/entitiesMixin'

  export default {
    mixins: [entitiesMixin],

    props: [
      'app',
      'options'
    ],

    data () {
      return {}
    },

    methods: {
      getCustomFieldLabel (customField, customFieldId) {
        let savedCustomField = this.options.entities.customFields.find(customField => customField.id === parseInt(customFieldId))

        return typeof savedCustomField !== 'undefined' ? savedCustomField.label.trim() : customField.label.trim()
      },

      getCustomFieldValue (value) {
        return Array.isArray(value) ? value.join('; ') : value
      },

      showCustomField (value) {
        if (Array.isArray(value)) {
          return value.length > 0
        }

        return !!value
      }
    }
  }
</script>