import React from "react";
import PriceRow from "./PriceRow";
import PriceHeader from "./PriceHeader";
import ContentHeader from "./ContentHeader";
import ContentRow from "./ContentRow";
import ImageCell from './ImageCell';
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faChevronUp, faChevronDown, faTrashAlt } from '@fortawesome/free-solid-svg-icons';
import { Button } from "shards-react";
import InlineTextEdit from '../common/InlineTextEdit';

class MenuRow extends React.Component {
    render() {
        const { item, opened, isPrice, isAux, openItem, addNewPrice, flags } = this.props;
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
                <td>
                    <InlineTextEdit
                        //validate={this.customValidateText}
                        activeClassName="editing"
                        text={item.name}
                        paramName="message"
                        //change={this.dataChanged}
                        style={{
                            backgroundColor: 'yellow',
                            minWidth: 150,
                            display: 'inline-block',
                            margin: 0,
                            padding: 0,
                            fontSize: 15,
                            outline: 0,
                            border: 0
                        }}

                    />
                </td>
                <td>{item.desc}</td>
                <td>
                    {_.map(flags, f => {
                        return item.flags > 0 && f.flags > 0 && (item.flags & f.flags) && <FontAwesomeIcon className="text-danger" key={f.id} icon={f.ico} />
                    })}
                </td>
                <td>
                    <Button
                        size="sm"
                        outline
                        theme="info"
                        onClick={()=>{
                            openItem(openPrices ? item.id : null, true);
                        }}
                    >
                        <FontAwesomeIcon icon={openPrices ? faChevronDown : faChevronUp} className="mr-2" />
                        Цены
                    </Button>
                    <Button
                        size="sm"
                        outline
                        theme="primary"
                        onClick={()=>{
                            openItem(openContent ? item.id : null, false);
                        }}
                    >
                        <FontAwesomeIcon icon={openContent ? faChevronDown : faChevronUp} className="mr-2" />
                        Состав
                    </Button>
                    <Button
                        size="sm"
                        outline
                        theme="success"
                        onClick={()=>{
                            openItem(openAuxiliary ? item.id : null, false);
                        }}
                    >
                        <FontAwesomeIcon icon={openAuxiliary ? faChevronDown : faChevronUp} className="mr-2" />
                        Доп.ингр.
                    </Button>
                </td>
            </tr>
            {opened && <tr><td colSpan={6}>{subGrid}</td></tr>}
        </>);
    }
}

export default MenuRow;
