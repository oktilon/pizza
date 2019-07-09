/* eslint jsx-a11y/anchor-is-valid: 0 */

import React from "react";
import cx from "classnames";
import { NavLink  } from "react-router-dom";
import MaskedInput from 'react-maskedinput';
import { connect } from 'react-redux';
import * as backendSelectors from '../store/backend/reducer';
import * as backendActions from '../store/backend/actions';
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

class Login extends React.Component {
  constructor(props) {
    super(props);

    this.onChangeLogin = this.onChangeLogin.bind(this);
    this.onChangePassword = this.onChangePassword.bind(this);
    this.onSubmitForm = this.onSubmitForm.bind(this);

    this.state = {
        login: '',
        password: '',
        showWarning: false,
        showError: false,
        logged: true
    };
  }

  onChangeLogin(ev) {
    const val = ev.target.value;
    const warn = this.state.showWarning ? val == '' : false;
    this.setState({login:val, showError:false, showWarning: warn});
  }

  onChangePassword(ev) {
    const val = ev.target.value;
    const warn = this.state.showWarning ? val == '' : false;
    this.setState({password:val, showError:false, showWarning: warn});
  }

  onSubmitForm(ev) {
    ev.preventDefault();
    const { login, password } = this.state;
    if(login == '' || password == '') {
        this.setState({showWarning:true, showError:false});
        return;
    }
    this.dispatch(backendActions.loginUser(usr, pwd));
  }

  render() {
    const {
        login,
        password,
        showWarning,
        showError,
        logged
    } = this.state;

    const {
        user
    } = this.props;

    let body = false;
    if(logged) {
        body = (<ListGroup flush>
            <ListGroupItem className="p-3">
                <Row>
                    <Col>
                        Вы зашли как <span>{user.login}</span>
                    </Col>
                </Row>
                <Row>
                    <Col>
                        <Button onClick={this.onLogOut} type="submit">Выйти</Button>
                    </Col>
                </Row>
            </ListGroupItem>
        </ListGroup>);
    } else {
        body = (<ListGroup flush>
            <ListGroupItem className="p-3">
                <Row>
                    <Col>
                        <Form onSubmit={this.onSubmitForm} >
                            <FormGroup>
                                <label htmlFor="feLogin">Имя</label>
                                <FormInput
                                    id="feLogin"
                                    type="text"
                                    value={login}
                                    placeholder="Ваше имя"
                                    required
                                    onChange={this.onChangeLogin}
                                />
                            </FormGroup>

                            <FormGroup>
                                <label htmlFor="fePassword">Телефон</label>
                                <FormInput
                                id="fePassword"
                                type="password"
                                value={password}
                                placeholder="Ваше пароль"
                                required
                                onChange={this.onChangePassword}
                                />
                            </FormGroup>

                            <FormGroup>
                                {showWarning && <FormFeedback>Укажите имя и пароль</FormFeedback>}
                                {showError && <FormFeedback>Неверній логин или пароль</FormFeedback>}
                            </FormGroup>

                            <Button type="submit">Войти</Button>
                        </Form>
                    </Col>
                </Row>
            </ListGroupItem>
        </ListGroup>);
    }

    return (
        <Container fluid className="main-content-container px-4" style={{width:"450px"}}>
            <Row>
                <Col>
                    <Card small width="400px">
                        <CardHeader className="border-bottom">
                            <h6 className="m-0">{logged ? "Выход" : "Вход"}</h6>
                        </CardHeader>
                        {body}
                    </Card>
                </Col>
            </Row>
        </Container>
    );
  }
}


function mapStateToProps(state) {
  const user = backendSelectors.getUser(state);
  return {
    user
  };
}

export default connect(mapStateToProps)(Login);

