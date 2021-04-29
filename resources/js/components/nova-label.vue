<template>
    <loading-view :loading="loading">
        <form
            @submit="submitViaPrintLabel"
            autocomplete="off"
            ref="form"
        >
            <heading :level="1" class="mb-3">{{__("Labels")}}</heading>

            <card class="mb-8">
                <component
                    :class="{
                        'remove-bottom-border': index == fields.length - 1,
                    }"
                    v-for="(field, index) in fields"
                    :key="index"
                    :is="`form-${field.component}`"
                    :errors="validationErrors"
                    :field="field"
                />
            </card>

            <div class="flex items-center justify-end">
                <progress-button
                    dusk="print-button"
                    type="submit"
                    :disable="isWorking"
                    :processing="wasSubmittedViaPrintLabel"
                >
                    {{__("Print Label")}}
                </progress-button>
            </div>

        </form>
    </loading-view>
</template>

<script>
    import { Errors, InteractsWithResourceInformation } from 'laravel-nova'

    export default {
        mixins: [ InteractsWithResourceInformation ],

        data: () => ({
            loading: true,
            fields: [],
            submittedViaPrintLabel: false,
            validationErrors: new Errors(),
            isWorking: false,
        }),

        async created() {
            this.getFields()
        },

        methods: {
            async getFields() {
                this.fields = []

                const { data: fields } = await Nova.request().get(
                    '/nova-vendor/label-creator/fields'
                )

                this.fields = fields
                this.loading = false
            },

            async submitViaPrintLabel(e) {
                e.preventDefault()
                this.submittedViaPrintLabel = true
                // await this.printLabel()
                this.downloadLabel()
            },

            downloadLabel() {
                this.isWorking = true

                if (this.$refs.form.reportValidity()) {

                    Nova.request({
                        method: 'post',
                        url: '/nova-vendor/label-creator/labels',
                        data: this.createFormData(),
                    })
                        .then(response => {

                            let link = document.createElement('a')
                            link.href = response.data.download
                            link.download = response.data.name
                            document.body.appendChild(link)
                            link.click()
                            document.body.removeChild(link)

                            this.isWorking  = false
                        })
                        .catch(error => {
                            this.isWorking  = false

                            if (error.response.status == 422) {
                                this.errors = new Errors(error.response.data.errors)
                                Nova.error(this.__('There was a problem executing the action.'))
                            }
                        })

                }
            },

            async printLabel() {
                this.isWorking = true

                if (this.$refs.form.reportValidity()) {
                    try {

                        const response = await this.createRequest()

                        let url = '/nova-vendor/label-creator/labels/' + response.data

                        window.open(url, '_blank')

                        Nova.success(
                            this.__("Success"), {
                                type: 'success'
                            }
                        )

                        this.validationErrors = new Errors()
                        this.submittedViaPrintLabel = false
                        this.isWorking = false

                    } catch (error) {
                        this.submittedViaPrintLabel = false
                        this.isWorking = false

                        if(error.response.status == 422) {
                            this.validationErrors = new Errors(error.response.data.errors)
                            Nova.error(this.__('There was a problem submitting the form.'))
                        }
                    }
                }

                this.submittedViaPrintLabel = false
                this.isWorking = false
            },

            createRequest() {
                return Nova.request().post(
                    '/nova-vendor/label-creator/labels',
                    this.createFormData()
                )
            },

            createFormData() {
                return _.tap(new FormData(), formData => {
                    _.each(this.fields, field => {
                        field.fill(formData)
                    })
                })
            },
        },

        computed: {
            wasSubmittedViaPrintLabel() {
                return this.isWorking && this.submittedViaPrintLabel
            },
        }
    }
</script>
