import React from "react";
import { NavLink  } from "react-router-dom";
import BottomNav from "./BottomNav"
import MenuLocations from "../../MenuLocations";
import { connect } from 'react-redux';
import * as backendSelectors from '../../store/backend/reducer';
import {
	Container,
	Row,
	Col,
  } from "shards-react";

class MainFooter extends React.Component {
	render() {
		const { path, routes } = this.props;

		return  (
			<Container id="footer" className="w-100">
				<Row className="advertise">
					<Col className="delivery">
						<h2>Голодно? Мы доставим</h2>
						<NavLink to="/pizza">Посмотри меню</NavLink>
					</Col>
					<Col className="event">
						<h2>Устроим праздник!</h2>
						<p>Раскрась<br/> вечеринку нашими блюдами</p>
					</Col>
					<Col className="connect">
						<h2>Давай дружить!</h2>
						<br/>
						<a href="https://www.facebook.com/" target="_blank" className="fb" title="Facebook"></a>
						<a href="https://www.instagram.com/orderpizza.dp.ua/" target="_blank" className="twitr" title="Twitter"></a>
					</Col>
				</Row>
				<Row className="navigation bottom-collapse">
					{routes.filter(x=>x.menu == MenuLocations.Bottom)
						.map( (item, ix) => {
							if(item.hasOwnProperty('user')) {
								const needLogged = item.user;
								const isLogged = this.props.user && this.props.user.id > 0;
								if(isLogged != needLogged) return false;
						  	}
							return ( <BottomNav item={item} path={path} key={ix} /> );
						})
					}
				</Row>
				<Row className="w-100 text-center">
					<span>© Copyright 2017. All Rights Reserved.</span>
				</Row>
			</Container>
		);
	}
}

function mapStateToProps(state) {
	const user = backendSelectors.getUser(state);
	return {
		user
	};
  }

  export default connect(mapStateToProps)(MainFooter);