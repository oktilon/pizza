import React from "react";

const MainPage = ({ }) => (
  <>
    <ul id="featured">
      <li className="main">
        <a href="/pizza"></a>
      </li>
      <li className="drinks">
        <a href="/drinks"></a>
      </li>
      <li className="entree">
        <a href="/entree"></a>
      </li>
      <li className="desserts">
        <a href="/desserts"></a>
      </li>
    </ul>
    <div className="mn-pizza">
      <b>{"{"}</b> Pizza <b>{"}"}</b>
    </div>
  </>
);

export default MainPage;
