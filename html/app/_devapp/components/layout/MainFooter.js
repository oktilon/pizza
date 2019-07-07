import React from "react";
import { NavLink  } from "react-router-dom";
import BottomNav from "./BottomNav"
import MenuLocations from "../../MenuLocations";

const MainFooter = ({ path, menu }) => (
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
		<div>
			<ul className="navigation">
				{menu.filter(x=>x.menu == MenuLocations.Bottom)
					.map( (item, ix) => (
						<BottomNav item={item} path={path} key={ix} />
					))
				}
			</ul>
			<span>© Copyright 2017. All Rights Reserved.</span>
		</div>		
  </div>
);

export default MainFooter;
