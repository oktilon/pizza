import React from "react";
import PropTypes from "prop-types";
import { NavLink as RouteNavLink } from "react-router-dom";
import { NavItem, NavLink } from "shards-react";

const SidebarNavItem = ({ item, active, onSwitchPage }) => (
    <NavItem>
        <NavLink onClick={() => { onSwitchPage(item.page);} } active={active}>
            {item.icon}
            {item.title &&
                <span>{item.title}</span>
            }
        </NavLink>
    </NavItem>
);

SidebarNavItem.propTypes = {
    item: PropTypes.object,
    active: PropTypes.bool,
    onSwitchPage: PropTypes.func
};

export default SidebarNavItem;
