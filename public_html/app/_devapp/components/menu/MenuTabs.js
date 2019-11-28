/* eslint jsx-a11y/anchor-is-valid: 0 */

import React from "react";
import { Redirect } from "react-router-dom";
import Tabs from '@material-ui/core/Tabs';
import Tab from '@material-ui/core/Tab';
import MenuLocations from '../../MenuLocations';
import routes from "../../routes";

class MenuTabs extends React.Component {
    constructor(props) {
        super(props);

        this.handleChange = this.handleChange.bind(this);

        this.state = {
            goTo: ''
        };
    }

    handleChange(ev, newVal){
        const m = /\/(.+)/.exec(newVal);
        if(m && m[1] == this.props.active) return;
        this.setState({goTo: newVal});
    }

    render() {
        const { active } = this.props;
        const val = `/${active}`;
        if(this.state.goTo != '') return (<Redirect to={this.state.goTo} />);
        return (<Tabs
            value={val}
            indicatorColor="primary"
            textColor="primary"
            variant="scrollable"
            scrollButtons="auto"
            onChange={this.handleChange}
        >
            {routes.filter( x => x.menu == MenuLocations.Top)
                .map( (item, ix) => (
                <Tab
                    label={item.title}
                    value={item.path}
                    key={item.path}
                />
            ))}
        </Tabs>);
    }
}

export default MenuTabs;