import React from "react";
import PropTypes from "prop-types";
import { Navbar, NavbarBrand } from "shards-react";

class SidebarMainNavbar extends React.Component {
    constructor(props) {
        super(props);

    }

    render() {
        const { user } = this.props;
        return (
            <div className="main-navbar">
                <Navbar
                    className="align-items-stretch bg-white flex-md-nowrap border-bottom p-0"
                    type="light"
                >
                    <NavbarBrand
                        className="w-100 mr-0"
                        tag="span"
                        href="#"
                        style={{ lineHeight: "25px" }}
                    >
                        <div className="d-table m-auto">
                            <span className="d-md-inline ml-1">
                                {"Админка. " + user.name}
                            </span>
                        </div>
                    </NavbarBrand>
                </Navbar>
            </div>
        );
    }
}

SidebarMainNavbar.propTypes = {
    user: PropTypes.object
};

SidebarMainNavbar.defaultProps = {
    user: { name: 'Guest' }
};

export default SidebarMainNavbar;
