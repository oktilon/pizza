import React from "react";
import ImageCell from "./ImageCell";
import IngridientFlag from "./IngridientFlag";
import InlineTextEdit from '../common/InlineTextEdit';
import { Button } from "@material-ui/core";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faTrashAlt, faTrashRestoreAlt } from "@fortawesome/free-solid-svg-icons";
import * as Conf from "~/conf";

class IngridientRow extends React.Component {
    constructor(props) {
        super(props);

        this.handleChangeName = this.handleChangeName.bind(this);
    }

    handleChangeName(item, txt) {
        console.log(item, txt);
    }

    render() {
        const { item } = this.props;
        const isDeleted = item.flags & Conf.INGR_DELETED;
        const delIcon = isDeleted ? faTrashRestoreAlt : faTrashAlt;
        return (<tr>
            <td>
                <ImageCell type="c" id={item.id} />
            </td>
            <td>
                <InlineTextEdit
                    //validate={this.customValidateText}
                    activeClassName="editing"
                    text={item.name}
                    paramName="message"
                    onChange={ (txt) => this.handleChangeName(item, txt) }
                    style={{
                        backgroundColor: 'yellow',
                        minWidth: 150,
                        display: 'inline-block',
                        margin: 0,
                        padding: 0,
                        fontSize: 15,
                        outline: 0,
                        border: 0
                    }}
                />
            </td>
            <td>
                <IngridientFlag
                    flag={item.flags}
                />
            </td>
            <td>
                <Button
                    color={isDeleted ? "primary" : "secondary"}
                >
                    <FontAwesomeIcon icon={delIcon} />
                </Button>
            </td>
        </tr>);
    }
}

export default IngridientRow;
