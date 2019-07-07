import React from "react";
import PropTypes from "prop-types";
import { NavLink  } from "react-router-dom";
import MenuLocations from "../../MenuLocations";
import TopNav from "./TopNav";
//import { cx } from "classnames";

const MainHeader = ({ noNavigation, path, menu }) => (
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
		<h1><a href="/" id="logo">Youssef</a></h1>
        { !noNavigation &&
			<ul id="navigation">
				{menu.filter( x => x.menu == MenuLocations.Top)
					.map( (item, ix) => (
					<TopNav item={item} path={path} key={ix} />
				))}
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
