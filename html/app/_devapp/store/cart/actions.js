// actions are where most of the business logic takes place
// they are dispatched by views or by other actions
// there are 3 types of actions:
//  async thunks - when doing asynchronous business logic like accessing a service
//  sync thunks - when you have substantial business logic but it's not async
//  plain object actions - when you just send a plain action to the reducer

import _ from 'lodash';
import * as types from './actionTypes';

export function addItem(prod, price) {
    return (dispatch) => {
        try {
            const { id } = price;
            dispatch({ type:types.ITEM_ADDED, id, prod, price })
        } catch (error) {
            console.error(error);
        }
    };
}

export function removeItem(price) {
    return (dispatch) => {
        try {
            const { id } = price;
            dispatch({ type:types.ITEM_REMOVED, id })
        } catch (error) {
            console.error(error);
        }
    };
}

export function emptyCart() {
    return (dispatch) => {
        try {
            dispatch({ type:types.CART_CLEANED })
        } catch (error) {
            console.error(error);
        }
    };
}
