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
  switch (action.type) {
    case types.ITEM_SELECTED:
      const id = action.item.id;
      const name = action.item.title;
      let upd = state.cart.slice();
      if(_.has(upd, id)) {
        upd[id].count++;
      } else {
        upd.concat({
          count: 1,
          name: name
        });
      }
      return state.merge({
        cart: upd
      });
      
    case types.ITEM_REMOVED:
      return state.merge({
        cart: action.data
      });
    default:
      return state;
  }
}

// selectors

export function getCart(state) {
  return state.cart;
}

export function getCartCount(state) {
  return state.cart.length;
}