import React from "react";
import PropTypes from "prop-types";
import { connect } from 'react-redux';
import * as backendSelectors from '../../store/backend/reducer';
import * as cartSelectors from '../../store/cart/reducer';
import MenuLocations from "../../MenuLocations";
import TopNav from "./TopNav";
import { NavLink } from 'react-router-dom';
import {
	Nav,
	NavItem,
	Badge,
  } from "shards-react";

class MainHeader extends React.Component {
	constructor(props) {
		super(props);
	}

	render() {
		const { noNavigation, path, routes, data, cartCnt } = this.props;
		const badge = cartCnt > 0 && <Badge pill theme="primary">{cartCnt}</Badge>;
		return (
			<div id="header">
				<span className="signboard visible-top"></span>
				<Nav id="infos">
					<NavItem className="home">
						<NavLink to="/">НА ГЛАВНУЮ</NavLink>
					</NavItem>
					<NavItem className="phone">
						<NavLink to="/contacts">{data.phone}</NavLink>
					</NavItem>
					<NavItem className="address">
						<NavLink to="/contacts">{data.email}</NavLink>
					</NavItem>
					<NavItem className="basket">
						<NavLink to="/order">
							{badge}
						</NavLink>
					</NavItem>
				</Nav>
				<h1><a href="/" id="logo">Youssef</a></h1>
				{ !noNavigation &&
					<ul id="navigation" className="visible-top-menu">
						{routes.filter( x => x.menu == MenuLocations.Top)
							.map( (item, ix) => (
							<TopNav item={item} path={path} key={ix} />
						))}
					</ul>
				}
			</div>
		);
	}
}

MainHeader.propTypes = {
	noNavigation: PropTypes.bool,
	path: PropTypes.string,
	routes: PropTypes.array
};

MainHeader.defaultProps = {
	noNavigation: false,
	path: '',
	routes: []
};

function mapStateToProps(state) {
	const data = backendSelectors.getContactsData(state);
	const cartCnt = cartSelectors.getCartCount(state);
	return {
		data,
		cartCnt
	};
  }

export default connect(mapStateToProps)(MainHeader);

