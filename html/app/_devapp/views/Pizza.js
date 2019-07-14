/* eslint jsx-a11y/anchor-is-valid: 0 */

import React from "react";
import PageTitle from "../components/common/PageTitle";
import Menu from "../components/menu/Menu";
import MenuTabs from '../components/menu/MenuTabs';
import Tab from '@material-ui/core/Tab';
import {
  Container,
  Row,
} from "shards-react";

class Pizza extends React.Component {
  constructor(props) {
    super(props);

    this.state = {};
  }

  render() {
    return (
      <Container fluid className="main-content-container px-4">
        <Row className="top-menu-mobile">
          <MenuTabs active="pizza" />
        </Row>
        <Row noGutters className="page-header py-4">
          <PageTitle sm="4" title="Пицца" subtitle="" className="text-center" />
        </Row>
        <Menu
          kind="pizza"
          row={4}
        />
      </Container>
    );
  }
}

export default Pizza;