import React from "react";
import { NavLink  } from "react-router-dom";
import { connect } from 'react-redux';
import * as backendSelectors from '../store/backend/reducer';
import {
  Container,
  Card,
  CardHeader,
  ListGroup,
  ListGroupItem,
  Row,
  Col,
} from "shards-react";

const Contacts = ({ data }) => (
  <Container fluid className="main-content-container px-4">
  <Row>
    <Col>
      <Card small>
        <CardHeader className="border-bottom">
          <h6 className="m-0">Контакты</h6>
        </CardHeader>
        <ListGroup flush>
          <ListGroupItem className="p-3">
            <div>
                <p>На нашем сайте <NavLink to="/">Order Pizza Youssef</NavLink> Вы найдете широкий ассортимент блюд тунисийской и средиземноморской кухни. </p>
                <p>Мы рады слышать Ваши отзывы, пожелания и предложения. Мы дарим Вам аппетит и его удовлетворение, а от Вас ждем Ваших радостных эмоций.
                <br/>
                Мы только запускаем наш сервис по доставке, но на рынке фаст фуда уже непрерывно работаем с 2010 года. Постепенно мы совершенствуемся и добавляем свежие акции.
                В нашем <NavLink to="/blog">блоге</NavLink> мы будем делиться с нашими клиентами новинками и промо.
                Добавляйтесь к нам в социальных сетях <a
                  href="https://www.facebook.com/Pizza-youssef-351759775196922/" target="_blank">Facebook</a> и <a
                  href="https://www.instagram.com/orderpizza.dp.ua/" target="_blank">Instagram</a> чтобы не пропустить лучшие предложения!</p>
                <table className="contact-details">
                    <tbody>
                        <tr><td>EMAIL</td><td>:</td><td>{data.email}</td></tr>
                        <tr><td>АДРЕС</td><td>:</td><td>{data.adr}</td></tr>
                        <tr><td>ТЕЛЕФОН</td><td>:</td><td>{data.phone}</td></tr>
                    </tbody>
                </table>
            </div>
          </ListGroupItem>
        </ListGroup>
      </Card>
    </Col>
  </Row>
</Container>
);

function mapStateToProps(state) {
    const data = backendSelectors.getContactsData(state);
    return {
        data
    };
}

export default connect(mapStateToProps)(Contacts);