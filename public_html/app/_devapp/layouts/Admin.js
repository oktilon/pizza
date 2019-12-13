
import React from "react";
import { Container, Row, Col } from "shards-react";

import MainSidebar from "../components/layout/MainSidebar/MainSidebar";
import Admin from "../views/Admin";


import * as backendSelectors from '../store/backend/reducer';
import * as backendActions from '../store/backend/actions';

import { connect } from 'react-redux';

class AdminLayout extends React.Component  {
    constructor(props) {
        super(props);

        this.state = {
            page: 'menu'
        };

        this.switchPage = this.switchPage.bind(this);
    }

    switchPage(page) {
        if(page == 'exit') {
            this.props.dispatch(backendActions.logoutUser());
        } else {
            this.setState({page: page});
        }
    }

    render() {
        const { path, routes, children, noHeader, noFooter, user } = this.props;
        const { page } = this.state;

        if(page == 'exit') {
            return false;
        }

        return (
            <Container fluid>
                <Row>
                    <MainSidebar activePage={page} onSwitchPage={this.switchPage} user={user} />
                    <Col
                        className="main-content p-0"
                        lg={{ size: 10, offset: 2 }}
                        md={{ size: 9, offset: 3 }}
                        sm="12"
                        tag="main"
                    >
                        <Admin user={user} activePage={page} />
                    </Col>
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


export default connect(mapStateToProps)(AdminLayout);