// Layout Types
import { DefaultLayout, ContentLayout } from "./layouts";

// Constants
import MenuLocations from './MenuLocations';

// Route Views
import MainPage from "./views/MainPage";
import Pizza from "./views/Pizza";
import Desserts from "./views/Desserts";
import Drinks from "./views/Drinks";
import FastFood from "./views/FastFood";
import Contacts from "./views/Contacts";
import Order from "./views/Order";

export default [
  {
    path: "/",
    exact: true,
    layout: DefaultLayout,
    component: MainPage,
    title: 'Главная',
    menu: MenuLocations.Bottom
  },
  {
    path: "/fast-food",
    layout: ContentLayout,
    component: FastFood,
    title: 'Fast food',
    menu: MenuLocations.Top
  },
  {
    path: "/pizza",
    layout: ContentLayout,
    component: Pizza,
    title: 'Pizza',
    menu: MenuLocations.Top
  },
  {
    path: "/desserts",
    layout: ContentLayout,
    component: Desserts,
    title: 'Desserts',
    menu: MenuLocations.Top
  },
  {
    path: "/drinks",
    layout: ContentLayout,
    component: Drinks,
    title: 'Drinks',
    menu: MenuLocations.Top
  },
  {
    path: "/order",
    layout: ContentLayout,
    component: Order,
    title: 'Заказать онлайн',
    menu: MenuLocations.Bottom
  },
  {
    path: "/blog",
    layout: ContentLayout,
    component: Order,
    title: 'Блог',
    menu: MenuLocations.Bottom
  },
  {
    path: "/about",
    layout: ContentLayout,
    component: Order,
    title: 'О нас',
    menu: MenuLocations.Bottom
  },
  {
    path: "/contacts",
    layout: ContentLayout,
    component: Contacts,
    title: 'Контакты',
    menu: MenuLocations.Bottom,
    last: true
  },
];
