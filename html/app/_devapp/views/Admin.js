/* eslint jsx-a11y/anchor-is-valid: 0 */

import React from "react";
import { Redirect  } from "react-router-dom";
import { connect } from 'react-redux';
import * as backendSelectors from '../store/backend/reducer';
import * as cartSelectors from '../store/cart/reducer';

import { Container, Row, Col } from "shards-react";

import PageTitle from "../components/common/PageTitle";
import UserDetails from "../components/user-profile-lite/UserDetails";
import UserAccountDetails from "../components/user-profile-lite/UserAccountDetails";

class Admin extends React.Component {
    constructor(props) {
        super(props);

        this.onChangeName = this.onChangeName.bind(this);
        this.onChangePhone = this.onChangePhone.bind(this);
        this.onChangeAdr = this.onChangeAdr.bind(this);
        this.onSubmitForm = this.onSubmitForm.bind(this);

        this.state = {
            fio: '',
            phone: '',
            adr: '',
            initFio: true,
            initPhone: true
        };
    }

    onChangeName(ev) {
        const { initFio } = this.state;
        const val = ev.target.value;
        let initUpd = initFio ? true : false;
        if(initFio && this.validateName(val)) initUpd = false;
        this.setState({fio:val, initFio:initUpd});
    }

    onChangePhone(ev) {
        const { initPhone } = this.state;
        const val = ev.target.value;
        let initUpd = initPhone ? true : false;
        if(initPhone && this.validatePhone(val)) initUpd = false;
        this.setState({phone:val, initPhone:initUpd});
    }

    onChangeAdr(ev) {
        const val = ev.target.value;
        this.setState({adr:val});
    }

    onSubmitForm(ev) {
        ev.preventDefault();
        console.log("Submit", ev);
    }

    validateName(txt) {
        return txt.length > 3;
    }

    validatePhone(txt) {
        return /\+380 \d\d\ \d\d\d\-\d\d\-\d\d/.test(txt);
    }

    render() {
        const {
            fio,
            phone,
            adr,
            initFio,
            initPhone
        } = this.state;

        const {
            cart
        } = this.props;

        const validName = this.validateName(fio);
        const validPhone = this.validatePhone(phone);

        return (
            <Container fluid className="main-content-container px-4">
                <Row noGutters className="page-header py-4">
                    <PageTitle title="User Profile" subtitle="Overview" md="12" className="ml-sm-auto mr-sm-auto" />
                </Row>
                <Row>
                    <Col lg="4">
                        <UserDetails />
                    </Col>
                    <Col lg="8">
                        <UserAccountDetails />
                    </Col>
                </Row>
            </Container>
        );
    }
}


function mapStateToProps(state) {
    const user = backendSelectors.getUser(state);
    const menu = backendSelectors.getMenu(state);
    return {
        user,
        menu
    };
}

export default connect(mapStateToProps)(Admin);

