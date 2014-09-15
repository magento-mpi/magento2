/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    '_'
], function(_) {
    var utils = {},
        atobSupport,
        btoaSupport;
    
    atobSupport = typeof atob === 'function';
    btoaSupport = typeof btoa === 'function';

    /** 
     * Base64 encoding/decoding methods.
     * First check for native support.
     */
    if( btoaSupport && atobSupport ){
         _.extend(utils, {
            atob: function(input){
                return window.atob(input);
            },

            btoa: function(input){
                return window.btoa(input);
            }
        });
    }
    else{
        _.extend(utils, {
            atob: function(input){
                return Base64.decode(input)

            btoa: function(input){
                return Base64.encode(input);
            }
        });
    }    


    utils.submitAsForm = function(params){  
        var form,
            field;

        form = document.createElement('form');

        form.setAttribute('method', params.method);
        form.setAttribute('action', params.action);

        _.each(params.data, function(value, name){
            field = document.createElement('input');

            if(typeof value === 'object'){
                value = JSON.stringify(value);
            }

            field.setAttribute('name', name);
            field.setAttribute('type', 'hidden');
            
            field.value = value;

            form.appendChild(field);
        });

        document.body.appendChild(form);

        return form.submit();
    };

    return utils;
});