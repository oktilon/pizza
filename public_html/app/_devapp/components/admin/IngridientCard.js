import React from "react";
import { Card, CardHeader, CardBody, Row } from "shards-react";
import IngridientRow from "./IngridientRow";

class IngridientCard extends React.Component {
    constructor(props) {
        super(props);

        this.filter = this.filter.bind(this);
        this.header = this.header.bind(this);
        this.body = this.body.bind(this);
    }

    filter() {
        return (<Row form></Row>);

    }

    header() {
        return (
            <thead className="bg-light">
                <tr>
                    <th scope="col" className="border-0">Фото</th>
                    <th scope="col" className="border-0">Название</th>
                    <th scope="col" className="border-0">Статус</th>
                    <th scope="col" className="border-0">Управление</th>
                </tr>
            </thead>
        );
    }

    body() {
        const { ingr } = this.props;
        return (<tbody>
            {_.map(ingr, item => <IngridientRow item={item} key={item.id} />)}
        </tbody>);
    }

    render() {
        return (<Card small className="mb-4">
            <CardHeader className="border-bottom">
                <h6 className="m-0">Ингридиенты</h6>
            </CardHeader>
            <CardBody className="p-0 pb-3">
                {this.filter()}
                <table className="table mb-0">
                    {this.header()}
                    {this.body()}
                </table>
            </CardBody>
        </Card>);
    }
}

export default IngridientCard;