import injection from '../helpers/injection';

export const information = ({ commit }) => {
    injection.http.post(`${window.api}/administration/informations`).then(response => {
        const {
            navigation,
        } = response.data.data;
        commit('navigation', navigation);
        const keys = Object.keys(navigation);
        if (keys.length > 0 && navigation[keys[0]].children) {
            commit('sidebar', navigation[keys[0]].children);
        }
    });
};

export const other = () => {
};