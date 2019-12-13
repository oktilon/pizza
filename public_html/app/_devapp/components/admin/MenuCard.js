import React from "react";
import MenuRow from "./MenuRow";
import { FormSelect, Row, Col, Card, CardHeader, CardBody } from "shards-react";
import Popup from 'react-popup';
import NewMenuPopup from "./NewMenuPopup";
import NewPricePopup from './NewPricePopup';
import * as OpenTypes from '../../constants';
import * as Conf from '../../conf';
import MenuActions from "./MenuActions";
import Table from '@material-ui/core/Table';
import TableBody from '@material-ui/core/TableBody';
import TableCell from '@material-ui/core/TableCell';
import TableHead from '@material-ui/core/TableHead';
import TableRow from '@material-ui/core/TableRow';

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
    let priceName = null;
    let priceValue = null;
    let nameChange = function (value) {
        priceName = value;
    };
    let valueChange = function (value) {
        priceValue = value;
    };

    this.create({
        title: 'Новая цена',
        content: <NewPricePopup onChangeName={nameChange} onChangeVal={valueChange} />,
        buttons: {
            right: ['cancel'],
            left: [{
                text: 'Добавить',
                key: 'ctrl+s',
                className: 'success',
                action: function () {
                    if(!priceName || priceName.length < 3) return;
                    if(!priceValue || priceValue == 0) return;
                    callback({n:priceName, p:priceValue});
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
        this.filterStat = this.filterStat.bind(this);
        this.openItem = this.openItem.bind(this);
        this.addNewPrice = this.addNewPrice.bind(this);

        this.actions = new MenuActions(
            this.addNewPrice,
            () => { console.log('add-cont') },
            () => { console.log('add-aux') },
            (it, sub) => { console.log('del-' + it + '-' + sub) }
        );

        this.state = {
            kind : 'all',
            stat : 'active',
            openedItem : null,
            openMode : OpenTypes.OPEN_NONE
        }
    }

    filterKind(ev) {
        this.setState({ kind: ev.target.value });
    }

    filterStat(ev) {
        this.setState({ stat: ev.target.value });
    }

    openItem(itemId, openMode) {
        this.setState({
            openedItem: itemId,
            openMode: itemId ? openMode : OpenTypes.OPEN_NONE
        })
    }

    addNewPrice() {
        Popup.plugins().new_price('', 'новая цена', function (value) {
            Popup.alert('You typed: ' + JSON.stringify(value));
        });
    }

    filter() {
        const { kind, stat } = this.state;

        return (<Row form className="ml-2">
            <Col md="2" className="form-group">
                <label htmlFor="fltKind">Тип</label>
                <FormSelect id="fltKind" onChange={this.filterKind} defaultValue={kind}>
                    {_.map(Conf.kinds, k => <option value={k.id} key={k.id}>{k.name}</option>)}
                </FormSelect>
            </Col>
            <Col md="2" className="form-group">
                <label htmlFor="fltStatus">Статус</label>
                <FormSelect id="fltStatus" onChange={this.filterStat} defaultValue={stat}>
                    {_.map(Conf.stats, s => <option value={s.id} key={s.id}>{s.name}</option>)}
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
            stat,
            openedItem,
            openMode
        } = this.state;

        const st = _.find(Conf.stats, s => s.id == stat);
        console.log(st);

        const menuFiltered = _.filter(menu, it => {
            if(kind != 'all' && it.kind != kind) return false;
            if(stat != 'all' && (it.flags & st.flags) ) return false;
            return true;
        });
        return (<tbody>
            {_.map(menuFiltered, item => {
                return <MenuRow
                    key={item.id}
                    item={item}
                    opened={item.id == openedItem}
                    openMode={openMode}
                    openItem={this.openItem}
                    actions={this.actions}
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