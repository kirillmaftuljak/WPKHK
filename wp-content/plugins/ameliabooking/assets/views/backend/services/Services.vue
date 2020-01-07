<template>
  <div class="am-wrap">
    <div id="am-services" class="am-body">

      <!-- Page Header -->
      <page-header
          :servicesTotal="services.length"
          :categoriesTotal="categories.length"
          @newServiceBtnClicked="showDialogNewService()"
      >
      </page-header>

      <!-- Spinner -->
      <div class="am-spinner am-section" v-show="!fetched">
        <img :src="$root.getUrl+'public/img/spinner.svg'"/>
      </div>

      <!-- Services & Categories -->
      <div v-if="fetched" class="am-services-categories">
        <el-row class="am-flexed">
          <el-col :md="8" class="">
            <div class="am-categories-column am-section">
              <h2>{{ $root.labels.categories }}</h2>

              <!-- All Services Filter -->
              <div
                  class="am-category-item"
                  :class="{ active: Object.keys(activeCategory).length === 0 }"
                  @click="filterServices({})"
              >
                <h3>
                  <span class="am-category-title">
                    {{ $root.labels.all_services }}
                  </span>
                </h3>
              </div>

              <!-- Categories -->
              <draggable v-model="categories" :options="draggableOptions" @end="dropCategory">
                <transition-group name="list-complete">
                  <div
                      class="am-category-item"
                      :class="{ active: activeCategory.id === category.id }"
                      v-for="category in categories"
                      :key="category.id"
                      @click="filterServices(category)"
                  >

                    <!-- Reorder & Title -->
                    <h3 class="am-three-dots">

                      <!-- Reorder Button -->
                      <span class="am-drag-handle">
                        <img class="svg" width="20px" :src="$root.getUrl + 'public/img/burger-menu.svg'">
                      </span>

                      <!-- Title Input -->
                      <input
                          ref="input"
                          class="am-category-title"
                          :class="{ hidden: editedCategoryId !== category.id }"
                          v-model="editedCategoryName"
                          @keyup.enter="editCategoryName(category, $event)"
                      />

                      <!-- Title Text -->
                      <span class="am-category-title"
                            :class="{ hidden: editedCategoryId === category.id }"
                            v-model="category.name"
                      >
                        {{ category.name }}
                      </span>
                    </h3>

                    <div class="am-category-item-footer">
                      <el-row type="flex" align="middle">

                        <!-- Number of Services -->
                        <el-col :span="12">
                          <span class="service-count"> {{ category.serviceList.length }} {{ category.serviceList.length === 1 ? $root.labels.service : $root.labels.services }}</span>
                        </el-col>

                        <!-- Category Actions -->
                        <el-col :span="12" class="align-right category-actions">

                          <!-- Edit Category Name -->
                          <span
                              :class="{active: editedCategoryId === category.id }"
                              @click="editCategoryName(category, $event)"
                          >
                            <img class="svg edit" width="16px" :src="$root.getUrl+'public/img/edit.svg'">
                            <i class="el-icon-success done"></i>
                          </span>

                          <!-- Duplicate Category -->
                          <span @click="duplicateCategory(category)">
                            <img class="svg" width="16px" :src="$root.getUrl+'public/img/copy.svg'">
                          </span>

                          <!-- Delete Category -->
                          <span
                              v-if="$root.settings.capabilities.canDelete === true"
                              @click="handleCategoryDeleteConfirmation(category)"
                          >
                            <img class="svg" width="16px" :src="$root.getUrl+'public/img/delete.svg'">
                          </span>

                        </el-col>
                      </el-row>

                      <!-- Delete Confirmation -->
                      <el-collapse-transition>
                        <div class="am-confirmation" v-show="deleteConfirmation && categoryToDeleteId === category.id">
                          <p>{{ $root.labels.delete_category_confirmation }}?</p>
                          <el-alert
                              title=""
                              type="warning"
                              description=""
                              :closable="false"
                          >
                          </el-alert>
                          <div class="align-right">
                            <el-button size="small" @click="hideDeleteCategoryDialog()">
                              {{ $root.labels.cancel }}
                            </el-button>
                            <el-button size="small" @click="deleteCategory(category)" type="primary"
                                       :loading="loadingDeleteCategory">
                              {{ $root.labels.delete }}
                            </el-button>
                          </div>
                        </div>
                      </el-collapse-transition>

                    </div>

                  </div>
                </transition-group>
              </draggable>

              <!-- Add Category Button -->
              <el-button @click="addCategory" size="large" type="primary" class="am-dialog-create"
                         :loading="loadingAddCategory">
                <i class="el-icon-plus"></i> <span class="button-text">{{ $root.labels.add_category }}</span>
              </el-button>

            </div>
          </el-col>

          <el-col :md="16">
            <div class="am-services-column am-section">
              <el-row :gutter="16">
                <el-col :md="12">
                  <h2 v-show="Object.keys(activeCategory).length === 0">
                    {{$root.labels.all_services }}
                  </h2>
                  <h2 v-show="Object.keys(activeCategory).length !== 0">
                    {{ activeCategory.name }}
                  </h2>
                </el-col>
                <el-col :md="12" class="am-align-right">
                  <span class="am-sort-services-label">{{$root.labels.services_sorting}}</span>
                  <el-select v-model="sortingServices" clearable placeholder="Sort Services" class="am-sort-services" @change="changeServiceSorting">
                    <el-option
                        v-for="sortSelection in sortingServicesSelection"
                        :key="sortSelection.sortValue"
                        :label="sortSelection.sortName"
                        :value="sortSelection.sortValue">
                    </el-option>
                  </el-select>
                </el-col>
              </el-row>


              <!-- Empty State For Categories -->
              <div class="am-empty-state am-section" v-show="fetched && categories.length === 0">
                <img :src="$root.getUrl+'public/img/emptystate.svg'">
                <h2>{{ $root.labels.no_categories_yet }}</h2>
                <p>{{ $root.labels.click_add_category }}</p>
              </div>

              <!-- Empty State For Services -->
              <div class="am-empty-state am-section"
                   v-show="fetched && categories.length !== 0 && services.filter(item => item.visible).length === 0">
                <img :src="$root.getUrl+'public/img/emptystate.svg'">
                <h2>{{ $root.labels.no_services_yet }}</h2>
                <p>{{ $root.labels.click_add_service }}</p>
              </div>

              <!-- Services -->
              <div v-show="fetched && categories.length !== 0" class="am-services-grid">
                <el-row :gutter="16">
                  <el-col :md="24">

                    <draggable v-model="services" :options="draggableOptions" @end="dropService">
                      <div
                          class="am-service-card"
                          @click="showDialogEditService(index)"
                          :class="{'am-hidden-entity' : service.status === 'hidden'}"
                          v-for="(service, index) in services"
                          v-show="service.visible"
                      >
                        <span class="am-drag-handle">
                          <img class="svg" width="20px" :src="$root.getUrl + 'public/img/burger-menu.svg'">
                        </span>

                        <div class="am-service-photo">
                          <img :src="pictureLoad(service, false)" @error="imageLoadError(service, false)"/>
                          <span class="am-service-color" :style="bgColor(service.color)"></span>
                        </div>
                        <div class="am-service-data">
                          <el-row :gutter="16">
                            <el-col :md="12">
                              <h4>{{ service.name }}</h4>
                            </el-col>
                            <el-col :md="6">
                              <p>{{ $root.labels.duration }}: {{ secondsToNiceDuration(service.duration) }}</p>
                            </el-col>
                            <el-col :md="6">
                              <p>{{ $root.labels.price }}: {{ getFormattedPrice(service.price) }}</p>
                            </el-col>
                          </el-row>
                        </div>
                      </div>

                    </draggable>

                  </el-col>


                </el-row>
              </div>

            </div>
          </el-col>

        </el-row>
      </div>

      <!-- Button New -->
      <div v-if="categories.length > 0 && $root.settings.capabilities.canWrite === true"
           id="am-button-new"
           class="am-button-new"
      >
        <el-button id="am-plus-symbol"
           type="primary"
           icon="el-icon-plus"
           @click="showDialogNewService()"
        >
        </el-button>
      </div>

      <!-- Dialog Service -->
      <transition name="slide">
        <el-dialog
            class="am-side-dialog am-dialog-service"
            :visible.sync="dialogService"
            :show-close="false"
            v-if="dialogService"
        >
          <dialog-service
              :categories="categories"
              :passedService="service"
              :employees=options.employees
              :settings=options.settings
              :futureAppointments="futureAppointments"
              @saveCallback="saveServiceCallback"
              @duplicateCallback="duplicateServiceCallback"
              @closeDialog="dialogService = false"
          >
          </dialog-service>
        </el-dialog>
      </transition>

      <!-- Help Button -->
      <el-col :md="6" class="">
        <a class="am-help-button" href="https://wpamelia.com/services-and-categories/" target="_blank">
          <i class="el-icon-question"></i> {{ $root.labels.need_help }}?
        </a>
      </el-col>

      <DialogLite/>

    </div>
  </div>
