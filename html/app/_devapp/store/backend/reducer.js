// reducers hold the store's state (the initialState object defines it)
// reducers also handle plain object actions and modify their state (immutably) accordingly
// this is the only way to change the store's state
// the other exports in this file are selectors, which is business logic that digests parts of the store's state
// for easier consumption by views

import _ from 'lodash';
import * as types from './actionTypes';
import Immutable from 'seamless-immutable';

const initialState = Immutable({
    menu : [],
    data: {
        email: 'mail@orderpizza.dp.ua',
        adr: 'г. Днепр, пр. Гагарина, 8ж',
        phone: '+380 93 713 5868'
    },
    user: {
        id: 0,
        name: 'Guest'
    }
});

export default function reduce(state = initialState, action = {}) {
    switch (action.type) {
        case types.MENU_FETCHED:
            return state.merge({
                menu: action.menu,
                data: action.data,
            });
        case types.USER_LOGGED:
            return state.merge({
                user: action.user,
            });
        default:
            return state;
    }
}

// selectors

export function getMenu(state) {
    return state.backend.menu;
}

export function getContactsData(state) {
    return state.backend.data;
}

export function getUser(state) {
    return state.backend.user;
}