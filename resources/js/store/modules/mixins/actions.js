export function load(context) {
    if (!makeDataUrl(context)) {
        return
    }

    return axios
        .get(makeDataUrl(context), {
            params: { query: context.getters.getQueryFilter },
        })
        .then(response => {
            context.commit('mutateSetData', response.data)
        })
}

export function save(context, payload) {
    const url =
        payload === 'create'
            ? makeStoreUrl(context)
            : makeUpdateUrl(context) + '/' + context.state.form.fields.id

    return context.state.form
        .post(url, context.state.form.fields)
        .then(response => {
            context.dispatch('load')
        })
}

export function clearForm(context) {
    dd('clearForm')
    set_null(context.state.form.fields)
}

export function mutateSetQueryFilterText(context, payload) {
    let data = context.state.data

    data.filter.text = payload

    data.links.pagination.current_page = 1

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
