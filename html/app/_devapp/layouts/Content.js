import React from "react";
import PropTypes from "prop-types";
import { Container, Row, Col } from "shards-react";

import MenuHeader from "../components/layout/MainHeader";
import MainFooter from "../components/layout/MainFooter";

const ContentLayout = ({ path, menu, children, noHeader, noFooter }) => (
  <Container fluid>
    <Row>
      <Col>
        {!noHeader && <MenuHeader noNavigation={false} path={path} menu={menu} />}
        <div id="contents">
          {children}
        </div>
        {!noFooter && <MainFooter path={path} menu={menu} />}
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
