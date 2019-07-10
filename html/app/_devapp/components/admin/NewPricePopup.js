import React from 'react';

class NewPricePopup extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            value: this.props.defaultValue
        };

        this.onChange = (e) => this._onChange(e);
    }

    componentDidUpdate(prevProps, prevState) {
        if (prevState.value !== this.state.value) {
            this.props.onChange(this.state.value);
        }
    }

    _onChange(e) {
        let value = e.target.value;

        this.setState({value: value});
    }

    render() {
        return <form>
            <input type="number" placeholder={this.props.placeholder} className="mm-popup__input" value={this.state.value} onChange={this.onChange} />
        </form>;
    }
}

export default NewPricePopup;