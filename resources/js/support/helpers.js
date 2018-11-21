window.dd = (...args) => {
    if (
        !window.laravel ||
        !window.laravel.environment ||
        window.laravel.environment.debug
    ) {
        console.log('[DEBUG] ---- ', ...args)
    }
}

window.is_url = str => {
    let pattern = new RegExp(
        '^(https?:\\/\\/)?' + // protocol
        '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.?)+[a-z]{2,}|' + // domain name
        '((\\d{1,3}\\.){3}\\d{1,3}))' + // OR ip (v4) address
        '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*' + // port and path
        '(\\?[;&a-z\\d%_.~+=-]*)?' + // query string
            '(\\#[-a-z\\d_]*)?$',
        'i',
    ) // fragment locator
    return pattern.test(str)
}

window.is_email = email => {
    let re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
    return re.test(String(email).toLowerCase())
}

window.initials = name => {
    let initials = name.match(/\b\w/g) || []

    return ((initials.shift() || '') + (initials.pop() || '')).toUpperCase()
}

window.closeModal = id => {
    jQuery(id).modal('hide')
}

window.confirm = (title, vue) => {
    return vue.$swal({
        icon: 'warning',
        title: title,
        dangerMode: true,
        buttons: {
            cancel: {
                text: 'sim',
                value: true,
                visible: true,
                className: 'btn-outline-secondary',
                closeModal: true,
            },
            confirm: {
                text: 'não',
                value: false,
                visible: true,
                className: 'btn-success',
                closeModal: true,
            },
        },
    })
}

window.post = (...args) => {
    return axios.post(...args)
}

window.get = (...args) => {
    return axios.get(...args)
}

window.object_get = (obj, descendants) => {
    const arr = descendants.split('.')

    arr.forEach(element => {
        if (!obj.hasOwnProperty(element)) {
            return null
        }

        obj = obj[element]
    })

    return obj
}

window.is_object = target => {
    return typeof target === 'object'
}

window.remove_empty_properties = obj => {
    let me = JSON.parse(JSON.stringify(obj))

    for (let propName in me) {
        if (me.hasOwnProperty(propName)) {
            if (
                me[propName] === null ||
                me[propName] === undefined ||
                (typeof me[propName] === 'string' && me[propName].trim() === '')
            ) {
                delete me[propName]
            }
        }
    }

    return me
}

window.logout = () => {
    axios.post('/logout').then(response => {
        window.location = '/'
    })
}

window.clone = object => {
    return JSON.parse(JSON.stringify(object))
}

window.set_object_values = (obj, val) => {
    Object.keys(obj).forEach(k => {
        if (obj[k] !== null && typeof obj[k] === 'object') {
            set_object_values(obj[k], val)
        } else {
            obj[k] = val
        }
    })
}

window.set_null = obj => {
    set_object_values(obj, null)
}

window.merge_objects = (target, ...sources) => {
    return Object.assign({}, target, ...sources)
}

window._ = require('lodash')

window.loadDebounced = _.debounce(context => {
    context.dispatch('load')
}, 650)

window.buildApiUrl = (uri, state) => {
    let url = '/api/v1/' + uri
    let hasNulls = false

    _.each(uri.match(/(\{.*?\})/g), param => {
        let elements = param.match(/(\w+)/g)

        let result = _.reduce(
            elements,
            (carry, value) => {
                carry =
                    carry && carry.hasOwnProperty(value) ? carry[value] : null

                if (carry === null) {
                    hasNulls = true
                }

                return carry
            },
            state,
        )

        url = url.replace(param, result)
    })

    return hasNulls ? null : url
}

window.makeDataUrl = context => {
    dd('makeDataUrl - context.state.service && context.state.service.uri - ',context.state.service && context.state.service.uri)
    return context.state.service && context.state.service.uri
        ? buildApiUrl(context.state.service.uri, context.rootState)
        : null
}

window.makeStoreUrl = context => {
    return context.state.service && context.state.service.uri
        ? buildApiUrl(context.state.service.uri, context.rootState)
        : null
}

window.makeUpdateUrl = context => {
    return context.state.service && context.state.service.uri
        ? buildApiUrl(context.state.service.uri, context.rootState)
        : null
}

window.findById = (data, id) => {
    return _.find(data.rows, item => {
        return item.id == id
    })
}

window.append_form_data = (FormData, data, name) => {
    name = name || ''
    if (typeof data === 'object') {
        $.each(data, (index, value) => {
            if (name === '') {
                append_form_data(FormData, value, index)
            } else {
                append_form_data(FormData, value, name + '[' + index + ']')
            }
        })
    } else {
        FormData.append(name, data)
    }
}

window.blob_to_base64 = (blob, callback) => {
    let reader = new FileReader()

    reader.onload = () => {
        let dataUrl = reader.result

        let base64 = dataUrl.split(',')[1]

        callback(base64)
    }

    reader.readAsDataURL(blob)
}

window.except = (list, id) => {
    return _.filter(list, item => {
        return !id || !item.id || item.id !== id
    })
}

window.extractFileNameFromResponse = (response, filename = 'eventos.pdf') => {
    var filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/

    var matches = filenameRegex.exec(response['headers']['content-disposition'])

    if (matches != null && matches[1]) {
        filename = matches[1].replace(/['"]/g, '')
    }

    return filename
}

window.flush_image_cache = imageUrl => {
    if (imageUrl && !imageUrl.includes('data:image/')) {
        imageUrl = imageUrl + '?' + Math.random()
    }

    return imageUrl
}

window.downloadPDF = fileUrl => {
    return axios({
        method: 'get',
        url: fileUrl,
        responseType: 'arraybuffer',
    }).then(response => {
        let blob = new Blob([response.data], { type: 'application/pdf' })

        let link = document.createElement('a')

        link.href = window.URL.createObjectURL(blob)

        link.download = extractFileNameFromResponse(response)

        link.click()
    })
}
