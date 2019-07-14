/* eslint jsx-a11y/anchor-is-valid: 0 */

import React from "react";
import cx from "classnames";
import { NavLink  } from "react-router-dom";
import MaskedInput from 'react-maskedinput';
import IconButton from '@material-ui/core/IconButton';
import Table from '@material-ui/core/Table';
import TableHead from '@material-ui/core/TableHead';
import TableBody from '@material-ui/core/TableBody';
import TableFooter from '@material-ui/core/TableFooter';
import TableRow from '@material-ui/core/TableRow';
import TableCell from '@material-ui/core/TableCell';
import DeleteIcon from '@material-ui/icons/Delete';
import { connect } from 'react-redux';
import * as backendSelectors from '../store/backend/reducer';
import * as backendActions from '../store/backend/actions';
import * as cartSelectors from '../store/cart/reducer';
import * as cartActions from '../store/cart/actions';
import SnackbarContentWithButton from '../components/common/SnackbarContentWithButton';
import {
  Container,
  Card,
  CardHeader,
  ListGroup,
  ListGroupItem,
  Row,
  Col,
  Form,
  FormInput,
  FormGroup,
  FormFeedback,
} from "shards-react";
import Button from '@material-ui/core/Button';
import Snackbar from '@material-ui/core/Snackbar';
import { withStyles } from '@material-ui/styles';
import CircularProgress from '@material-ui/core/CircularProgress';

const kindToLink = {
  pizza: '/pizza#',
  fastfood: '/fast-food#',
  drink: '/drinks#',
  desert: '/desserts#'
}


const styles = {
  progress: {
    margin: 'auto'
  }
};

@withStyles(styles)
class Order extends React.Component {
  constructor(props) {
    super(props);

    this.onChangeName = this.onChangeName.bind(this);
    this.onChangePhone = this.onChangePhone.bind(this);
    this.onChangeAdr = this.onChangeAdr.bind(this);
    this.onSubmitForm = this.onSubmitForm.bind(this);
    this.handleCloseMessage = this.handleCloseMessage.bind(this);
    this.handleDeleteItem = this.handleDeleteItem.bind(this);

    this.state = {
      fio: '',
      phone: '',
      adr: '',
      initFio: true,
      initPhone: true
    };
  }

  onChangeName(ev) {
    const { initFio } = this.state;
    const val = ev.target.value;
    let initUpd = initFio ? true : false;
    if(initFio && this.validateName(val)) initUpd = false;
    this.setState({fio:val, initFio:initUpd});
  }

  onChangePhone(ev) {
    const { initPhone } = this.state;
    const val = ev.target.value;
    let initUpd = initPhone ? true : false;
    if(initPhone && this.validatePhone(val)) initUpd = false;
    this.setState({phone:val, initPhone:initUpd});
  }

  onChangeAdr(ev) {
    const val = ev.target.value;
    this.setState({adr:val});
  }

  onSubmitForm(ev) {
    ev.preventDefault();
    const { cart, dispatch } = this.props;
    const { fio, phone, adr } = this.state;
    const form = {
      fio: fio,
      phone: phone,
      adr: adr
    };
    const ord = _.map(cart, it => {
      return {
        p: it.price.id,
        c: it.cnt
      }
    });
    dispatch(backendActions.makeOrder(form, ord));
    // console.log("Submit", form, ord);
  }

  handleCloseMessage() {
    this.props.dispatch(backendActions.closeOrder());
  }

  handleDeleteItem(item) {
    this.props.dispatch(cartActions.removeItem(item));
  }

  validateName(txt) {
    return txt.length > 3;
  }

  validatePhone(txt) {
    return /\+380 \d\d\ \d\d\d\-\d\d\-\d\d/.test(txt);
  }

  renderTableHeader() {
    return (<TableHead>
      <TableRow>
        <TableCell>Наименование</TableCell>
        <TableCell align="right">Цена</TableCell>
        <TableCell align="center">Кол</TableCell>
        <TableCell align="right">Сумма</TableCell>
        <TableCell></TableCell>
      </TableRow>
    </TableHead>);
  }

  renderTableFooter() {
    const { cart } = this.props;
    const tot = _.reduce(cart, (acc, it) => acc + it.price.price * it.cnt, 0);

    return (<TableFooter>
      <TableRow>
        <TableCell></TableCell>
        <TableCell></TableCell>
        <TableCell></TableCell>
        <TableCell align="right">{tot}</TableCell>
        <TableCell size="small"></TableCell>
      </TableRow>
    </TableFooter>);
  }

