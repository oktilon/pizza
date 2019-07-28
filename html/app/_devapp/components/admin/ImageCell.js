import React from "react";
import PropTypes from "prop-types";

const ImageCell = ({ type, id, wd }) => {
    return (<img src={`/image/${type}/${id}`} width={wd} />);
}

ImageCell.propTypes = {
    type : PropTypes.string,
    id : PropTypes.number,
    wd : PropTypes.number
}

ImageCell.defaultProps = {
    type  : 'm',
    id    : 0,
    wd    : 24
}

export default ImageCell;
