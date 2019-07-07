import React from "react";
import MainItem from "../components/main-page/MainItem";

const MainPage = ({ }) => (
  <ul id="featured">
      <MainItem itemName="pizza" itemTitle="Pizza" />
      <MainItem itemName="drinks" itemTitle="Drinks" />
      <MainItem itemName="fast-food" itemTitle="Fast-food" />
      <MainItem itemName="desserts" itemTitle="Desserts" />
  </ul>
);

export default MainPage;
