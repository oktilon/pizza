import React from "react";
import MainItem from "../components/main-page/MainItem";

const MainPage = ({ }) => (
  <ul id="featured">
      <MainItem itemName="pizza" itemTitle="Пицца" />
      <MainItem itemName="drinks" itemTitle="Напитки" />
      <MainItem itemName="fast-food" itemTitle="Фаст-фуд" />
      <MainItem itemName="desserts" itemTitle="Десерты" />
  </ul>
);

export default MainPage;
