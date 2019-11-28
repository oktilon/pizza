import React from "react";
import { Nav } from "shards-react";

import SidebarNavItem from "./SidebarNavItem";

class SidebarNavItems extends React.Component {
  constructor(props) {
    super(props)

  }

  render() {
    const { navItems, activePage, onSwitchPage } = this.props;
    return (
      <div className="nav-wrapper">
        <Nav className="nav--no-borders flex-column">
          {navItems.map((item, idx) => (
            <SidebarNavItem key={idx} item={item} active={item.page == activePage} onSwitchPage={onSwitchPage} />
          ))}
        </Nav>
      </div>
    )
  }
}

export default SidebarNavItems;
