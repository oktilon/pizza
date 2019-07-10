/* eslint jsx-a11y/anchor-is-valid: 0 */

import React from "react";
import { Redirect  } from "react-router-dom";
import { connect } from 'react-redux';
import * as backendSelectors from '../store/backend/reducer';
import * as cartSelectors from '../store/cart/reducer';

import Popup from "react-popup";

import { Container, Card } from "shards-react";

import MenuCard from "../components/admin/MenuCard";
import IngridientCard from "../components/admin/IngridientCard";


class Admin extends React.Component {
    constructor(props) {
        super(props);

        // this.onChangeName = this.onChangeName.bind(this);
        // this.onChangePhone = this.onChangePhone.bind(this);
        // this.onChangeAdr = this.onChangeAdr.bind(this);
        // this.onSubmitForm = this.onSubmitForm.bind(this);

        // this.state = {
        // };
    }



    render() {
        const {
            user,
            menu,
            ingr,
            activePage
        } = this.props;

        if(user.id == 0) return (<Redirect to='/login' />);

        var pageItem = (<Card small className="mb-4" />);

        switch (activePage) {
            case 'menu':
                pageItem = <MenuCard menu={menu} />
                break;

            case 'ingridients':
                pageItem = <IngridientCard ingr={ingr}/>;
                break;

            case 'info':
                pageItem = false;
                break;
        }

        return (<Container fluid className="main-content-container px-4">
            {pageItem}
            <Popup />
        </Container>);
    }
}


function mapStateToProps(state) {
    const user = backendSelectors.getUser(state);
    const menu = backendSelectors.getMenu(state);
    const ingr = backendSelectors.getIngridients(state);
    return {
        user,
        menu,
        ingr
    };
}

export default connect(mapStateToProps)(Admin);

