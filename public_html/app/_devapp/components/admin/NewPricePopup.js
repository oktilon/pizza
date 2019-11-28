import React from 'react';

class NewPricePopup extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            name: '',
            value: 0
        };

        this.nameInput = null;

        this.onChangeVal = (e) => this._onChangeVal(e);
        this.onChangeName = (e) => this._onChangeName(e);
    }

    componentDidMount(){
        //this.nameInput.focus();
        setTimeout(() => this.nameInput.focus(), 100);
        //console.log('did-mount', this.nameInput);
    }    

    componentDidUpdate(prevProps, prevState) {
        if (prevState.value !== this.state.value) {
            this.props.onChangeVal(this.state.value);
        }
        if (prevState.name !== this.state.name) {
            this.props.onChangeName(this.state.name);
        }
    }

    _onChangeVal(e) {
        let value = e.target.value;

        this.setState({value: parseInt(value)});
    }

    _onChangeName(e) {
        let value = e.target.value;

        this.setState({name: value});
    }

    render() {
        return <form>
            <input 
                type="name" 
                placeholder="название" 
                className="mm-popup__input" 
                value={this.state.name} 
                onChange={this.onChangeName} 
                ref={(input) => { this.nameInput = input; }} 
            /><br/>
            <input 
                type="number" 
                placeholder="цена"
                className="mm-popup__input" 
                value={this.state.value > 0 ? this.state.value : ''} 
                onChange={this.onChangeVal} 
            />
        </form>;
    }
}

export default NewPricePopup;