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
        btoaSupport,
        _utf8_decode,
        _utf8_encode;
    
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
        keyStr = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
        
        _utf8_decode = function (utftext) {
            var string = "";
                i = 0,
                c = c1 = c2 = 0;

            while ( i < utftext.length ) {

                c = utftext.charCodeAt(i);

                if (c < 128) {
                    string += String.fromCharCode(c);
                    i++;
                }
                else if((c > 191) && (c < 224)) {
                    c2 = utftext.charCodeAt(i+1);
                    string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
                    i += 2;
                }
                else {
                    c2 = utftext.charCodeAt(i+1);
                    c3 = utftext.charCodeAt(i+2);
                    string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
                    i += 3;
                }
            }
            return string;
        };

        _utf8_encode = function (string) {
            var utftext = "",
                n,
                c;
            
            string = string.replace(/\r\n/g,"\n");
           
            for (n = 0; n < string.length; n++) {

                c = string.charCodeAt(n);

                if (c < 128) {
                    utftext += String.fromCharCode(c);
                }
                else if((c > 127) && (c < 2048)) {
                    utftext += String.fromCharCode((c >> 6) | 192);
                    utftext += String.fromCharCode((c & 63) | 128);
                }
                else {
                    utftext += String.fromCharCode((c >> 12) | 224);
                    utftext += String.fromCharCode(((c >> 6) & 63) | 128);
                    utftext += String.fromCharCode((c & 63) | 128);
                }
            }
            return utftext;
        },

        _.extend(utils, {
            atob: function(input){
                var output = "",
                    chr1, chr2, chr3,
                    enc1, enc2, enc3, enc4,
                    i = 0;

                input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");

                while (i < input.length) {

                    enc1 = keyStr.indexOf(input.charAt(i++));
                    enc2 = keyStr.indexOf(input.charAt(i++));
                    enc3 = keyStr.indexOf(input.charAt(i++));
                    enc4 = keyStr.indexOf(input.charAt(i++));

                    chr1 = (enc1 << 2) | (enc2 >> 4);
                    chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
                    chr3 = ((enc3 & 3) << 6) | enc4;

                    output = output + String.fromCharCode(chr1);

                    if (enc3 != 64) {
                        output = output + String.fromCharCode(chr2);
                    }
                    if (enc4 != 64) {
                        output = output + String.fromCharCode(chr3);
                    }
                }
                output = _utf8_decode(output);

                return output;
            },

            btoa: function(input){
                var output = "",
                    chr1, chr2, chr3, enc1, enc2, enc3, enc4,
                    i = 0;

                input = _utf8_encode(input);

                while (i < input.length) {

                    chr1 = input.charCodeAt(i++);
                    chr2 = input.charCodeAt(i++);
                    chr3 = input.charCodeAt(i++);

                    enc1 = chr1 >> 2;
                    enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
                    enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
                    enc4 = chr3 & 63;

                    if (isNaN(chr2)) {
                        enc3 = enc4 = 64;
                    } else if (isNaN(chr3)) {
                        enc4 = 64;
                    }
                    output = output +
                    keyStr.charAt(enc1) + keyStr.charAt(enc2) +
                    keyStr.charAt(enc3) + keyStr.charAt(enc4);
                }

                return output;
            }
        });
    }    


    utils.submitAsForm = function(config, data){  
        var form,
            field;

        form = document.createElement('form');

        form.setAttribute('method', config.method);
        form.setAttribute('action', config.action);

        _.each(data, function(value, name){
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