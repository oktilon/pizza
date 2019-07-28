import React from "react";
import PropTypes from "prop-types";


class InlineTextEdit extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            isEditing: this.props.isEditing || false,
            text: this.props.text || ""
        };

        this.handleFocus = this.handleFocus.bind(this);
        this.handleChange = this.handleChange.bind(this);
    }

    handleFocus() {
        if (this.state.isEditing) {
            if (typeof this.props.onFocusOut === 'function') {
                this.props.onFocusOut(this.state.text);
            }
        } else {
            if (typeof this.props.onFocus === 'function') {
                this.props.onFocus(this.state.text);
            }
        }

        this.setState({
            isEditing: !this.state.isEditing
        });
    }

    handleChange() {
        this.setState({
            text: this.textInput.value
        });
    }

    render() {
        const { isEditing, text } = this.state;
        var _this2 = this;

        if (isEditing) {
            const wd = (text.length * 15) + 'px';
            return (<div>
                <input
                    type='text'
                    className={this.props.inputClassName}
                    ref={ input => {
                        _this2.textInput = input;
                    }}
                    value={text}
                    onChange={this.handleChange}
                    onBlur={this.handleFocus}
                    style={{
                        width: this.props.inputWidth,
                        height: this.props.inputHeight,
                        fontSize: this.props.inputFontSize,
                        fontWeight: this.props.inputFontWeight,
                        borderWidth: this.props.inputBorderWidth
                    }}
                    maxLength={this.props.inputMaxLength}
                    placeholder={this.props.inputPlaceHolder}
                    tabIndex={this.props.inputTabIndex}
                    autoFocus={true}
                />
            </div>);
        }

        return (<div>
            <label
                className={this.props.labelClassName}
                onClick={this.handleFocus}
                style={{
                        fontSize: this.props.labelFontSize,
                        fontWeight: this.props.labelFontWeight
                }}
            >
                {text}
            </label>
        </div>);
    }
}

export default InlineTextEdit;


InlineTextEdit.propTypes = {
    text: PropTypes.string.isRequired,
    isEditing: PropTypes.bool,

    labelClassName: PropTypes.string,
    labelFontSize: PropTypes.string,
    labelFontWeight: PropTypes.string,

    inputMaxLength: PropTypes.number,
    inputPlaceHolder: PropTypes.string,
    inputTabIndex: PropTypes.number,
    inputWidth: PropTypes.string,
    inputHeight: PropTypes.string,
    inputFontSize: PropTypes.string,
    inputFontWeight: PropTypes.string,
    inputClassName: PropTypes.string,
    inputBorderWidth: PropTypes.string,

    onFocus: PropTypes.func,
    onFocusOut: PropTypes.func
};