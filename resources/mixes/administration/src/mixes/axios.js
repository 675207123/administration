import axios from 'axios';
import store from '../stores';

export default function (injection, Vue) {
    let time = 0;
    axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
    axios.interceptors.request.use(configuration => {
        if (configuration.url !== `${window.api}/debug/publish`) {
            time = 1;
        }
        return configuration;
    }, error => {
        injection.console.log(error);
        return Promise.reject(error);
    });
    axios.interceptors.response.use(response => {
        if (store.state.debug && time === 1) {
            time = 0;
            axios.post(`${window.api}/debug/publish`).then(() => {
                injection.notice.open({
                    title: '调试模式下发布资源成功！',
                });
            });
        }

        return response;
    }, error => {
        injection.console.log(error.response);
        injection.console.log(error.response.data);
        const data = error.response.data;
        const response = error.response;
        if (response.status === 401) {
            injection.notice.error({
                title: '请重新登录！',
            });
            injection.vue.$router.push('/login');
        }
        if (response.status > 401 && response.status <= 500) {
            if (typeof data.message === 'object') {
                for (const p in data.message) {
                    injection.notice.error({
                        title: data.message[p],
                    });
                }
            } else {
                injection.notice.error({
                    title: data.message,
                });
            }
        }
        const dispatch = new Error('Error');
        dispatch.response = response;
        throw dispatch;
    });
    Object.defineProperties(injection, {
        http: {
            get() {
                return axios;
            },
        },
    });
    Object.defineProperties(Vue, {
        http: {
            get() {
                return axios;
            },
        },
    });
    Object.defineProperties(Vue.prototype, {
        $http: {
            get() {
                return axios;
            },
        },
    });
}