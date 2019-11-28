import React from "react";
import { NavLink  } from "react-router-dom";
import {
	Col,
  } from "shards-react";

const BottomNav = ({ item, path }) => {
    let cls = [];
    const m = /\/(.*)/.exec(item.path);
    if(m && m[1] == path) cls.push('selected');
    if(item.last) cls.push('last');
    return (
        <Col className={cls.join(' ')}>
            <NavLink to={item.path}>{item.title}</NavLink>
        </Col>
    );
}

export default BottomNav;
