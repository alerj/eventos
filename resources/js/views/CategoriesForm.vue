<template>
    <div>
        <div class="py-2 text-center">
            <h2>{{ this.mode === 'create' ? 'Nova ' : 'Editar' }} Categoria</h2>
            <h2>&nbsp;{{ form.fields.name ? form.fields.name : '' }}</h2>
        </div>

        <div class="row justify-content-center">
            <div class="col-8">
                <form>
                    <div class="row">
                        <div class="col-12 mb-3">
                            <app-input
                                name="name"
                                label="Nome"
                                v-model="form.fields.name"
                                :required="true"
                                :form="form"
                                dusk="category-name"
                            ></app-input>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 text-right mb-3">
                            <button
                                @click.prevent="saveModel()"
                                class="btn btn-outline-secondary"
                                type="submit"
                                dusk="record-category-button"
                                :disabled="cannot('categories:modify')"
                            >
                                gravar
                            </button>

                            <router-link
                                to="/categories"
                                tag="button"
                                class="btn btn-success"
                            >
                                cancelar
                            </router-link>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import crud from './mixins/crud'
import categories from './mixins/categories'
import permissions from './mixins/permissions'

const service = { name: 'categories', uri: 'categories', performLoad: false }

export default {
    props: ['mode'],

    mixins: [crud, categories, permissions],

    data() {
        return {
            service: service,
        }
    },
}
</script>

<style></style>
