import React from "react";
import classNames from "classnames";
import PropTypes from "prop-types";
import { Col } from "shards-react";

const PageTitle = ({ title, subtitle, className, ...attrs }) => {
  const classes = classNames(
    className,
    "text-center",
    // "text-md-left",
    "mx-auto",
    "mb-sm-0"
  );

  return (
    <Col xs="12" sm="4" className={classes} { ...attrs }>
      <h2 className="main-course">      
        <span>{title}</span>
      </h2>
    </Col>
  )
};

PageTitle.propTypes = {
  /**
   * The page title.
   */
  title: PropTypes.string,
  /**
   * The page subtitle.
   */
  subtitle: PropTypes.string
};

export default PageTitle;
