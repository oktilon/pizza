import React from "react";
import Button from '@material-ui/core/Button';
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faTrashAlt, faArrowCircleUp, faArrowCircleDown } from '@fortawesome/free-solid-svg-icons';
import Fab from '@material-ui/core/Fab';
import IconButton from '@material-ui/core/IconButton';
import AddIcon from '@material-ui/icons/Add';
import DeleteIcon from '@material-ui/icons/Delete';
import Table from '@material-ui/core/Table';
import TableBody from '@material-ui/core/TableBody';
import TableCell from '@material-ui/core/TableCell';
import TableHead from '@material-ui/core/TableHead';
import TableRow from '@material-ui/core/TableRow';
import ImageCell from "./ImageCell";

class ContentRow extends React.Component {

    render() {
        const { item, hasPrice, actions } = this.props;
        return (<TableRow>
            <TableCell>
                <ImageCell
                    type="c"
                    id={item.id}
                />
            </TableCell>
            <TableCell>{item.name}</TableCell>
            {hasPrice && <TableCell>{item.price}</TableCell>}
            <TableCell>
                <Button
                    size="small"
                    variant="outlined"
                    color="secondary"
                    onClick={() => console.log('delete-prc-' + item.id)}
                >
                    <FontAwesomeIcon icon={faTrashAlt} />
                </Button>
                <Button
                    size="small"
                    variant="outlined"
                    color="primary"
                    className="ml-1"
                    onClick={() => console.log('move-up-prc-' + item.id)}
                >
                    <FontAwesomeIcon icon={faArrowCircleUp} />
                </Button>
                <Button
                    size="small"
                    variant="outlined"
                    color="primary"
                    onClick={() => console.log('move-down-prc-' + item.id)}
                >
                    <FontAwesomeIcon icon={faArrowCircleDown} />
                </Button>
            </TableCell>
        </TableRow>);
    }
}

export default ContentRow;
