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

const injection = {
    modules: [],
};

function init(Vue) {
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
            const imports = [];
            const informations = [];
            const scripts = respone.data.data.scripts;
            Object.keys(scripts).forEach(index => {
                const script = scripts[index];
                imports.push(loadScript(index, script.link));
                informations.push(script);
            });
            Object.keys(respone.data.data.stylesheets).forEach(index => {
                const stylesheet = respone.data.data.stylesheets[index];
                loadStylesheet(stylesheet);
            });
            Promise.all(imports).then(data => {
                window.console.log(data);
                injection.extensions = [];
                injection.modules = [];
                Object.keys(data).forEach(index => {
                    window.console.log(informations[index].type);
                    switch (informations[index].type) {
                        case 'extension':
                            if (data[index] && data[index].default) {
                                window.console.log(data[index].default);
                                injection.extensions.push(data[index].default);
                            } else {
                                window.console.warn(`插件[${informations[index].name}]加载失败！`);
                            }
                            break;
                        case 'module':
                            if (data[index] && data[index].default) {
                                injection.modules.push(data[index].default);
                            } else {
                                window.console.warn(`模块[${informations[index].name}]加载失败！`);
                            }
                            break;
                        default:
                            window.console.error('不支持的注入类型！');
                            break;
                    }
                });
                window.console.log(injection.extensions);
                window.console.log(injection.modules);
                init(Vue);
                injection.loading.finish();
                injection.notice.open({
                    title: '加载模块和插件成功！',
                });
            });
            if (respone.data.data.debug) {
                store.commit('debug', true);
            }
        }).catch(() => {
            init(Vue);
        });
    } else {
        init(Vue);
    }
}

export const trans = t;

export default Object.assign(injection, {
    install,
});