import React from "react";
import PropTypes from "prop-types";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import * as Conf from "~/conf";

const IngridientFlag = ({ flag }) => (
    <div className="w-100">
        {Conf.ingridientFlags.map(f => (f.f & flag) > 0 && <FontAwesomeIcon icon={f.i} key={f.f} />)}
    </div>
);

IngridientFlag.propTypes = {
    flag: PropTypes.number
}

IngridientFlag.defaultProps = {
    flag: 0
}

export default IngridientFlag;