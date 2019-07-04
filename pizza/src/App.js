import React from "react";
import { BrowserRouter as Router, Route } from "react-router-dom";

import routes from "./routes";
//import withTracker from "./withTracker";

import "bootstrap/dist/css/bootstrap.min.css";
import "./App.css";
//import "./shards-dashboard/styles/shards-dashboards.1.1.0.min.css";

export default () => (
  <Router basename={process.env.REACT_APP_BASENAME || ""}>
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
                <route.layout path={pathName} {...props}>
                  <route.component {...props} />
                </route.layout>
              );
            }}
          />
        );
      })}
    </div>
  </Router>
);