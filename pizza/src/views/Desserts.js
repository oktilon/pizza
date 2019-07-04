/* eslint jsx-a11y/anchor-is-valid: 0 */

import React from "react";

class Desserts extends React.Component {
  constructor(props) {
    super(props);

    this.state = {};
  }

  render() {

    return (
      <>
        <h2 class="main-course"><span>Пицца</span></h2>
        <div id="menus">
          <ul class="main">
            <li>
              { /*<!-- <h3>Курица</h3> --> */ }
              <ul>
                <li>
                  <span class="price">95/75 грн</span>
                  <b>Миста</b>
                  <p>(курица,ветчина,бекон,салями, лук,зелень)</p>
                </li>
                <li>
                  <span class="price">75/46 грн</span>
                  <b>Луи</b>
                  <p>(курица,грибы,помидор)</p>
                </li>
                <li>
                  <span class="price">90/65 грн</span>
                  <b>Юсеф</b>
                  <p>(курица,грибы,ананас,сладкий перец,маслины,помидоры,зелень)</p>
                </li>
              </ul>
            </li>
            <li>
              { /*<!-- <h3>Сыр</h3> --> */ }
              <ul>
                <li>
                  <span class="price">50/40 грн</span>
                  <b>Маргарита</b>
                  <p>(помидор,базилик)</p>
                </li>
                <li>
                  <span class="price">92/80 грн</span>
                  <b>4 сыра</b>
                  <p>(сыр дорблю, сыр моцарелла, сыр  гауда, сыр пармезан)</p>
                </li>
                <li>
                  <span class="price">70/55 грн</span>
                  <b>Вегетарианская</b>
                  <p>(микс овощей,лук,помидор,грибы,кукуруза)</p>
                </li>
              </ul>
            </li>
            <li>
              {/*<!-- <h3>Море</h3> -->*/}
              <ul>
                <li>
                  <span class="price">90/60 грн</span>
                  <b>Море</b>
                  <p>(морской коктель , крабовые палочки ,помидор,красный лук )</p>
                </li>
                <li>
                  <span class="price">85/65 грн</span>
                  <b>Нептун</b>
                  <p>(тунец,маслины,зелень)</p>
                </li>
              </ul>
            </li>
            <li>
              {/*<!-- <h3>Кальцоне</h3> -->*/}
              <ul>
                <li>
                  <span class="price">82/62 грн</span>
                  <b>Кальцоне</b>
                  <p>(курица,ветчина,салями,грибы)</p>
                </li>
                <li>
                  <span class="price">55/70 грн</span>
                  <b>Дьявола</b>
                  <p>(острый перец,салями)</p>
                </li>
              </ul>
            </li>
          </ul>
        </div>      
      </>
    );
  }
}

export default Desserts;
