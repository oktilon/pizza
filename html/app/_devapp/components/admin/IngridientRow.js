import React from "react";
import ImageCell from "./ImageCell";

class IngridientRow extends React.Component {

    render() {
        const { item } = this.props;
        return (<tr>
            <td><ImageCell ingr img={item.pic} /></td>
            <td>{item.name}</td>
            <td>{item.flags}</td>
            <td>{""}</td>
        </tr>);
    }
}

export default IngridientRow;
