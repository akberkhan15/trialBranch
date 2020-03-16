import Vue from 'vue';
import VueRouter from 'vue-router';

// Login Module pages
import Home from './components/Home.vue';
import Login from './components/Login.vue';

// Routes
Vue.use(VueRouter)

const router = new VueRouter({
    routes: [
        {
            name: 'Home',
            path: '/',
            component: Home
        },
        {
            name: 'Login',
            path: '/login',
            component: Login
        },
    ]
});

// router.beforeEach((to, from, next) => {
//     console.log(to);
//     var param = new URL(location.href).searchParams.get("param");
//     var authUser = localStorage.getItem('token');
//     if (to.meta.auth) {
//         if (authUser) {
//             next()
//         } else {
//             next('/login')
//         }
//     }
//     else if (!to.meta.auth && authUser) {

//         console.log(param);
//         if (param != null) {
//             localStorage.clear();
//             iziToast.show({
//                 title: "Success",
//                 message: param,
//                 backgroundColor: "#08b89d",
//                 color: "#FFFFFF",
//                 progressBar: true,
//                 icon: "fa fa-check",
//                 iconColor: "#FFFFFF",
//                 position: "topCenter",
//                 titleColor: "#FFFFFF",
//                 messageColor: "#FFFFFF",
//                 showIco: true
//             });
//             next('/')
//         }
//         else if (localStorage.getItem('is_freeze_account') === 'true') {
//             next("/selectplan");
//         }
//         else if (localStorage.getItem('is_flagged') === 'true') {
//             next("/otp");
//         } else if (localStorage.getItem('is_pending_verification') === 'true') {
//             next('/confirmemail');
//         }
//         else if (localStorage.getItem('is_trial_period_account') === 'true') {
//             next('/dashboard');
//         } else if (localStorage.getItem('is_freeze_account') === 'true') {
//             next("/selectplan");
//         } else if (localStorage.getItem('is_unconnected_account') === 'true') {
//             next("/mwsauthorization");
//         }
//     }
//     else {
//         next()
//     }
// })

export default router