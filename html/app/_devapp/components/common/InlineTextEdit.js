import React from "react";
import PropTypes from "prop-types";


class InlineTextEdit extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            isEditing: this.props.isEditing || false,
            text: this.props.text || "",
            undo: this.props.text || ""
        };

        this.handleFocus = this.handleFocus.bind(this);
        this.handleChange = this.handleChange.bind(this);
        this.handleKeyUp = this.handleKeyUp.bind(this);
    }

    handleFocus() {
        if (this.state.isEditing) {
            if (typeof this.props.onFocusOut === 'function') {
                this.props.onFocusOut(this.state.text);
            }
            this.setState({
                undo: this.state.text
            });
            if (typeof this.props.onChange === 'function') {
                this.props.onChange(this.state.text);
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
            text: this.textInput.value,
        });
    }

    handleKeyUp(ev) {
        if(ev.keyCode == 13) {
            this.setState({
                isEditing: false,
                undo: this.state.text
            });
            if (typeof this.props.onChange === 'function') {
                this.props.onChange(this.state.text);
            }
        }
        if(ev.keyCode == 27) {
            this.setState({
                isEditing: false,
                text: this.state.undo
            });
        }
    }

    render() {
        const { isEditing, text } = this.state;
        var _this2 = this;

        if (isEditing) {
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
                    onKeyUp={this.handleKeyUp}
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
    onFocusOut: PropTypes.func,
    onChange: PropTypes.func,
};