import Vue from 'vue'
import Vuex from 'vuex'
import axios from 'axios'
import router from './router';

Vue.use(Vuex)

export default new Vuex.Store({
    state: {
        accessToken: null,
        loggingIn: false,
        loginError: null
    },
    mutations: {
        loginStart: state => state.loggingIn = true,
        loginStop: (state, errorMessage) => {
            state.loggingIn = false;
            state.loginError = errorMessage;
        },
        updateAccessToken: (state, accessToken) => {
            state.accessToken = accessToken;
        },
        logout: (state) => {
            state.accessToken = null;
        }
    },
    actions: {
        fetchAccessToken({ commit }) {
            commit('updateAccessToken', localStorage.getItem('accessToken'));
        },
        logout({ commit }) {
            localStorage.removeItem('accessToken');
            commit('logout');
            router.push('/login');
        }
    }
})