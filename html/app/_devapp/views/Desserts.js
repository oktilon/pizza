/* eslint jsx-a11y/anchor-is-valid: 0 */
import React from "react";
import PageTitle from "../components/common/PageTitle";
import Menu from "../components/menu/Menu";
import {
  Container,
  Row,
} from "shards-react";

class Desserts extends React.Component {
  constructor(props) {
    super(props);

    this.state = {};
  }

  render() {
    return (
      <Container fluid className="main-content-container px-4">
        <Row noGutters className="page-header py-4">
          <PageTitle sm="4" title="Десерты" subtitle="" className="text-center" />
        </Row>
        <Menu
          kind="desert"
          row={4}
        />
      </Container>
    );
  }
}

export default Desserts;