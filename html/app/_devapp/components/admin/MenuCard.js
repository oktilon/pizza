import React from "react";
import MenuRow from "./MenuRow";
import { FormSelect, Row, Col, Card, CardHeader, CardBody } from "shards-react";
import Popup from 'react-popup';
import NewMenuPopup from "./NewMenuPopup";
import NewPricePopup from './NewPricePopup';

const kinds = [
    { id: 'all',      name: 'Все' },
    { id: 'pizza',    name: 'Пицца' },
    { id: 'fastfood', name: 'Фаст-фуд' },
    { id: 'drink',    name: 'Напиток' },
    { id: 'desert',   name: 'Десерт' }
];


Popup.registerPlugin('new_menu', function (defaultValue, placeholder, callback) {
    let promptValue = null;
    let promptChange = function (value) {
        promptValue = value;
    };

    this.create({
        title: 'Название нового товара',
        content: <NewMenuPopup onChange={promptChange} placeholder={placeholder} value={defaultValue} />,
        buttons: {
            left: ['cancel'],
            right: [{
                text: 'Save',
                key: '⌘+s',
                className: 'success',
                action: function () {
                    callback(promptValue);
                    Popup.close();
                }
            }]
        }
    });
});

Popup.registerPlugin('new_price', function (defaultValue, placeholder, callback) {
    let promptValue = null;
    let promptChange = function (value) {
        promptValue = value;
    };

    this.create({
        title: 'Название нового товара',
        content: <NewPricePopup onChange={promptChange} placeholder={placeholder} value={defaultValue} />,
        buttons: {
            left: ['cancel'],
            right: [{
                text: 'Save',
                key: '⌘+s',
                className: 'success',
                action: function () {
                    callback(promptValue);
                    Popup.close();
                }
            }]
        }
    });
});

class MenuCard extends React.Component {
    constructor(props) {
        super(props);

        this.filter = this.filter.bind(this);
        this.header = this.header.bind(this);
        this.body = this.body.bind(this);
        this.filterKind = this.filterKind.bind(this);
        this.openItem = this.openItem.bind(this);
        this.addNewPrice = this.addNewPrice.bind(this);

        this.state = {
            kind : 'all',
            openedItem : null,
            openedPrices : false
        }
    }

    filterKind(ev) {
        this.setState({ kind: ev.target.value });
    }

    openItem(itemId, isPrice) {
        this.setState({
            openedItem: itemId,
            openedPrices: isPrice
        })
    }

    addNewPrice() {
        console.log('addNewPrice');
        const promptChange = (ev) => { console.log('change', ev); };
        const promptSave = () => { console.log('save'); };
        Popup.create({
            title: 'Название нового товара',
            content: <NewPricePopup onChange={promptChange} placeholder={"price"} value={""} />,
            buttons: {
                left: ['cancel'],
                right: [{
                    text: 'Save',
                    key: '⌘+s',
                    className: 'success',
                    action: function () {
                        //callback(promptValue);
                        promptSave();
                        Popup.close();
                    }
                }]
            }
        });
        // Popup.plugins().new_price('', '', function (value) {
        //     Popup.alert('You typed: ' + value);
        // });
    }

    filter() {
        const { kind } = this.state;

        return (<Row form className="ml-2">
            <Col md="2" className="form-group">
                <label htmlFor="fltKind">Тип</label>
                <FormSelect id="fltKind" onChange={this.filterKind} defaultValue={kind}>
                    {_.map(kinds, k => <option value={k.id} key={k.id}>{k.name}</option>)}
                </FormSelect>
            </Col>
        </Row>);
    }

    header() {
        return (
            <thead className="bg-light">
                <tr>
                    <th scope="col" className="border-0">Фото</th>
                    <th scope="col" className="border-0">Тип</th>
                    <th scope="col" className="border-0">Название</th>
                    <th scope="col" className="border-0">Описание</th>
                    <th scope="col" className="border-0">Статус</th>
                    <th scope="col" className="border-0">Управление</th>
                </tr>
            </thead>
        );
    }

    body() {
        const { menu } = this.props;
        const {
            kind,
            openedItem,
            openedPrices
        } = this.state;

        const menuFiltered = _.filter(menu, it => {
            if(kind != 'all' && it.kind != kind) return false;
            return true;
        });
        return (<tbody>
            {_.map(menuFiltered, item => {
                return <MenuRow
                    key={item.id}
                    item={item}
                    opened={item.id == openedItem}
                    isPrice={openedPrices}
                    openItem={this.openItem}
                    addNewPrice={this.addNewPrice}
                />
            })}
        </tbody>);
    }

    render() {
        return (<Card small className="mb-4">
            <CardHeader className="border-bottom">
                <h6 className="m-0">Меню</h6>
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

export default MenuCard;