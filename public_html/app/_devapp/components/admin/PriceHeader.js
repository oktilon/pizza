import React from "react";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faPlus } from '@fortawesome/free-solid-svg-icons';
import { Button } from "shards-react";


const PriceHeader = ({ addItem }) => (<thead className="bg-light">
    <tr>
        <th scope="col" className="border-0">Фото</th>
        <th scope="col" className="border-0">Название</th>
        <th scope="col" className="border-0">Цена</th>
        <th scope="col" className="border-0">Управление
            <Button className="ml-4" size="sm" outline theme="primary" onClick={addItem}>
                <FontAwesomeIcon icon={faPlus} />
            </Button>
        </th>
    </tr>
</thead>);

export default PriceHeader;
