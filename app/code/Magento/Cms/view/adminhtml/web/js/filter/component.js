define([
    'Magento_Ui/js/framework/ko/scope',
    'Magento_Ui/js/framework/ko/view',
    'jquery/autocomplete/jquery.autocomplete'
], function(Scope, View, Autocomplete) {

    var Filters = Scope.extend({
        initialize: function(el) {
            this.def('myValue');

            this.myValue.subscribe(this.onChange, 'change');

            var auto = new Autocomplete( el.firstElementChild, {
                lookup: [
                    { value: 'Andorra', data: 'AD' },
                    { value: 'Zimbabwe', data: 'ZZ' }
                ]
            }, this.myValue);
        },

        onChange: function(value){
            console.log(value);
        }
    });

    return function(el, config, initial) {
        var massAction = new Filters(el);
    
        View.bind(el, massAction);
    }
});