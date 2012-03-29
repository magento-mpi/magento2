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
    refresh: function(elem) {
        formId = this.formId;
        if (elem) Element.addClassName(elem, 'refreshing');
        new Ajax.Request(this.url, {
            onSuccess: function (response) {
                if (response.responseText.isJSON()) {
                    var json = response.responseText.evalJSON();
                    if (!json.error && json.imgSrc) {
                        $(formId).writeAttribute('src', json.imgSrc);
                        if (elem) Element.removeClassName(elem, 'refreshing');
                    } else {
                        if (elem) Element.removeClassName(elem, 'refreshing');
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

document.observe('billing-request:completed', function(event) {
    if (typeof window.checkout != 'undefined') {
        if (window.checkout.method == 'guest' && $('guest_checkout')){
            $('guest_checkout').captcha.refresh()
        }
        if (window.checkout.method == 'register' && $('register_during_checkout')){
            $('register_during_checkout').captcha.refresh()
        }
    }
});


document.observe('login:setMethod', function(event) {
    switch(event.memo.method){
        case 'guest':
            if ($('register_during_checkout')) {
                $('captcha-input-box-register_during_checkout').hide();
                $('captcha-image-box-register_during_checkout').hide();
                $('captcha-input-box-guest_checkout').show();
                $('captcha-image-box-guest_checkout').show();

            }
            break;
        case 'register':
            if ($('guest_checkout')) {
                $('captcha-input-box-guest_checkout').hide();
                $('captcha-image-box-guest_checkout').hide();
                $('captcha-input-box-register_during_checkout').show();
                $('captcha-image-box-register_during_checkout').show();

            }
            break;
    }
});
