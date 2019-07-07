/* eslint jsx-a11y/anchor-is-valid: 0 */

import React from "react";
import ContentItem from './ContentItem';

class ContentList extends React.Component {
  render() {
    const {
      content
    } = this.props;

    return (
        <ul className="card-text menu-content">
            {content.map( it => {
                return <ContentItem 
                    contentItem={it}
                    key={it.id}
                />
            })}
        </ul>
    );
  }
}

export default ContentList;
