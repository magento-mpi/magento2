/**
 * {license_notice}
 *
 * @category    Mage
 * @package     js
 * @copyright   {copyright}
 * @license     {license_link}
 */
var Captcha = Class.create();
Captcha.prototype = {
    initialize: function(url, formId){
        this.url = url;
        this.formId = formId;
    },
    refresh: function() {
        formId = this.formId;
        new Ajax.Request(this.url, {
            onSuccess: function (response) {
                if (response.responseText.isJSON()) {
                    var json = response.responseText.evalJSON();
                    if (!json.error && json.imgSrc) {
                        $(formId).writeAttribute('src', json.imgSrc);
                    }
                }
            },
            method: 'post',
            parameters: {
                'formId'   : this.formId
            }
        });
    }
};

document.observe('billing-request:completed', function(event){
    if (window.checkout !== undefined){
        if (window.checkout.method == 'guest' && $('guest_checkout')){
            window.captcha_guest_checkout.refresh()
        }
        if (window.checkout !== undefined && window.checkout.method== 'register' && $('register_during_checkout')){
            window.captcha_register_during_checkout.refresh()
        }
    }
});


document.observe('login:setMethod', function(event){
    switch(event.memo.method){
        case 'guest':
            if ($('register_during_checkout')) {
                $('captcha-input-box-register_during_checkout').hide();
                $('captcha-image-box-register_during_checkout').hide();
            }
            break;
        case 'register':
            if ($('guest_checkout')) {
                $('captcha-input-box-guest_checkout').hide();
                $('captcha-image-box-guest_checkout').hide();
            }
            break;
    }
});
