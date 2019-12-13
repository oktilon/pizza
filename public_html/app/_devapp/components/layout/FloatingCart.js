import React from 'react';
import { connect } from 'react-redux';
import * as cartSelectors from '../../store/cart/reducer';
import { Redirect  } from "react-router-dom";

import Fab from '@material-ui/core/Fab';
import ShoppingCartIcon from '@material-ui/icons/ShoppingCart';

class FloatingCart extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            action: false
        };
    }

    render() {
        if(this.state.action) {
            return (<Redirect to="/order" />);
        }
        const color = this.props.cartCount > 0 ? "primary" : "default";
        return (<Fab
            aria-label="Оформить заказ"
            color={color}
            onClick={() => {
                if(this.props.cartCount > 0) {
                    this.setState({action: true});
                }
            }}
            title="Оформить заказ"
            style={{
                position:"sticky",
                top:20,
                float:"right",
                right:20,
                zIndex:2
            }}
        >
            <ShoppingCartIcon />
      </Fab>);
    }
}

function mapStateToProps(state) {
    const cnt = cartSelectors.getCartCount(state);

    return {
        cartCount: cnt
    };
}
export default connect(mapStateToProps)(FloatingCart);