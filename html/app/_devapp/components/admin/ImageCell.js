import React from "react";
import PropTypes from "prop-types";

const ImageCell = ({ ingr, menu, price, img }) => {
    var folder = 'menu';
    var pic = '' + img;
    if(ingr) folder = 'content';
    if(price) folder = 'price';
    if(!img || img == '') {
        folder = 'menu';
        pic = 'none.png';
    }
    return (<img src={`/images/${folder}/${pic}`} width={24} />);
}

ImageCell.propTypes = {
    ingr : PropTypes.bool,
    menu : PropTypes.bool,
    price : PropTypes.bool,
    img   : PropTypes.string
}

ImageCell.defaultProps = {
    ingr  : false,
    menu  : true,
    price : false,
    img   : ''
}

export default ImageCell;
