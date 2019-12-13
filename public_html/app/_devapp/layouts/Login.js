import React from "react";
import PropTypes from "prop-types";
import { Container, Row, Col } from "shards-react";

import MenuHeader from "../components/layout/MainHeader";
import MainFooter from "../components/layout/MainFooter";

const LoginLayout = ({ path, routes, children }) => (
  <Container
    fluid
  >
    <Row>
      <Col>
        <div id="login-page">
          {children}
        </div>
      </Col>
    </Row>
  </Container>
);

LoginLayout.propTypes = {
  /**
   * Whether to display the navbar, or not.
   */
  noHeader: PropTypes.bool,
  /**
   * Whether to display the footer, or not.
   */
  noFooter: PropTypes.bool,

  path: PropTypes.string
};

LoginLayout.defaultProps = {
  noHeader: false,
  noFooter: false,
  path: ''
};

export default LoginLayout;
