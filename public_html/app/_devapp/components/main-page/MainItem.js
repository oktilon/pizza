import React from "react";
import { NavLink  } from "react-router-dom";

const MainItem = ({ itemName, itemTitle }) => (
    <li className={itemName}>
        <NavLink to={"/" + itemName}>
            <div>
                <span>{"{\u00A0"+itemTitle+"\u00A0}"}</span>
            </div>
        </NavLink>
    </li>
);

export default MainItem;
