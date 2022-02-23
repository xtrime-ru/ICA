import Vue from 'vue'
import Vuetify from 'vuetify/lib'

Vue.use(Vuetify)

export default new Vuetify({
    icons: {
        iconfont: 'mdi',
    },
    treeShake: true,
    theme: {
        dark: true,
        themes : {
            dark: {
                primary: '#FFC107',
            },
        },
    },
})
