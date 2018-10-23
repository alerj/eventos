const loadDebounced = _.debounce(context => {
    context.dispatch('load')
}, 650)

export function load(context) {
    dd('context.state.dataUrl', context.state.dataUrl)
    if (!context.state.dataUrl) {
        return
    }

    return axios
        .get(context.state.dataUrl, { params: { query: context.getters.getQueryFilter } })
        .then(response => {
            dd('response.data', response.data)
            context.commit('mutateSetData', response.data)
        })
}

export function save(context, payload) {
    const url =
        payload === 'create' ? context.state.storeUrl : context.state.updateUrl

    return context.state.form
        .post(url, context.state.form.fields)
        .then(response => {
            context.dispatch('load')
        })
}

export function clearForm(context) {
    set_null(context.state.form.fields)
}

export function mutateSetQueryFilterText(context, payload) {
    let data = context.state.data

    data.filter.text = payload

    context.commit('mutateSetData', data)

    loadDebounced(context)
}

export function setCurrentPage(context, payload) {
    let data = context.state.data

    data.links.pagination.current_page = payload

    context.commit('mutateSetData', data)

    loadDebounced(context)
}

export function setPerPage(context, payload) {
    context.commit('mutateSetPerPage', payload)

    context.dispatch('load')

    context.dispatch('updateUserPerPage', payload)
}

export function updateUserPerPage(context, payload) {
    post('/api/v1/users/per-page/' + payload)
}

export function select(context, payload) {
    context.commit('mutateSetSelected', payload)

    context.commit('mutateFormData', payload)
}
