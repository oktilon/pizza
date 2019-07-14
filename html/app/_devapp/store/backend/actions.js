// actions are where most of the business logic takes place
// they are dispatched by views or by other actions
// there are 3 types of actions:
//  async thunks - when doing asynchronous business logic like accessing a service
//  sync thunks - when you have substantial business logic but it's not async
//  plain object actions - when you just send a plain action to the reducer

import _ from 'lodash';
import * as types from './actionTypes';
import * as typesCart from '../cart/actionTypes';
import axios from 'axios';
import cfg from '../../config';
import moment from 'moment';
import md5 from 'md5';

var r = require('jsrsasign');

const pubKey = `-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAmcWNN9lIyRt0E1hfRmhK
thX+waWdSOp6iwwxejl+ED27aP5KCoe4XDUXPXr7NQ4qSBjTTBM0sI7uMzC7c0PW
H0Sik0Zfe/YL1G9arlYkrVYxUjdfdLonyb4eB7Ze70jwK2lqlhswlPeTYbJrhV/i
AjXK+87tl1a+ymMX4rQzc6PxHdZbBHAiEPiRLieG/5q6rZXsaO03bJmkriZKs8K5
P6gcE1Kl+i0djuMgGa0m/tRZUWOH0jPlqbH2giIcvC2bv9RfwiqGYEMKVkcZRPH+
CGdKsjwV70WxjZIDvY+f9jl6m/k317KILxeqszP0SIzLU/p2xBoZNI3ZH32SxdGO
VQIDAQAB
-----END PUBLIC KEY-----`;

export function fetchMenu() {
    return (dispatch, getState) => {
        try {
            axios.get('/menu')
                .then(function (response) {
                    // handle success
                    const data = response.data.data;
                    const menu = response.data.menu;
                    const ingr = response.data.ingr;
                    //   console.log(data);
                    dispatch({
                        type: types.MENU_FETCHED, menu, data, ingr });
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

export function makeOrder(form, cart) {
    return (dispatch, getState) => {
        const m = moment();
        const dt = m.format(cfg.appFmt);
        const pd = {
            f: form,
            c: cart,
            d: m.unix()
        };
        const body = JSON.stringify(pd);
        const sign = md5(`${dt}${body}${cfg.appKey}`);

        dispatch({ type: types.ORDER_BEGIN });

        try {
            axios.post('/make/order', pd,{
                headers: {'Signature': sign},
            })
                .then(function (response) {
                    const { oid } = response.data;
                    // handle success
                    if(response.data.status == 'ok') {
                        dispatch({ type: types.ORDER_SENT, oid });
                        dispatch({ type: typesCart.CART_CLEANED });
                    }
                })
                .catch(function (error) {
                    const status = error.response.data.status || `error ${error.response.status}`;
                    dispatch({ type: types.ORDER_ERROR, status });
                })
                .finally(function () {
                    // always executed
                });
        } catch (error) {
            console.error(error);
        }
    };
}

export function loginUser(usr, pwd) {
    return (dispatch, getState) => {
        try {
            axios.post('/dologin', {
                l: usr,
                h: btoa(pwd)
            })
                .then(function (response) {
                    // handle success
                    if(response.data.status == 'ok') {
                        const jwt = response.data.jwt;
                        if(r.jws.JWS.verify(jwt, pubKey, ['RS256'])) {
                            const data = response.data.data;
                            //const menu = response.data.menu;
                            const ingr = response.data.ingr;

                            const obj = r.jws.JWS.parse(jwt);
                            dispatch({ type: types.USER_LOGGED, user: obj.payloadObj, jwt, data, ingr });
                        }
                    }
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

export function logoutUser() {
    return (dispatch, getState) => {
        try {
            dispatch({ type: types.USER_LOGGED_OUT });
        } catch (error) {
            console.error(error);
        }
    };
}

export function closeOrder() {
    return (dispatch, getState) => {
        try {
            dispatch({ type: types.ORDER_RESET });
        } catch (error) {
            console.error(error);
        }
    };
}
