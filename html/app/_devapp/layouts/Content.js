import React from "react";
import _ from 'lodash';
import PropTypes from "prop-types";
import { Container, Row, Col } from "shards-react";

import MenuHeader from "../components/layout/MainHeader";
import MainFooter from "../components/layout/MainFooter";
import FloatingCart from "../components/layout/FloatingCart";

const ContentLayout = ({ path, routes, children, noHeader, noFooter }) => (
  <Container
    fluid
    onScroll={(ev) => {
      console.log("Route scroll", ev);
    }}
  >
    <Row>
      <Col>
        {!noHeader && <MenuHeader noNavigation={false} path={path} routes={routes} />}
        {_.indexOf(['pizza','fast-food','desserts','drinks'], path) >= 0 && <FloatingCart />}
        <div id="contents">
          {children}
        </div>
        {!noFooter && <MainFooter path={path} routes={routes} />}
      </Col>
    </Row>
  </Container>
);

ContentLayout.propTypes = {
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

ContentLayout.defaultProps = {
  noHeader: false,
  noFooter: false,
  path: ''
};

export default ContentLayout;
