/* eslint jsx-a11y/anchor-is-valid: 0 */
import React from "react";
import PageTitle from "../components/common/PageTitle";
import Menu from "../components/menu/Menu";
import MenuTabs from '../components/menu/MenuTabs';
import {
  Container,
  Row,
} from "shards-react";

class Drinks extends React.Component {
  constructor(props) {
    super(props);

    this.state = {};
  }

  render() {
    return (
      <Container fluid className="main-content-container px-4">
        <Row className="top-menu-mobile">
          <MenuTabs active="drinks" />
        </Row>
        <Row noGutters className="page-header py-4">
          <PageTitle sm="4" title="Напитки" subtitle="" className="text-center" />
        </Row>
        <Menu
          kind="drink"
          row={4}
        />
      </Container>
    );
  }
}

export default Drinks;
