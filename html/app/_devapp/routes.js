// Layout Types
import { DefaultLayout, ContentLayout, LoginLayout, AdminLayout } from "./layouts";

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
import Login from "./views/Login";
import About from "./views/About";
import Blog from "./views/Blog";

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
    title: 'Фаст-фуд',
    menu: MenuLocations.Top
  },
  {
    path: "/pizza",
    layout: ContentLayout,
    component: Pizza,
    title: 'Пицца',
    menu: MenuLocations.Top
  },
  {
    path: "/desserts",
    layout: ContentLayout,
    component: Desserts,
    title: 'Десерты',
    menu: MenuLocations.Top
  },
  {
    path: "/drinks",
    layout: ContentLayout,
    component: Drinks,
    title: 'Напитки',
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
    path: "/login",
    layout: LoginLayout,
    component: Login,
    title: 'Вход',
    menu: MenuLocations.Bottom,
    user: false
  },
  {
    path: "/admin",
    layout: AdminLayout,
    component: false,
    title: 'Админ',
    menu: MenuLocations.Bottom,
    user: true
  },
  {
    path: "/about",
    layout: ContentLayout,
    component: About,
    title: 'О нас',
    menu: MenuLocations.Bottom
  },
  {
    path: "/blog",
    layout: ContentLayout,
    component: Blog,
    title: 'Блог',
    menu: false
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
