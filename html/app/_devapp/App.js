import React from "react";
import { BrowserRouter as Router, Route } from "react-router-dom";

import routes from "./routes";
import { connect } from 'react-redux';
import * as backendActions from './store/backend/actions';

import "bootstrap/dist/css/bootstrap.min.css";
import "./App.scss";
import "./shards-dashboards.1.1.0.min.css";

class AppBase extends React.Component {

    componentDidMount() {
        console.log('AppBase did mount');
        this.props.dispatch(backendActions.fetchMenu());
    }

    render() {
        return (<Router basename={process.env.REACT_APP_BASENAME || ""}>
            <div>
                {routes.map((route, index) => {
                    const regPath = /\/(.*)/.exec(route.path);
                    const pathName = regPath ? regPath[1].toLowerCase() : '';
                    return (
                        <Route
                            key={index}
                            path={route.path}
                            exact={route.exact}
                            component={props => {
                                return (
                                    <route.layout path={pathName} routes={routes} {...props}>
                                        <route.component {...props} />
                                    </route.layout>
                                );
                            }}
                        />
                    );
                })}
            </div>
        </Router>);
    }
}

export default connect()(AppBase);