export default class MenuActions {
    addNewPrice = () => {};
    addNewContent = () => {};
    addNewAux = () => {};
    delItem = (it, sub) => {};

    constructor(pr, cont, aux, del) {
        this.addNewPrice = pr;
        this.addNewContent = cont;
        this.addNewAux = aux;
        this.delItem = del;
    }
}