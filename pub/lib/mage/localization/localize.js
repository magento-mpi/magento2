mage = {};

mage.Localize = function(culture) {
    this.localize = Globalize;
    if (culture == null){
        this.localize.culture('en');
    }else{
        this.localize.culture(culture);
    }
    this.dateFormat = ['d', 'D', 'f', 'F', 'M', 'S', 't', 'T', 'Y'];
    this.numberFormat = ['n', 'n1', 'n3', 'd', 'd2', 'd3', 'p', 'p1', 'p3', 'c', 'c0'];
};

mage.Localize.prototype.name = function() {
    return this.localize.culture().name;
};

mage.Localize.prototype.date = function(dateParam, format) {
    if (this.dateFormat.indexOf(format.toString()) < 0){
        return 'Invalid date formatter'
    }
    if(dateParam instanceof Date){
        return this.localize.format(dateParam, format);
    }
    var d = new Date(dateParam.toString());
    if (d == null || d.toString === 'Invalid Date'){
        return d.toString;
    }else{
        return this.localize.format(d, format);
    }
};

mage.Localize.prototype.number = function(numberParam, format) {
    if (this.numberFormat.indexOf(format.toString()) < 0){
        return 'Invalid date formatter'
    }
    if(typeof numberParam === 'number'){
        return this.localize.format(numberParam, format);
    }
    var num = Number(numberParam);
    if (num == null || isNaN(num)){
        return numberParam;
    }else{
        return this.localize.format(num, format);
    }
};

mage.Localize.prototype.currency = function(currencyParam) {
    if(typeof currencyParam === 'number'){
        return this.localize.format(currencyParam, 'c');
    }
    var num = Number(currencyParam);
    if (num == null || isNaN(num)){
        return currencyParam;
    }else{
        return this.localize.format(num, 'c');
    }
};