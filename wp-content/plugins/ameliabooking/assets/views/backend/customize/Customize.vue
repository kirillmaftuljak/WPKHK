<template>
  <div class="am-wrap">
    <div id="am-customize" class="am-body">

      <!-- Page Header -->
      <page-header
          :addNewCustomFieldBtnDisplay="addNewCustomFieldBtnDisplay"
          @newCustomFieldBtnClicked="showDialogNewCustomFields()"
      >
      </page-header>

      <!-- Tabs -->
      <div class="am-customize am-section">
        <el-tabs v-model="activeTab">
        <el-tab-pane :label="$root.labels.colors_and_fonts" name="customize">
          <BlockLite/>

          <!-- Customize Settings -->
          <div class="am-customize-settings-preview" :class="{'am-lite-container-disabled': $root.isLite}">
            <el-row class="am-flexed">

              <!-- Customize Settings Inputs -->
              <el-col :lg="6" class="">
                <div class="am-customize-settings">

                  <!-- Primary Color -->
                  <div class="am-setting">
                    <p>{{ $root.labels.primary_color }}</p>
                    <el-color-picker v-model="customization.primaryColor"></el-color-picker>
                  </div>

                  <!-- Primary Gradient -->
                  <div class="am-setting">
                    <p>{{ $root.labels.primary_gradient }}</p>
                    <div class="am-gradient-picker">
                      <el-color-picker v-model="customization.primaryGradient1"></el-color-picker>
                      <el-color-picker v-model="customization.primaryGradient2"></el-color-picker>
                    </div>
                  </div>

                  <!-- Text Color -->
                  <div class="am-setting">
                    <p>{{ $root.labels.text_color }}</p>
                    <el-color-picker v-model="customization.textColor"></el-color-picker>
                  </div>

                  <!-- Text Color On Background -->
                  <div class="am-setting">
                    <p>{{ $root.labels.text_color_on_background }}</p>
                    <el-color-picker v-model="customization.textColorOnBackground"></el-color-picker>
                  </div>

                  <!-- Font -->
                  <div class="am-setting">
                    <p>{{ $root.labels.font }}</p>
                    <el-select
                        v-model="customization.font"
                        value-key="customer.id"
                        placeholder=""
                    >
                      <el-option
                          v-for="font in fonts"
                          :key="font.id"
                          :label="font.name"
                          :value="font.value"
                      >
                      </el-option>
                    </el-select>
                  </div>

                  <!-- Reset & Save Buttons -->
                  <div class="am-actions">

                    <!-- Reset Button -->
                    <el-button @click="resetSettings()">{{ $root.labels.reset }}</el-button>

                    <!-- Save Button -->
                    <el-button
                        @click="saveSettings()"
                        :loading="loadingButton"
                        type="primary"
                    >
                      {{ $root.labels.save }}
                    </el-button>

                  </div>

                </div>
              </el-col>

              <!-- Preview Fonts Import -->
              <link href="https://fonts.googleapis.com/css?family=Lato:300,400|Roboto:300,400,500,700" rel="stylesheet">

              <!-- Customize Preview -->
              <el-col :lg="18" class="">
                <div id="am-step-booking" class="am-active-picker" :style="{ 'fontFamily': customization.font }">

                  <!-- Select Service -->
                  <div class="am-select-service">
                    <p :style="{ 'color': customization.textColor }">
                      {{ $root.labels.please_select_service }}:
                    </p>

                    <!-- Booking Form -->
                    <el-form label-position="top">

                      <!-- Service -->
                      <el-form-item
                          :label="$root.labels.service + ':'"
                          :style="{ 'color': customization.textColor }"
                      >
                        <el-select
                            v-model="service"
                            placeholder=""
                            :disabled=true
                        >
                          <el-option
                              :key="0"
                              :label="$root.labels.any_service"
                              :value="0"
                          >
                          </el-option>
                        </el-select>
                      </el-form-item>

                      <!-- Employee -->
                      <el-form-item
                          :label="$root.labels.employee + ':'"
                          :style="{ 'color': customization.textColor }"
                      >
                        <el-select
                            v-model="employee"
                            placeholder=""
                            :style="{ 'color': customization.textColor}"
                            :disabled=true
                        >
                          <el-option
                              :key="0"
                              :label="$root.labels.any_employee"
                              :value="0"
                          >
                          </el-option>
                        </el-select>
                      </el-form-item>

                      <!-- Bringing anyone with you -->
                      <el-form-item label="">
                        <el-row>

                          <el-col :span="18" class="am-bringing-anyone">
                        <span :style="{ 'color': customization.textColor }">
                          {{ $root.labels.bringing_anyone_with_you }}
                        </span>
                          </el-col>

                          <el-col :span="6" class="am-align-right">
                            <div role="switch"
                                 class="el-switch is-checked"
                                 aria-checked="true">
                              <input type="checkbox" name="" true-value="true" class="el-switch__input">
                              <span class="el-switch__core"
                                    style="width: 40px;"
                                    :style="{ 'background-color': customization.primaryColor, 'border-color': customization.primaryColor }"
                              >
                            <span class="el-switch__button" style="transform: translate3d(20px, 0px, 0px);"></span>
                          </span>
                            </div>
                          </el-col>

                        </el-row>
                      </el-form-item>

                      <!-- Add extra -->
                      <div class="am-add-element" :style="{ 'color': customization.primaryColor }">
                        <i class="el-icon-plus"></i> {{ $root.labels.add_extra }}
                      </div>

                    </el-form>
                  </div>

                  <!-- Pick Date & Time -->
                  <div
                      class="am-select-date"
                      :style="{'background': 'linear-gradient(135deg, '+ customization.primaryGradient1 +' 0%, '+ customization.primaryGradient2 +' 100%)'}"
                  >

                    <p :style="{ 'color': customization.textColorOnBackground }">
                      {{ $root.labels.pick_date_and_time }}:
                    </p>

                    <!-- Datepicker -->
                    <v-date-picker
                        v-model="selectedDate"
                        mode="single"
                        id="am-calendar-picker"
                        class='am-calendar-picker'
                        :show-day-popover=false
                        :is-expanded=true
                        :is-inline=true
                        :style="{ 'color': this.customization.textColorOnBackground, 'fontFamily': customization.font }"
                        :formats="vCalendarFormats"
                    >
                    </v-date-picker>

                    <!-- Continue -->
                    <div class="am-button-wrapper">
                      <button
                          type="button"
                          class="el-button el-button--default"
                          :style="{ 'background-color': '#FFFFFF' }"
                      >
                        <span :style="{ 'color': customization.primaryColor}">{{ $root.labels.continue }}</span>
                      </button>
                    </div>

                  </div>

                </div>
              </el-col>

            </el-row>
          </div>

        </el-tab-pane>

        <el-tab-pane :label="$root.labels.custom_fields" name="customFields">
          <BlockLite/>

          <!-- Custom Fields -->
          <custom-fields
              :dialogCustomFields="dialogCustomFields"
              @closeDialogCustomFields="closeDialogCustomFields"
          >
          </custom-fields>

        </el-tab-pane>
      </el-tabs>
      </div>

      <DialogLite/>

      <!-- Help Button -->
      <el-col :md="6" class="">
        <a class="am-help-button" :href="needHelpPage" target="_blank">
          <i class="el-icon-question"></i> {{ $root.labels.need_help }}?
        </a>
      </el-col>

    </div>
  </div>
