import React from "react";
import { Button } from "shards-react";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faTrashAlt, faArrowCircleUp, faArrowCircleDown } from '@fortawesome/free-solid-svg-icons';
import ImageCell from "./ImageCell";


class PriceRow extends React.Component {
    constructor(props) {
        super(props);

        this.setPicture = this.setPicture.bind(this);
    }

    setPicture() {
        //
    }

    render() {
        const { item } = this.props;
        return (<tr>
            <td><ImageCell price img={item.pic} onClick={this.setPicture} /></td>
            <td>{item.name}</td>
            <td>{item.price}</td>
            <td>
                <Button size="sm" outline theme="danger" onClick={() => console.log('delete-prc-' + item.id)}>
                    <FontAwesomeIcon icon={faTrashAlt} />
                </Button>
                <Button size="sm" outline theme="success" className="ml-1" onClick={() => console.log('move-up-prc-' + item.id)}>
                    <FontAwesomeIcon icon={faArrowCircleUp} />
                </Button>
                <Button size="sm" outline theme="success" onClick={() => console.log('move-down-prc-' + item.id)}>
                    <FontAwesomeIcon icon={faArrowCircleDown} />
                </Button>
            </td>
        </tr>);
    }
}

export default PriceRow;
