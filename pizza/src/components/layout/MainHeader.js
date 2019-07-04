import React from "react";
import PropTypes from "prop-types";
import { NavLink  } from "react-router-dom";
//import { cx } from "classnames";

const MainHeader = ({ noNavigation, path }) => (
	<div id="header">
		<span className="signboard"></span>
		<ul id="infos">
			<li className="home">
				<NavLink to="/">НА ГЛАВНУЮ</NavLink>
			</li>
			<li className="phone">
				<NavLink to="/contacts">093 713 5868</NavLink>
			</li>
			<li className="address">
				<NavLink to="/contacts">mail@orderpizza.dp.ua</NavLink>
			</li>
		</ul>
		<h1><a href="/" id="logo2">Youssef</a></h1>
        { !noNavigation &&
			<ul id="navigation">
				<li className={path=='fast-food'?'current':''}>
					<NavLink to="/fast-food"><span>Fast food</span></NavLink>
				</li>
				<li className={path=='pizza'?'current':''}>
					<NavLink to="/pizza"><span>Pizza</span></NavLink>
				</li>
				<li className={path=='desserts'?'current':''}>
					<NavLink to="/desserts"><span>Desserts</span></NavLink>
				</li>
				<li className={path=='drinks'?'current':''}>
					<NavLink to="/drinks"><span>Drinks</span></NavLink>
				</li>
			</ul>
		}
	</div>
);

MainHeader.propTypes = {
	noNavigation: PropTypes.bool,
	path: PropTypes.string,
};
  
MainHeader.defaultProps = {
	noNavigation: false,
	path: ''
};

export default MainHeader;
