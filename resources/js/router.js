import Vue from 'vue'
import VueRouter from 'vue-router';

import Landing from './components/LandingPage.vue'
import Login from './components/login/LoginApp.vue'
import RootApp from './components/RootApp.vue'
import RootSAdmin from './components/RootSAdmin'
import RootAdmin from './components/RootAdmin'
import NotFound from './components/NotFound'
import SAdminDashboard from './components/SuperAdmin/AdminDashboard'
import HomeSAdmin from './components/SuperAdmin/pages/HomeApp.vue'
import UserSAdmin from './components/SuperAdmin/pages/UserApp.vue'
import ticketSAdmin from './components/SuperAdmin/pages/TicketAdmin.vue'

Vue.use(VueRouter)

const routes = [
    {path: '*', component: NotFound},
    {path: '/', component: RootApp,
        children: [
            {path: "/", component: Landing}
        ]
    },
    {path: '/superadmin', component: RootSAdmin,
        children: [
            {path: '/superadmin',component: SAdminDashboard,
                children: [
                    {path: '/superadmin', redirect: 'home'},
                    {path: 'home', component: HomeSAdmin},
                    {path: 'ticket', component: ticketSAdmin},
                    {path: 'user', component: UserSAdmin}
                ],
                meta: {requiresAuth:true}
            }
        ]
        },
        {path: '/admin', component: RootAdmin,
            children: [
                {path:'/admin'}
            ]
    } 

]


const router = new VueRouter({
    routes,
    hashbang: false,
    mode: 'history',
})

export default router;