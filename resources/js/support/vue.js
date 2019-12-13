/**
 * Vue & Vuex
 */
window.Vue = require('vue')
window.Vuex = require('vuex')

/**
 * Vue Grecaptcha
 */
Vue.use(() => import('vue-recaptcha'), {
    sitekey: '6LchDyAUAAAAAPPYWb2ZcFQOh12bI88qpJjKei9J',
})

Vue.component('vue-recaptcha', () => import('vue-recaptcha'))

/**
 * Vue The Mask
 */
Vue.use(() => import('vue-the-mask'))

/**
 * SweetAlert
 */
Vue.use(() => import('vue-swal'))

/**
 * Vue Bootstrap
 */
import { ModalPlugin } from 'bootstrap-vue'
import { ButtonPlugin } from 'bootstrap-vue'
import { AlertPlugin } from 'bootstrap-vue'
Vue.use(AlertPlugin)
Vue.use(ModalPlugin)
Vue.use(ButtonPlugin)

/**
 * Autoload Vue components
 */
const file = require.context('../components/app/', true, /\.vue$/i)
file.keys().map(file => {
    const name = 'App' + _.last(file.split('/')).split('.')[0]

    return Vue.component(name, () =>
        import('../components/app/' + basename(file)),
    )
})

/**
 * VueSelect
 */
Vue.component('vue-select', () => import('vue-select'))

/**
 * Vue Croppa
 */
//Vue.use(() => import('vue-croppa'), { componentName: 'vue-croppa' })
import Croppa from 'vue-croppa'
Vue.use(Croppa, { componentName: 'vue-croppa' })

/**
 * Vue Clipboard
 */
import VueClipboard from 'vue-clipboard2'
Vue.use(VueClipboard)
