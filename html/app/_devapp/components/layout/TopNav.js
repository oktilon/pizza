import React from "react";
import { NavLink  } from "react-router-dom";

const TopNav = ({ item, path }) => {
    let cls = [];
    const m = /\/(.*)/.exec(item.path);
    if(m && m[1] == path) cls.push('current');
    return (
        <li className={cls.join(' ')}>
            <NavLink to={item.path}><span>{item.title}</span></NavLink>
        </li>
    );
}

export default TopNav;
