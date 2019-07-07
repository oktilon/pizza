/* eslint jsx-a11y/anchor-is-valid: 0 */

import React from "react";

const ContentItem = (props) => {
    const {
      contentItem
    } = props;

    const img = contentItem.pic ? contentItem.pic : 'none';
    const url = '/images/content/' + img + '.png';

    const style = {
        backgroundImage: `url('${url}')`
    }

    return (
        <li
            className="ingridient"
            style={style}
        >
            {contentItem.name}
        </li>
    );
}

export default ContentItem;
