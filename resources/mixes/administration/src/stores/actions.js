import injection from '../helpers/injection';

export const information = ({ commit }) => {
    commit('loading', true);
    injection.http.post(`${window.api}/administration/informations`).then(response => {
        const {
            navigation,
            pages,
        } = response.data.data;
        commit('navigation', navigation);
        commit('page', pages);
        const keys = Object.keys(navigation);
        if (keys.length > 0 && navigation[keys[0]].children) {
            commit('sidebar', navigation[keys[0]].children);
        }
        commit('loading', false);
    });
};

export const other = () => {
};