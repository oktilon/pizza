import React from "react";
import PriceRow from "./PriceRow";
import PriceHeader from "./PriceHeader";
import ContentHeader from "./ContentHeader";
import ContentRow from "./ContentRow";
import ImageCell from './ImageCell';
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faChevronUp, faChevronDown } from '@fortawesome/free-solid-svg-icons';
import { Button } from "shards-react";

class MenuRow extends React.Component {
    render() {
        const { item, opened, isPrice, openItem, addNewPrice } = this.props;
        var childBody = false;
        var childHead = false;
        var openPrices = !opened || (opened && !isPrice);
        var openContent = !opened || (opened && isPrice);

        if(isPrice) {
            childHead = <PriceHeader addItem={addNewPrice} />
            childBody = _.map(item.prices, prc => <PriceRow item={prc} key={prc.id} />);
        } else {
            childHead = <ContentHeader />
            childBody = _.map(item.content, it => <ContentRow item={it} key={it.id} />);
        }

        const subGrid = (<table className="table mb-0 ml-2 mt-2">
            {childHead}
            <tbody>
                {childBody}
            </tbody>
        </table>);

        return (<>
            <tr>
                <td><ImageCell menu img={item.pic} /></td>
                <td>{item.kind}</td>
                <td>{item.name}</td>
                <td>{item.desc}</td>
                <td>{item.flags}</td>
                <td>
                    {openPrices && <Button size="sm" outline theme="info" onClick={()=>{openItem(item.id, true)}}><FontAwesomeIcon icon={faChevronDown} />Цены</Button>}
                    {!openPrices && <Button size="sm" outline theme="warning" onClick={()=>{openItem(null, true)}}><FontAwesomeIcon icon={faChevronUp} />Цены</Button>}
                    {openContent && <Button size="sm" outline theme="primary" onClick={()=>{openItem(item.id, false)}}><FontAwesomeIcon icon={faChevronDown} />Состав</Button>}
                    {!openContent && <Button size="sm" outline theme="warning" onClick={()=>{openItem(null, false)}}><FontAwesomeIcon icon={faChevronUp} />Состав</Button>}
                </td>
            </tr>
            {opened && <tr><td colSpan={6}>{subGrid}</td></tr>}
        </>);
    }
}

export default MenuRow;
