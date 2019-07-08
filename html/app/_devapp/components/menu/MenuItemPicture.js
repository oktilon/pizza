/* eslint jsx-a11y/anchor-is-valid: 0 */

import React from "react";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faShoppingCart, faTrashAlt } from '@fortawesome/free-solid-svg-icons'
import ContentList from './ContentList';
import { connect } from 'react-redux';
import * as cartSelectors from '../../store/cart/reducer';
import * as cartActions from '../../store/cart/actions';

import {
//  Container,
  Row,
  Col,
  Card,
  CardBody,
  CardFooter,
  Badge,
  Button
} from "shards-react";

class MenuItemPicture extends React.Component {
    constructor(props) {
        super(props);

        this.addItem = this.addItem.bind(this);
    }

    addItem(item) {
        const { menuItem } = this.props;
        this.props.dispatch(cartActions.addItem(menuItem, item));
    }

    removeItem(item) {
        const { menuItem } = this.props;
        this.props.dispatch(cartActions.removeItem(menuItem, item));
    }

    render() {
        const {
            cart,
            menuItem
        } = this.props;

        let footer = null; //  className="d-inline-block mt-1">
        if(menuItem.prices.length > 0) {
            footer = menuItem.prices.map( (item, ix) => {
                const inBasket = _.find(cart, x => x.id == item.id);
                const cnt = inBasket ? inBasket.cnt : 0;
                const del = cnt > 0;
                return (
                    <Row key={ix}>
                        <Col>
                            <span>{item.name}</span><span className="text-fiord-blue ml-1">{item.price}</span>
                        </Col>
                        <Col>
                            <Button size="sm" theme="danger" disabled={!del} outline={!del} className="float-right" onClick={()=>{this.removeItem(item)}}>
                                <FontAwesomeIcon icon={faTrashAlt} />
                            </Button>
                            <Button size="sm" theme="white" className="float-right" onClick={()=>{this.addItem(item)}}>
                                <FontAwesomeIcon icon={faShoppingCart} /> {cnt}
                            </Button>
                        </Col>
                    </Row>
                );
            });    
        }

        const url = '/images/menu/' + menuItem.pic;

        const style = {
            backgroundImage: `url('${url}')`
        }

        const badge = 'primary';

        return (
            <Card small className="card-post h-100">
                <div
                    className="card-post__image"
                    style={style}
                >
                    { false && <Badge
                        pill
                        className={`card-post__category bg-${badge}`}
                    >
                        {"--"}
                    </Badge>}
                </div>
                <CardBody>
                    <h5 className="card-title">
                        <span className="text-fiord-blue" href="#">
                        {menuItem.name}
                        </span>
                    </h5>
                    {menuItem.content.length > 0 && 
                        <ContentList content={menuItem.content} />
                    }
                </CardBody>
                <CardFooter className="text-muted border-top py-3 text-left">
                    {footer}
                </CardFooter>
            </Card>
        );
    }
}

function mapStateToProps(state) {
    const cart = cartSelectors.getCart(state);
    return {
        cart
    };
}

export default connect(mapStateToProps)(MenuItemPicture);
