/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-01-17 14:16
 */

import Vue from 'vue';

export const debug = (state, payload) => {
    state.debug = payload;
};

export const setting = (state, payload) => {
    state.setting = payload;
};

export const single = (state, payload) => {
    Vue.set(state.setting, payload.key, payload.value);
};

export const title = (state, payload) => {
    state.title = payload;
    global.document.title = payload;
};

export const token = (state, payload) => {
    window.localStorage.setItem('token', JSON.stringify(payload));
    state.token = payload;
};