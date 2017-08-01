import {
    mixinAxios,
    mixinBoard,
    mixinComponent,
    mixinDebug,
    mixinExtension,
    mixinLocal,
    mixinModule,
    mixinNavigation,
    mixinRouter,
    mixinSidebar,
    mixinUse,
} from '../mixes/injection';
import App from '../App.vue';
import store from '../stores';
import { t } from '../local';

const injection = {};

function loadScript(identification, url) {
    return new Promise((resolve, reject) => {
        const script = document.createElement('script');
        script.type = 'text/javascript';
        if (script.readyState) {
            script.onreadystatechange = () => {
                if (script.readyState === 'loaded' || script.readyState === 'complete') {
                    script.onreadystatechange = null;
                    resolve(window[identification]);
                    script.remove();
                }
            };
        } else {
            script.onload = () => {
                resolve(window[identification]);
                script.remove();
            };
        }
        script.onerror = () => {
            reject(Error(`${url} load error!`));
        };
        script.src = url;
        document.body.appendChild(script);
    });
}

function loadStylesheet(url) {
    return new Promise(() => {
        const stylesheet = document.createElement('link');
        stylesheet.rel = 'stylesheet';
        stylesheet.href = url;
        document.body.appendChild(stylesheet);
    });
}

function install(Vue) {
    mixinDebug(injection);
    mixinLocal(injection);
    mixinComponent(Vue, injection);
    mixinAxios(injection, Vue);
    const token = JSON.parse(window.localStorage.getItem('token'));
    if (token && token.access_token && token.refresh_token) {
        Vue.http.defaults.headers.common.Accept = 'application/json';
        Vue.http.defaults.headers.common.Authorization = `Bearer ${token.access_token}`;
        injection.notice.open({
            title: '正在加载模块和插件...',
        });
        injection.loading.start();
        injection.http.post(`${window.api}/administration/info`).then(respone => {
            window.console.log(respone);
            const imports = [];
            Object.keys(respone.data.data.scripts).forEach(index => {
                const script = respone.data.data.scripts[index];
                imports.push(loadScript(index, script));
            });
            Object.keys(respone.data.data.stylesheets).forEach(index => {
                const stylesheet = respone.data.data.stylesheets[index];
                loadStylesheet(stylesheet);
            });
            Promise.all(imports).then(modules => {
                injection.modules = modules;
                mixinBoard(injection);
                mixinNavigation(injection);
                mixinRouter(injection);
                mixinSidebar(injection);
                mixinUse(injection);
                mixinExtension(injection);
                mixinModule(injection);
                injection.vue = new Vue({
                    el: '#app',
                    router: injection.router,
                    store,
                    template: '<App/>',
                    components: {
                        App,
                    },
                });
                injection.loading.finish();
                injection.notice.open({
                    title: '加载模块和插件成功！',
                });
            });
        });
    } else {
        mixinBoard(injection);
        mixinNavigation(injection);
        mixinRouter(injection);
        mixinSidebar(injection);
        mixinUse(injection);
        mixinExtension(injection);
        mixinModule(injection);
        injection.vue = new Vue({
            el: '#app',
            router: injection.router,
            store,
            template: '<App/>',
            components: {
                App,
            },
        });
    }
}

export const trans = t;

export default Object.assign(injection, {
    install,
});