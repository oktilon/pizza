// reducers hold the store's state (the initialState object defines it)
// reducers also handle plain object actions and modify their state (immutably) accordingly
// this is the only way to change the store's state
// the other exports in this file are selectors, which is business logic that digests parts of the store's state
// for easier consumption by views

import _ from 'lodash';
import * as types from './actionTypes';
import Immutable from 'seamless-immutable';

const initialState = Immutable({
    cart : [],
});

export default function reduce(state = initialState, action = {}) {
    var id, has, cart;
    switch (action.type) {
        case types.ITEM_ADDED:
            id = action.data.id;
            has = false;
            cart = state.cart.map(it => {
                if(it.id == id) has = true;
                return it.id == id ? {
                    cnt: it.cnt + 1,
                    id: id,
                    prod: action.data.prod,
                    price: action.data.price
                } : it;
            });
            if(!has) {
                cart = cart.concat([{
                    cnt: 1,
                    id: id,
                    prod: action.data.prod,
                    price: action.data.price
                }]);
            }
            return state.merge({
                cart: cart
            });
          
        case types.ITEM_REMOVED:
            id = action.data.id;
            has = false;
            cart = _.filter(
                _.map(state.cart, it => {
                    return it.id == id ? null : it;
                }), x => x != null);
            return state.merge({
                cart: cart
            });
        default:
            return state;
    }
}

// selectors

export function getCart(state) {
    return state.cart.cart;
}

export function getCartCount(state) {
    return _.reduce(state.cart.cart, (sum, it) => sum + it.cnt, 0);
}