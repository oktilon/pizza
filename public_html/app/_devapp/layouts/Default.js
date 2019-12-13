import React from "react";
import PropTypes from "prop-types";
import { Container, Row, Col } from "shards-react";

import MainHeader from "../components/layout/MainHeader";
import MainFooter from "../components/layout/MainFooter";

const DefaultLayout = ({ path, routes, children, noHeader, noFooter }) => (
  <Container fluid>
    <Row>
      <Col>
        {!noHeader && <MainHeader noNavigation={true} path={path} routes={routes} />}
        <div id="body">
          {children}
        </div>
        {!noFooter && <MainFooter path={path} routes={routes} />}
      </Col>
    </Row>
  </Container>
);

DefaultLayout.propTypes = {
  /**
   * Whether to display the navbar, or not.
   */
  noHeader: PropTypes.bool,
  /**
   * Whether to display the footer, or not.
   */
  noFooter: PropTypes.bool
};

DefaultLayout.defaultProps = {
  noHeader: false,
  noFooter: false
};

export default DefaultLayout;
