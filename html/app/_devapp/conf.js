import { faTrashAlt } from '@fortawesome/free-solid-svg-icons';

export const kinds = [
    { id: 'all',      name: 'Все' },
    { id: 'pizza',    name: 'Пицца' },
    { id: 'fastfood', name: 'Фаст-фуд' },
    { id: 'drink',    name: 'Напиток' },
    { id: 'desert',   name: 'Десерт' }
];

export const stats = [
    { id: 'active',   name: 'Активные', flags: 1, ico: faTrashAlt, clr: 'danger' },
    { id: 'all',      name: 'Все',      flags: 0, ico: false,      clr: '' }
];

export const INGR_DELETED = 1;

export const ingridientFlags = [
    { f: INGR_DELETED, i: faTrashAlt }
]