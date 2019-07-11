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
                <h6 className="m-0">Блог</h6>
              </CardHeader>
              <ListGroup flush>
                <ListGroupItem className="p-3">
                    <div id="blogs">
                        <div className="sidebar">
                            <div className="posts">
                                <h3>Последние посты</h3>
                                <ul>
                                    <li><NavLink to="/blog">История пиццы</NavLink></li>
                                    <li><NavLink to="/blog">Почему пиццу едят руками</NavLink></li>
                                    <li><NavLink to="/blog">Чья пицца эталон? Италия или Америка!?</NavLink></li>
                                    <li><NavLink to="/blog">Какой фастфуд может быть полезным</NavLink></li>
                                    <li><NavLink to="/blog">10 удивительных фактов о еде</NavLink></li>
                                </ul>
                            </div>
                            <div className="archives">
                                <h3>Архив</h3>
                                <ul>
                                    <li><NavLink to="/blog">Октябрь 2017</NavLink></li>
                                    <li><NavLink to="/blog">Сентябрь 2017</NavLink></li>
                                    <li><NavLink to="/blog">Август 2017</NavLink></li>
                                    <li><NavLink to="/blog">Июль 2017</NavLink></li>
                                    <li><NavLink to="/blog">Июнь 2017</NavLink></li>
                                    <li><NavLink to="/blog">Май 2017</NavLink></li>
                                    <li><NavLink to="/blog">Апрель 2017</NavLink></li>
                                    <li><NavLink to="/blog">Март 2017</NavLink></li>
                                    <li><NavLink to="/blog">Февраль 2017</NavLink></li>
                                    <li><NavLink to="/blog">Январь 2017</NavLink></li>
                                    <li><NavLink to="/blog">Декабрь 2016</NavLink></li>
                                    <li><NavLink to="/blog">Ноябрь 2016</NavLink></li>
                                </ul>
                            </div>
                        </div>
                        <div className="section">
                            <p><b>Добро пожаловать в блог <NavLink to="/">Order Pizza Youssef</NavLink> о вкуcном и горячем.</b></p>
                            <p>Здесь мы ведем наш блог, наши истории. Периодически мы выставляем промо-акции для постоянных клиентов.
                            </p>
                            <p>Добавляйтесь к нам в социальных сетях <a
                                href="https://www.facebook.com/" target="_blank">Facebook</a> и <a
                                href="https://www.instagram.com/orderpizza.dp.ua/" target="_blank">Instagram</a> чтобы быть постоянно в курсе событий.</p>
                        </div>
                    </div>
                </ListGroupItem>
              </ListGroup>
            </Card>
          </Col>
        </Row>
      </Container>
);

export default About;

