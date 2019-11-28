// reducers hold the store's state (the initialState object defines it)
// reducers also handle plain object actions and modify their state (immutably) accordingly
// this is the only way to change the store's state
// the other exports in this file are selectors, which is business logic that digests parts of the store's state
// for easier consumption by views

import _ from 'lodash';
import * as types from './actionTypes';
import Immutable from 'seamless-immutable';

const guestUser = {
    id: 0,
    sub: '',
    name: 'Guest',
    roles: [],
    iat: 0,
    exp: 0
};

const initialState = Immutable({
    menu : [],
    ingr : [],
    jwt  : '',
    data: {
        email : 'mail@orderpizza.dp.ua',
        adr   : 'г. Днепр, пр. Гагарина, 8ж',
        phone : '+380 93 713 5868',
        order : '',
        telegram : ''
    },
    user: guestUser,
    order: null,
    orderError: ''
});

export default function reduce(state = initialState, action = {}) {
    let prev = {};
    switch (action.type) {
        case types.MENU_FETCHED:
            prev = Object.assign({}, state.data);
            return state.merge({
                menu: action.menu,
                ingr: action.ingr,
                data: Object.assign(prev, action.data),
            });
        case types.USER_LOGGED:
                prev = Object.assign({}, state.data);
            return state.merge({
                user: action.user,
                jwt:  action.jwt,
                data: Object.assign(prev, action.data),
                ingr: action.ingr,
            });
        case types.USER_LOGGED_OUT:
            return state.merge({
                user: guestUser,
            });

        case types.ORDER_BEGIN:
            return state.merge({
                order: 0,
                orderError: ''
            });

        case types.ORDER_SENT:
            return state.merge({
                order: action.oid
            });

        case types.ORDER_RESET:
            return state.merge({
                order: null
            });

        case types.ORDER_ERROR:
            return state.merge({
                order: null,
                orderError: action.status
            });

        default:
            return state;
    }
}

// selectors

export function getMenu(state) {
    return state.backend.menu;
}

export function getIngridients(state) {
    return state.backend.ingr;
}

export function getContactsData(state) {
    return state.backend.data;
}

export function getUser(state) {
    // verify jwt ?
    return state.backend.user;
}

export function getOrder(state) {
    return state.backend.order;
}

export function getOrderError(state) {
    return state.backend.orderError;
}