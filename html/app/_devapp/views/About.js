/* eslint jsx-a11y/anchor-is-valid: 0 */

import React from "react";
import { NavLink  } from "react-router-dom";
import {
  Container,
  Card,
  CardHeader,
  ListGroup,
  ListGroupItem,
  Row,
  Col,
} from "shards-react";

const About = () => (
      <Container fluid className="main-content-container px-4">
        <Row>
          <Col>
            <Card small>
              <CardHeader className="border-bottom">
                <h6 className="m-0">О нас</h6>
              </CardHeader>
              <ListGroup flush>
                <ListGroupItem className="p-3">
                    <div id="aboutus">
                        <h3>Кухня Средиземноморья</h3>
                        <p>
                            Вкус ближнего востока - просто! В основе нашего заведения лежит добрая средиземноморская атмосфера,
                            тунисйский вкус и скорость обслуживания наших любимых клиентов.
                            Попробовав наши <NavLink to="/fast-food">быстрые обеды</NavLink> вы уже не сможете устоять перед тем,
                            чтобы прийти снова и снова. Блюда Туниса удивляют своей простотой и экзотическим вкусом.
                            Мы рады дарить вам приятные эмоции и полезные продукты,
                            входящие в состав каждой пиццы, булочки или обеда.
                        </p>
                        <h3>Доставка по городу</h3>
                        <p>
                            Территориально мы находимся в районе Нагорного рынка по пр. Гагарина, г.Днепр.
                            Вы можете как самостоятельно прийти к нам, так и заказать <NavLink to="/order">доставку</NavLink> еды в то место, где находитесь Вы.
                            Мы любим наших клиентов и создаем все условия для того, чтобы Вы смогли вкусно поесть там, где Вам это удобно.
                        </p>
                        <h3>Будьте частью нашего Сообщества</h3>
                        <p>
                            В нашем <NavLink to="/blog">блоге</NavLink> мы будем делиться с нашими клиентами новинками и промо.
                            Подписывайтесь в <a href="https://www.facebook.com/" target="_blank">Facebook</a> и <a href="https://www.instagram.com/orderpizza.dp.ua/" target="_blank">Instagram</a> - будем на связи, чтобы не пропустить лучшие предложения!
                            Если у Вас остались вопросы, <NavLink to="/contacts">свяжитесь с нами</NavLink>.
                        </p>
                    </div>
                </ListGroupItem>
              </ListGroup>
            </Card>
          </Col>
        </Row>
      </Container>
);

export default About;

