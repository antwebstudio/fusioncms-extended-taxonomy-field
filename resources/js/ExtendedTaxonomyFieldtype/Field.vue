<template>
    <div>
        <ui-field-group
            :label="field.name"
            :fieldId="`${field.handle}-field`"
            :name="field.handle"
            :inline="false"
            :help="field.help"
            :required="field.required"
            :has-error="hasError(field.handle)"
            :error-message="errorMessage(field.handle)"
            >

            <treeselect v-model="model" :multiple="field.settings.multiple" :options="treeSelectOptions(taxonomy.terms)" v-if="taxonomy.terms && taxonomy.terms.length > 0" />

            <p v-else-if="showAddNew" class="text-sm leading-none">Add a {{ singular }} below.</p>

            <div class="border-t pt-6" v-if="form && showAddNew">
                <ui-input-group
                    class="mb-2"
                    :name="term + '_name'"
                    :placeholder="'New ' + singular + ' name...'"
                    @keyup.enter.native="submit"
                    required
                    :has-error="form.errors.has('name')"
                    :error-message="form.errors.get('name')"
                    v-model="form.name">
                </ui-input-group>
                <ui-button @click.prevent="submit">Add {{ singular }}</ui-button>
            </div>
        
        </ui-field-group>
    </div>
</template>

<script>
    import pluralize from 'pluralize'
    import Form from '@/services/Form'
    import FieldMixin from '@/mixins/fieldtypes/field'
    // import the component
    import Treeselect from '@riophae/vue-treeselect'
    // import the styles
    import '@riophae/vue-treeselect/dist/vue-treeselect.css'

    export default {
        name: 'extended-taxonomy-fieldtype',

        mixins: [FieldMixin],
        
        components: { Treeselect },

        data() {
            return {
                taxonomy: {},
                form: false,
            }
        },

        computed: {
            term() {
                if (this.taxonomy.name) {
                    return this.taxonomy.name.toLowerCase()
                } else {
                    return 'terms'
                }
            },

            singular() {
                return pluralize.singular(this.term)
            },
            
            showAddNew() {
                if (this.field && this.field.settings) {
                    return this.field.settings.showAddNew
                }
            }
        },

        methods: {
            treeSelectOptions(terms) {
                if (!terms) return [];
                
                return terms.filter((term) => {
                        return ! term.parent_category || term.parent_category.length == 0
                    }).map((term) => {
                        if (term.children_category && term.children_category.length > 0) {
                            return {
                                id: term.id,
                                label: term.name,
                                children: term.children_category.map((term) => {
                                    return {
                                        id: term.id,
                                        label: term.name,
                                    }
                                }).sort((a, b) => a.label.localeCompare(b.label))
                            }
                        } else {
                            return {
                                id: term.id,
                                label: term.name,
                            }
                        }
                    }).sort((a, b) => a.label.localeCompare(b.label))
            },
            submit() {
                this.form.post(`/api/taxonomies/${this.taxonomy.id}/terms`).then((response) => {
                    toast('Term saved successfully', 'success')

                    this.fetchTaxonomy()
                    this.resetForm()
                }).catch((response) => {
                    toast(response.message, 'failed')
                })
            },

            resetForm() {
                this.form = new Form({
                    name: '',
                    slug: '',
                    status: 1,
                })
            },

            fetchTaxonomy() {
                axios.get(`/api/taxonomies/${this.field.settings.taxonomy}`).then((response) => {
                    this.taxonomy = response.data.data
                })
            }
        },

        mounted() {
            this.fetchTaxonomy()
            this.resetForm()

            this.model = (_.isObject(this.value) ? _.map(this.value, 'id') : this.value) || []
        }
    }
</script>