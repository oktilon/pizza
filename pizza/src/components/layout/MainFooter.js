import React from "react";
import { NavLink  } from "react-router-dom";

const MainFooter = () => (
  <div id="footer">
		<ul className="advertise">
			<li className="delivery">
				<h2>Голодно? Мы доставим</h2>
				<NavLink to="/pizza">Посмотри меню</NavLink>
			</li>
			<li className="event">
				<h2>Устроим праздник!</h2>
				<p>Раскрась<br/> вечеринку нашими блюдами</p>
			</li>
			<li className="connect">
				<h2>Давай дружить!</h2>
				<br/>
				<NavLink to="/" target="_blank" className="fb" title="Facebook"></NavLink>
				<a href="https://www.instagram.com/orderpizza.dp.ua/" target="_blank" className="twitr" title="Twitter"></a>
			</li>
		</ul>
  </div>
);

export default MainFooter;
