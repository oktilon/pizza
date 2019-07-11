import React from 'react';
import { connect } from 'react-redux';
import * as cartSelectors from '../../store/cart/reducer';

import Fab from '@material-ui/core/Fab';
import ShoppingCartIcon from '@material-ui/icons/ShoppingCart';

class FloatingCart extends React.Component {
    constructor(props) {
        super(props);
    }

    render() {
        const color = this.props.cartCount > 0 ? "primary" : "default";
        return (<Fab
            aria-label="Оформить заказ"
            color={color}
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