  renderTableBody() {
    const { cart } = this.props;

    const rows = cart.map(item => {
      const link = kindToLink[item.prod.kind] + item.prod.name;
      return (<TableRow key={item.id}>
        <TableCell>
          <NavLink to={link} >{item.prod.name + ' (' + item.price.name + ')'}</NavLink>
        </TableCell>
        <TableCell align="right">{item.price.price}</TableCell>
        <TableCell align="center">{item.cnt}</TableCell>
        <TableCell align="right">{item.price.price * item.cnt}</TableCell>
        <TableCell>
          <IconButton aria-label="Delete" className="m-2" onClick={()=> this.handleDeleteItem(item.price)}>
                <DeleteIcon fontSize="small" />
          </IconButton>
        </TableCell>
      </TableRow>);
    });

    return (<TableBody>{rows}</TableBody>);
  }

  render() {
    const {
      fio,
      phone,
      adr,
      initFio,
      initPhone
    } = this.state;

    const {
      cart,
      order,
      orderError,
      classes
    } = this.props;

    let message = false;
    let messageOpen = false;
    let messageType = 'info';

    const validName = this.validateName(fio);
    const validPhone = this.validatePhone(phone);

    //console.log('order props', this.props);

    const hasItemsInCart = cart.length > 0;
    let itemList = (<ListGroup><ListGroupItem>Заказ пуст</ListGroupItem></ListGroup>);
    let sendButton = false;

    if(hasItemsInCart) {
      itemList = (<Table className="order-table">
        {this.renderTableHeader()}
        {this.renderTableBody()}
        {this.renderTableFooter()}
      </Table>);

      sendButton = (<Button type="submit">Заказать</Button>);
    }
    if(order != null) {
      if(order > 0) {
        messageOpen = true;
        messageType = 'success';
        message = `Заказ №${order} успешно оформлен`;
      }
      if(hasItemsInCart) {
        sendButton = <CircularProgress className={classes.progress} />;
      }
    }
    if(orderError != '') {
      messageOpen = true;
      messageType = 'error';
      message = orderError;
    }

    return (
      <Container fluid className="main-content-container px-4">
        <Row>
          <Col>
            <Card small>
              <CardHeader className="border-bottom">
                <h6 className="m-0">Заказать доставку онлайн</h6>
              </CardHeader>
              <ListGroup flush>
                <ListGroupItem className="p-3">
                  <Row>
                    <Col>
                      <Form onSubmit={this.onSubmitForm} >
                        <Row form>
                          <Col md="6" className="form-group">
                            <label htmlFor="feName">Имя</label>
                            <FormInput
                              id="feName"
                              type="text"
                              value={fio}
                              placeholder="Ваше имя"
                              required
                              valid={!initFio && validName}
                              invalid={!initFio && !validName}
                              onChange={this.onChangeName}
                            />
                            {!initFio && !validName && <FormFeedback>Укажите как к Вам обращаться</FormFeedback>}
                          </Col>
                          <Col md="6">
                            <label htmlFor="fePhone">Телефон</label>
                            <MaskedInput
                              id="fePhone"
                              className={cx("form-control", (!initPhone && validPhone) ? 'is-valid' : '', (!initPhone && !validPhone) ? 'is-invalid' : '')}
                              value={phone}
                              mask="+380 11 111-11-11"
                              required
                              onChange={this.onChangePhone}
                            />
                            {!initPhone && !validPhone && <FormFeedback>Укажите как с Вами связаться</FormFeedback>}
                          </Col>
                        </Row>

                        <FormGroup>
                          <label htmlFor="feInputAddress">Адрес доставки</label>
                          <FormInput
                            id="feInputAddress"
                            placeholder="пр. Гагарина, 8ж"
                            value={adr}
                            onChange={this.onChangeAdr}
                          />
                        </FormGroup>

                        {itemList}

                        {sendButton}

                      </Form>
                    </Col>
                  </Row>
                </ListGroupItem>
              </ListGroup>
            </Card>
          </Col>
        </Row>
        <p>
          С блюдами нашего меню вы можете ознакомиться  <NavLink to="/pizza">здесь</NavLink>.<br/>
          После отправки заказа наш менеджер свяжется с Вами для подтверждения.<br/>
          Если по техническим причинам мы не связались с Вами, Вы можете <NavLink to="/contacts">связаться с нами</NavLink>.<br/>
          Доставка блюд осуществляется в течении 2 часов с момента заказа на сайте.
        </p>
        <Snackbar
          anchorOrigin={{
            vertical: 'top',
            horizontal: 'right',
          }}
          open={messageOpen}
          autoHideDuration={6000}
          onClose={this.handleCloseMessage}
        >
          <SnackbarContentWithButton
            onClose={this.handleCloseMessage}
            variant={messageType}
            message={message}
          />
        </Snackbar>
      </Container>
    );
  }
}


function mapStateToProps(state) {
  const menu = backendSelectors.getMenu(state);
  const order = backendSelectors.getOrder(state);
  const orderError = backendSelectors.getOrderError(state);
  const cart = cartSelectors.getCart(state);
  return {
    menu,
    order,
    orderError,
    cart
  };
}

export default connect(mapStateToProps)(Order);