</template>

<script>
  import PageHeader from '../parts/PageHeader.vue'
  import dateMixin from '../../../js/common/mixins/dateMixin'
  import notifyMixin from '../../../js/backend/mixins/notifyMixin'
  import imageMixin from '../../../js/common/mixins/imageMixin'
  import ElButton from '../../../../node_modules/element-ui/packages/button/src/button.vue'
  import CustomFields from './customfields/CustomFields.vue'

  export default {

    mixins: [dateMixin, notifyMixin, imageMixin],

    data () {
      return {
        activeTab: 'customize',
        customization: this.$root.settings.customization,
        dialogCustomFields: false,
        employee: this.$root.labels.any_employee,
        fonts: [{id: 1, name: 'Roboto', value: 'Roboto, sans-serif'}, {id: 2, name: 'Lato', value: 'Lato'}],
        loadingButton: false,
        selectedDate: this.$moment().toDate(),
        service: '',
        switchValue: true
      }
    },

    updated () {
      this.handleResize()
    },

    created () {
      window.addEventListener('resize', this.handleResize)
    },

    mounted () {
      this.handleResize()
      this.inlineSVG()
    },

    methods: {
      saveSettings: function () {},

      resetSettings: function () {},

      handleResize () {
        let amContainer = document.getElementById('am-step-booking')
        let amContainerWidth = amContainer.offsetWidth
        if (amContainerWidth < 768) {
          amContainer.classList.add('am-mobile-collapsed')
        } else {
          amContainer.classList.remove('am-mobile-collapsed')
        }
      },

      showDialogNewCustomFields () {
        this.dialogCustomFields = true
      },

      closeDialogCustomFields () {
        this.dialogCustomFields = false
      }
    },

    computed: {
      addNewCustomFieldBtnDisplay () {
        return this.activeTab === 'customFields'
      },

      needHelpPage () {
        return this.activeTab === 'customize'
          ? 'https://wpamelia.com/customize-design/' : 'https://wpamelia.com/custom-fields/'
      }
    },

    components: {
      ElButton,
      PageHeader,
      CustomFields
    }
  }
</script>