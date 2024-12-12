/*@params must be used with generic-table.js imported this will throw an error if generic-table.js was 
not found on the page */


class TableEngineFilter extends TableEngine {
    constructor(){
        super();
        console.log(super.data);
    }
}