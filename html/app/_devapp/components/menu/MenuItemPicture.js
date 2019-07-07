/* eslint jsx-a11y/anchor-is-valid: 0 */

import React from "react";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faShoppingCart } from '@fortawesome/free-solid-svg-icons'
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

  }

  render() {
    const {
      menuItem
    } = this.props;

    let footer = null; //  className="d-inline-block mt-1">
    if(menuItem.prices.length > 0) {
        footer = menuItem.prices.map( (item, ix) => (
            <Row key={ix}>
                <Col>
                    {item.name} 
                </Col>
                <Col>
                    <span className="text-fiord-blue">{item.price}</span>
                    <Button size="sm" theme="white" className="float-right">
                        <FontAwesomeIcon icon={faShoppingCart} /> Заказать
                    </Button>
                </Col>
            </Row>
        ));    
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
                <ul className="card-text">
                    {menuItem.content.length > 0 && 
                        menuItem.content.map(ing => {
                            return (<li key={ing.id}>{ing.name}</li>);
                        })
                    }
                </ul>
            </CardBody>
            <CardFooter className="text-muted border-top py-3 text-left">
                {footer}
            </CardFooter>
        </Card>
    );
  }
}

export default MenuItemPicture;