</template>

<script>
  import Form from 'form-object'
  import DialogService from './DialogService.vue'
  import PageHeader from '../parts/PageHeader.vue'
  import Draggable from 'vuedraggable'
  import liteMixin from '../../../js/common/mixins/liteMixin'
  import settingsMixin from '../../../js/common/mixins/settingsMixin'
  import imageMixin from '../../../js/common/mixins/imageMixin'
  import dateMixin from '../../../js/common/mixins/dateMixin'
  import durationMixin from '../../../js/common/mixins/durationMixin'
  import priceMixin from '../../../js/common/mixins/priceMixin'
  import notifyMixin from '../../../js/backend/mixins/notifyMixin'

  export default {

    mixins: [liteMixin, imageMixin, dateMixin, durationMixin, priceMixin, notifyMixin, settingsMixin],

    data () {
      return {
        activeCategory: {},
        categories: [],
        categoryToDeleteId: null,
        count: 0,
        dialogService: false,
        deleteConfirmation: false,
        draggableOptions: {
          handle: '.am-drag-handle',
          animation: 150
        },
        editedCategoryId: 0,
        editedCategoryName: '',
        editedCategoryOldName: '',
        fetched: false,
        form: new Form(),
        futureAppointments: {},
        loadingAddCategory: false,
        loadingDeleteCategory: false,
        options: {
          employees: [],
          settings: {
            payments: {
              wc: []
            }
          }
        },
        service: null,
        services: [],
        sortingServices: this.$root.settings.general.sortingServices,
        sortingServicesSelection: [
          {
            sortName: this.$root.labels.services_sorting_name_asc,
            sortValue: 'nameAsc'
          },
          {
            sortName: this.$root.labels.services_sorting_name_desc,
            sortValue: 'nameDesc'
          },
          {
            sortName: this.$root.labels.services_sorting_price_asc,
            sortValue: 'priceAsc'
          },
          {
            sortName: this.$root.labels.services_sorting_price_desc,
            sortValue: 'priceDesc'
          },
          {
            sortName: this.$root.labels.services_sorting_custom,
            sortValue: 'custom'
          }
        ],
        svgLoaded: false
      }
    },

    created () {
      this.getOptions()
    },

    mounted () {

    },

    updated () {
      if (this.svgLoaded) this.inlineSVG()
      this.svgLoaded = true
    },

    methods: {
      changeServiceSorting () {
        switch (this.sortingServices) {
          case ('nameAsc'):
            this.services = this.services.sort((a, b) => (a.name > b.name) ? 1 : -1)
            break
          case ('nameDesc'):
            this.services = this.services.sort((a, b) => (a.name < b.name) ? 1 : -1)
            break
          case ('priceAsc'):
            this.services = this.services.sort((a, b) => (a.price > b.price) ? 1 : -1)
            break
          case ('priceDesc'):
            this.services = this.services.sort((a, b) => (a.price < b.price) ? 1 : -1)
            break
        }

        if (this.sortingServices !== 'custom') {
          this.updateServicesPositions(true)
        }
      },

      updateServicesPositions (notify) {
        let services = []

        let $this = this

        this.services.forEach(function (service, index) {
          service.position = index + 1

          let serviceSettings = JSON.parse(JSON.stringify(service.settings))

          if (serviceSettings.payments.wc.productId === $this.$root.settings.payments.wc.productId) {
            delete serviceSettings.payments.wc
          }

          services.push(Object.assign(JSON.parse(JSON.stringify(service)), {settings: JSON.stringify(serviceSettings)}))
        })

        this.$http.post(`${this.$root.getAjaxUrl}/services/positions`, {
          services: services,
          sorting: this.sortingServices
        }).then(() => {
          if (notify) {
            this.notify(this.$root.labels.success, this.$root.labels.services_positions_saved, 'success')
          }
        }).catch(() => {
          this.notify(this.$root.labels.error, this.$root.labels.services_positions_saved_fail, 'error')
        })
      },

      getOptions () {
        this.$http.get(`${this.$root.getAjaxUrl}/entities`, {params: {types: ['employees', 'appointments', 'categories', 'locations', 'settings']}})
          .then(response => {
            this.options.settings.payments.wc = response.data.data.settings.payments.wc

            this.setInitialEmployee(response.data.data.employees)

            this.services = []

            this.parseOptions(response)

            for (let i = 0; i < this.categories.length; i++) {
              this.services = this.services.concat(this.categories[i].serviceList)
            }

            this.services = this.services.sort((a, b) => ((a.position && b.position) ? a.position > b.position : a.name > b.name) ? 1 : -1)

            let $this = this

            this.services.forEach(function (service) {
              $this.setEntitySettings(service)
            })

            this.filterServices(this.activeCategory)

            let appointments = response.data.data.appointments['futureAppointments']

            for (let key in appointments) {
              let serviceId = appointments[key].serviceId
              let providerId = appointments[key].providerId

              if (!(serviceId in this.futureAppointments)) {
                this.futureAppointments[serviceId] = []
                this.futureAppointments[serviceId].push(providerId)
              } else if (this.futureAppointments[serviceId].indexOf(providerId) === -1) {
                this.futureAppointments[serviceId].push(providerId)
              }
            }

            this.fetched = true
          })
          .catch(e => {
            console.log(e.message)
            this.fetched = true
          })
      },

      parseOptions: function (response) {
        this.options.employees = response.data.data.employees.slice(0, 1)

        this.categories = response.data.data.categories

        this.categories.forEach(function (category) {
          category.serviceList.forEach(function (service) {
            service.extras = []
          })
        })
      },

      addCategory () {
        this.loadingAddCategory = true

        let newCategory = {
          status: 'visible',
          name: this.$root.labels.new_category,
          position: this.categories.length + 1
        }

        this.form.post(`${this.$root.getAjaxUrl}/categories`, newCategory)
          .then(response => {
            this.categories.push(response.data.category)
            this.editedCategoryId = response.data.category.id
            this.editedCategoryName = response.data.category.name
            this.loadingAddCategory = false

            let that = this
            window.setTimeout(function () {
              that.$refs.input[that.categories.indexOf(response.data.category)].focus()
            }, 0)
          })
          .catch(e => {
            this.notify(this.$root.labels.error, this.$root.labels.category_add_fail, 'error')
            this.loadingAddCategory = false
          })
      },

      updateCategory (category) {
        this.form.post(`${this.$root.getAjaxUrl}/categories/${category.id}`, category)
          .then(() => {
            this.notify(this.$root.labels.success, this.$root.labels.category_saved, 'success')
          })
          .catch(() => {
            category.name = this.editedCategoryOldName
            this.notify(this.$root.labels.error, this.$root.labels.category_saved_fail, 'error')
          })
      },

      updateCategoriesPositions (notify) {
        this.$http.post(`${this.$root.getAjaxUrl}/categories/positions`, {
          categories: this.categories
        }).then(() => {
          if (notify) {
            this.notify(this.$root.labels.success, this.$root.labels.categories_positions_saved, 'success')
          }
        }).catch(() => {
          this.notify(this.$root.labels.error, this.$root.labels.categories_positions_saved_fail, 'error')
        })
      },

      editCategoryName (category, event) {
        this.hideDeleteCategoryDialog()
        // If edit or save button is clicked
        if (event.currentTarget.className === '') {
          this.editedCategoryId = category.id
          this.editedCategoryName = category.name
          // Focus category name input
          let that = this
          window.setTimeout(function () {
            that.$refs.input[that.categories.indexOf(category)].focus()
          }, 0)
        } else {
          this.editedCategoryOldName = category.name
          if (category.name !== this.editedCategoryName) {
            category.name = this.editedCategoryName
            this.updateCategory(category)
          }
          this.editedCategoryId = this.editedCategoryName = null
        }
      },

      dropCategory (e) {
        if (e.newIndex !== e.oldIndex) {
          let that = this
          this.categories.forEach((category) => {
            category.position = that.categories.indexOf(category) + 1
          })
          this.updateCategoriesPositions(true)
        }
      },

      dropService (e) {
        if (e.newIndex !== e.oldIndex) {
          this.sortingServices = 'custom'
          this.updateServicesPositions(true)
        }
      },

      duplicateCategory (category) {
        let newCategory = Object.assign({}, category)
        delete newCategory.id
        newCategory.position = this.categories.length + 1
        this.svgLoaded = false

        this.form.post(`${this.$root.getAjaxUrl}/categories`, newCategory)
          .then(response => {
            this.categories.push(response.data.category)
            this.services = this.services.concat(response.data.category.serviceList)
            this.notify(this.$root.labels.success, this.$root.labels.category_duplicated, 'success')
          })
          .catch(e => {
            this.notify(this.$root.labels.error, this.$root.labels.category_add_fail, 'error')
          })
      },

      handleCategoryDeleteConfirmation (category) {
        this.categoryToDeleteId = category.id
        this.deleteConfirmation = true
        // Remove category name editing if it is enabled
        this.editedCategoryId = null
        this.editedCategoryName = ''
      },

      hideDeleteCategoryDialog () {
        this.categoryToDeleteId = null
        this.deleteConfirmation = false
      },

      deleteCategory (category) {
        this.loadingDeleteCategory = true

        this.$http.post(`${this.$root.getAjaxUrl}/categories/delete/` + this.categoryToDeleteId)
          .then(() => {
            // Delete category
            let index = this.categories.indexOf(category)
            this.categories.splice(index, 1)
            // Delete services
            this.services = this.services.filter(service => service.categoryId !== this.categoryToDeleteId)
            // Refresh displayed services if active category is deleted
            if (this.activeCategory.id === this.categoryToDeleteId) {
              this.activeCategory = {}
            }

            // Update categories positions
            for (let i = 0; i < this.categories.length; i++) {
              this.categories[i].position = i + 1
            }

            this.updateCategoriesPositions(false)
            this.loadingDeleteCategory = false
            this.notify(this.$root.labels.success, this.$root.labels.category_deleted, 'success')
          })
          .catch(() => {
            this.loadingDeleteCategory = false
            this.notify(this.$root.labels.error, this.$root.labels.categories_delete_fail, 'error')
          })
      },

      filterServices (category) {
        this.activeCategory = category
        this.services.forEach(function (service) {
          service.visible = service.categoryId === category.id || Object.keys(category).length === 0
        })
      },

      showDialogNewService () {
        this.service = this.getInitServiceObject()
        this.dialogService = true
      },

      showDialogEditService (index) {
        this.service = this.services[index]

        if (this.service.timeBefore === null) { this.service.timeBefore = '' }
        if (this.service.timeAfter === null) { this.service.timeAfter = '' }

        this.dialogService = true
      },

      duplicateServiceCallback (service) {
        this.service = service
        this.service.id = 0
        this.service.duplicated = true

        setTimeout(() => {
          this.dialogService = true
        }, 300)
      },

      bgColor (color) {
        return {'background-color': color}
      },

      saveServiceCallback () {
        this.dialogService = false
        this.getOptions()
      },

      getInitServiceObject () {
        return {
          id: 0,
          categoryId: '',
          color: '#1788FB',
          description: '',
          duration: '',
          providers: [],
          extras: [],
          maxCapacity: 1,
          minCapacity: 1,
          name: '',
          pictureFullPath: '',
          pictureThumbPath: '',
          price: 0,
          status: 'visible',
          timeAfter: '',
          timeBefore: '',
          bringingAnyone: true,
          show: true,
          applyGlobally: false,
          gallery: [],
          aggregatedPrice: true,
          settings: this.getInitEntitySettings(),
          position: 0
        }
      }

    },

    components: {
      PageHeader,
      Draggable,
      DialogService
    }

  }
</script>
