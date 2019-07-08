// actions are where most of the business logic takes place
// they are dispatched by views or by other actions
// there are 3 types of actions:
//  async thunks - when doing asynchronous business logic like accessing a service
//  sync thunks - when you have substantial business logic but it's not async
//  plain object actions - when you just send a plain action to the reducer

import _ from 'lodash';
import * as types from './actionTypes';
import axios from 'axios';

export function fetchMenu() {
    return (dispatch, getState) => {
        try {
            axios.get('/menu')
                .then(function (response) {
                    // handle success
                    const data = response.data.data;
                    const menu = response.data.menu;
                    //   console.log(data);
                    dispatch({ type: types.MENU_FETCHED, menu, data });
                })
                .catch(function (error) {
                    // handle error
                    console.log(error);
                })
                .finally(function () {
                    // always executed
                });
        } catch (error) {
            console.error(error);
        }
    };
}
