// Layout Types
import { DefaultLayout, MenuLayout, ContentLayout } from "./layouts";

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
    component: MainPage
  },
  {
    path: "/pizza",
    layout: ContentLayout,
    component: Pizza
  },
  {
    path: "/desserts",
    layout: ContentLayout,
    component: Desserts
  },
  {
    path: "/drinks",
    layout: ContentLayout,
    component: Drinks
  },
  {
    path: "/fast-food",
    layout: ContentLayout,
    component: FastFood
  },
  {
    path: "/contacts",
    layout: ContentLayout,
    component: Contacts
  },
  {
    path: "/order",
    layout: ContentLayout,
    component: Order
  },
];
