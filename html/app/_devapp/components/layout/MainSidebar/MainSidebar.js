import React from "react";
import PropTypes from "prop-types";
import classNames from "classnames";
import { Col } from "shards-react";

import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faBars, faEllipsisV, faInfoCircle, faSignOutAlt } from '@fortawesome/free-solid-svg-icons'

import SidebarMainNavbar from "./SidebarMainNavbar";
import SidebarSearch from "./SidebarSearch";
import SidebarNavItems from "./SidebarNavItems";

class MainSidebar extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            sidebarNavItems: [
                {
                    title: "Меню",
                    icon: <FontAwesomeIcon fixedWidth icon={faBars} className="mr-1" />,
                    page: "menu",
                },
                {
                    title: "Ингридиенты",
                    icon: <FontAwesomeIcon fixedWidth icon={faEllipsisV} className="mr-1" />,
                    page: "ingridients",
                },
                {
                    title: "Инфо",
                    icon: <FontAwesomeIcon fixedWidth icon={faInfoCircle} className="mr-1" />,
                    page: "info",
                },
                {
                    title: "Выход",
                    icon: <FontAwesomeIcon fixedWidth icon={faSignOutAlt} className="mr-1" />,
                    page: "exit",
                }
            ]
        };

    }

    render() {
        const classes = classNames(
            "main-sidebar",
            "px-0",
            "col-12",
            "open"
        );

        const { sidebarNavItems } = this.state;

        const { activePage, onSwitchPage, user } = this.props;

        return (
            <Col
                tag="aside"
                className={classes}
                lg={{ size: 2 }}
                md={{ size: 3 }}
            >
                <SidebarMainNavbar user={user} />
                <SidebarNavItems navItems={sidebarNavItems} activePage={activePage} onSwitchPage={onSwitchPage} />
            </Col>
        );
    }
}

MainSidebar.propTypes = {
    activePage:PropTypes.string,
    onSwitchPage: PropTypes.func,
    user: PropTypes.object
};

MainSidebar.defaultProps = {
    activePage   : 'menu',
    onSwitchPage : () => {},
    user         : { id:0, name:'Guest' }
};

export default MainSidebar;
