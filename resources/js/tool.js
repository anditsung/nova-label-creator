import NovaSidebarMenu from 'nova-sidebar-menu'

Nova.booting((Vue, router, store) => {
    router.addRoutes([
        {
            name: 'labels',
            path: '/labels',
            component: require('./components/nova-label.vue')
        }
    ])

    Vue.component('nova-sidebar', NovaSidebarMenu)
})
