/* eslint jsx-a11y/anchor-is-valid: 0 */

import React from "react";
import cx from "classnames";
import { NavLink  } from "react-router-dom";
import MaskedInput from 'react-maskedinput';
import { connect } from 'react-redux';
import * as backendSelectors from '../store/backend/reducer';
import * as cartSelectors from '../store/cart/reducer';
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
  FormCheckbox,
  FormSelect,
  Button  
} from "shards-react";

class Order extends React.Component {
  constructor(props) {
    super(props);

    this.onChangeName = this.onChangeName.bind(this);
    this.onChangePhone = this.onChangePhone.bind(this);
    this.onChangeAdr = this.onChangeAdr.bind(this);
    this.onSubmitForm = this.onSubmitForm.bind(this);

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
    console.log("Submit", ev);
  }

  validateName(txt) {
    return txt.length > 3;
  }

  validatePhone(txt) {
    return /\+380 \d\d\ \d\d\d\-\d\d\-\d\d/.test(txt);
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
      cart
    } = this.props;

    const validName = this.validateName(fio);
    const validPhone = this.validatePhone(phone);

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
                            placeholder="пр. Д.Яворницкого, 52" 
                            value={adr} 
                            onChange={this.onChangeAdr} 
                          />
                        </FormGroup>

                        <ListGroup>
                          {cart.map(item => <ListGroupItem key={item.id}>
                            <Row>
                              <Col>{item.prod.name + ' (' + item.price.name + ')'}</Col>
                              <Col>{item.price.price + ' x ' + item.cnt}</Col>
                              <Col>{item.price.price * item.cnt}</Col>
                            </Row>
                          </ListGroupItem>)}
                        </ListGroup>

                        {false &&
                          <Row form>
                            <Col md="6" className="form-group">
                              <label htmlFor="feInputCity">City</label>
                              <FormInput id="feInputCity" />
                            </Col>
                            <Col md="4" className="form-group">
                              <label htmlFor="feInputState">State</label>
                              <FormSelect id="feInputState">
                                <option>Choose...</option>
                                <option>...</option>
                              </FormSelect>
                            </Col>
                            <Col md="2" className="form-group">
                              <label htmlFor="feInputZip">Zip</label>
                              <FormInput id="feInputZip" />
                            </Col>
                            <Col md="12" className="form-group">
                              <FormCheckbox>
                                {/* eslint-disable-next-line */}I agree with your{" "}
                                <a href="#">Privacy Policy</a>.
                              </FormCheckbox>
                            </Col>
                          </Row>
                        }
                        <Button type="submit">Заказать</Button>
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
      </Container>
    );
  }
}


function mapStateToProps(state) {
  const menu = backendSelectors.getMenu(state);
  const cart = cartSelectors.getCart(state);
  return {
    menu,
    cart
  };
}

export default connect(mapStateToProps)(Order);

