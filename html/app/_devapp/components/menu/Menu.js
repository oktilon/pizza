/* eslint jsx-a11y/anchor-is-valid: 0 */

import React from "react";
import * as _ from "lodash";
import MenuItemPicture from "./MenuItemPicture";
import { connect } from 'react-redux';
import * as backendSelectors from '../../store/backend/reducer';
import {
  Row,
  Col,
} from "shards-react";

class Menu extends React.Component {
  constructor(props) {
    super(props);
  }

  render() {
    const {
      menu,
      kind
    } = this.props;

    let items = [];
    if(menu) {
      items = _.filter(menu, m => m.kind == kind);
    }

    return (
      <Row>
        {items.map((menuItem, idx) => (
          <Col xl="3" lg="4" md="6" sm="12" className="mb-4" key={idx}>
            <MenuItemPicture menuItem={menuItem} />
          </Col>
        ))}
      </Row>
    );
  }
}

function mapStateToProps(state) {
  const menu = backendSelectors.getMenu(state);
  return {
    menu
  };
}

export default connect(mapStateToProps)(Menu);
