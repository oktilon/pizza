import React from "react";
import Fab from '@material-ui/core/Fab';
import AddIcon from '@material-ui/icons/Add';
import TableCell from '@material-ui/core/TableCell';
import TableHead from '@material-ui/core/TableHead';
import TableRow from '@material-ui/core/TableRow';

const ContentHeader = ({ addItem, hasPrice }) => (<TableHead className="bg-light">
    <TableRow>
        <TableCell scope="col" className="border-0">Фото</TableCell>
        <TableCell scope="col" className="border-0">Название</TableCell>
        {hasPrice && <TableCell scope="col" className="border-0">Цена</TableCell>}
        <TableCell scope="col" className="border-0">Управление
            <Fab className="ml-4" size="small" color="primary" onClick={addItem}>
                <AddIcon />
            </Fab>
        </TableCell>
    </TableRow>
</TableHead>);

export default ContentHeader;
