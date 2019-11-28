import React from "react";
import PriceRow from "./PriceRow";
import PriceHeader from "./PriceHeader";
import ContentHeader from "./ContentHeader";
import ContentRow from "./ContentRow";
import ImageCell from './ImageCell';
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faChevronDown, faTrashAlt } from '@fortawesome/free-solid-svg-icons';
import { Button } from "shards-react";
import InlineTextEdit from '../common/InlineTextEdit';
import * as OpenTypes from '../../constants';
import * as Conf from '../../conf';
import * as cx from 'classnames';
import Paper from '@material-ui/core/Paper';
import Table from '@material-ui/core/Table';
import TableBody from '@material-ui/core/TableBody';
import TableCell from '@material-ui/core/TableCell';
import TableHead from '@material-ui/core/TableHead';
import TableRow from '@material-ui/core/TableRow';

class MenuRow extends React.Component {
    render() {
        const { item, opened, openMode, openItem, actions } = this.props;
        var childBody = false;
        var childHead = false;
        var openPrices = !opened || (opened && openMode != OpenTypes.OPEN_PRICES);
        var openContent = !opened || (opened && openMode != OpenTypes.OPEN_CONTENT);
        var openAuxiliary = !opened || (opened && openMode != OpenTypes.OPEN_AUXILIARY);

        switch(openMode) {
            case OpenTypes.OPEN_PRICES:
                childHead = <PriceHeader addItem={actions.addNewPrice} />
                childBody = _.map(item.prices, prc => <PriceRow item={prc} key={prc.id} />);
                break;

            case OpenTypes.OPEN_CONTENT:
                childHead = <ContentHeader addItem={actions.addNewContent}  hasPrice={false} />
                childBody = _.map(item.content, it => <ContentRow item={it} key={it.id} hasPrice={false} />);
                break;

            case OpenTypes.OPEN_AUXILIARY:
                childHead = <ContentHeader addItem={actions.addNewAux} hasPrice={true} />
                childBody = _.map(item.aux, it => <ContentRow item={it} key={it.id} hasPrice={true} />);
                break;
        }

        const subGrid = (<Paper className="mb-0 ml-2 mt-2">
            <Table>
                {childHead}
                <TableBody>
                    {childBody}
                </TableBody>
            </Table>
        </Paper>);

        const kind = _.find(Conf.kinds, x => x.id == item.kind);

        return (<>
            <tr>
                <td><ImageCell type="m" id={item.id} /></td>
                <td>{kind.name}</td>
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
                    {_.map(Conf.stats, f => {
                        return item.flags > 0 && f.flags > 0 && (item.flags & f.flags) && <FontAwesomeIcon className="text-danger" key={f.id} icon={f.ico} />
                    })}
                </td>
                <td>
                    <Button
                        size="sm"
                        outline
                        theme="info"
                        onClick={()=>{
                            openItem(openPrices ? item.id : null, OpenTypes.OPEN_PRICES);
                        }}
                    >
                        <FontAwesomeIcon
                            icon={faChevronDown}
                            className={cx('mr-2', 'icon-open', {'icon-turn': !openPrices})}
                        />
                        Цены
                    </Button>
                    <Button
                        size="sm"
                        outline
                        theme="primary"
                        onClick={()=>{
                            openItem(openContent ? item.id : null, OpenTypes.OPEN_CONTENT);
                        }}
                    >
                        <FontAwesomeIcon
                            icon={faChevronDown}
                            className={cx('mr-2', 'icon-open', {'icon-turn': !openContent})}
                        />
                        Состав
                    </Button>
                    <Button
                        size="sm"
                        outline
                        theme="success"
                        onClick={()=>{
                            openItem(openAuxiliary ? item.id : null, OpenTypes.OPEN_AUXILIARY);
                        }}
                    >
                        <FontAwesomeIcon
                            icon={faChevronDown}
                            className={cx('mr-2', 'icon-open', {'icon-turn': !openAuxiliary})}
                        />
                        Допы
                    </Button>
                </td>
            </tr>
            {opened && <tr><td colSpan={6}>{subGrid}</td></tr>}
        </>);
    }
}

export default MenuRow;
