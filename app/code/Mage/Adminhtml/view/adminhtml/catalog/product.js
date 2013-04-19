/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

var Product = {};

(function ($) {
    $.widget("mage.productAttributes", {
        _create: function () {
            this._on({'click':'_showPopup'});
        },
        _prepareUrl: function() {
            return this.options.url +
                (/\?/.test(this.options.url) ? '&' : '?') +
                'set=' + $('#attribute_set_id').val();
        },
        _showPopup: function (event) {
            var wrapper = $('<div id="create_new_attribute"/>').appendTo('body').dialog({
                title: 'New Attribute',
                minWidth: 1000,
                minHeight: 700,
                modal: true,
                resizeStop: function(event, ui) {
                    iframe.height($(this).outerHeight() + 'px');
                    iframe.width($(this).outerWidth() + 'px');
                }
            });
            wrapper.mage('loader', {showOnInit: true});
            var iframe = $('<iframe id="create_new_attribute_container">').attr({
                src: this._prepareUrl(event),
                frameborder: 0,
                style: "position:absolute;top:58px;left:0px;right:0px;bottom:0px"
            });
            iframe.on('load', function () {
                wrapper.mage('loader', 'hide');
                $(this).css({
                    height:  wrapper.outerHeight() + 'px',
                    width: wrapper.outerWidth() + 'px'
                });
            });
            wrapper.append(iframe);
            wrapper.on('dialogclose', function () {
                var dialog = this;
                //ie9 break script execution on iframe removing
                window.setInterval(function() {
                    $(dialog).remove();
                }, 50);
            });
        }
    });

    $.widget("mage.configurableAttribute", $.mage.productAttributes, {
        _prepareUrl: function() {
            var name = $('#configurable-attribute-selector').val();
            return this.options.url +
                (/\?/.test(this.options.url) ? '&' : '?') +
                'set=' + window.encodeURIComponent($('#attribute_set_id').val()) +
                '&attribute[frontend_label]=' +
                window.encodeURIComponent(name) +
                '&attribute[attribute_code]=' +
                window.encodeURIComponent(name.replace(/[^a-z0-9]+/g, '_').toLowerCase())
                + "&attribute[apply_to]=simple,configurable,virtual";
        }
    });
})(jQuery);

var onInitDisableFieldsList = [];

function toogleFieldEditMode(toogleIdentifier, fieldContainer) {
    if ($(toogleIdentifier).checked) {
        enableFieldEditMode(fieldContainer);
    } else {
        disableFieldEditMode(fieldContainer);
    }
}

function disableFieldEditMode(fieldContainer) {
    $(fieldContainer).disabled = true;
    if ($(fieldContainer + '_hidden')) {
        $(fieldContainer + '_hidden').disabled = true;
    }
}

function enableFieldEditMode(fieldContainer) {
    $(fieldContainer).disabled = false;
    if ($(fieldContainer + '_hidden')) {
        $(fieldContainer + '_hidden').disabled = false;
    }
}

function initDisableFields(fieldContainer) {
    onInitDisableFieldsList.push(fieldContainer);
}

function onCompleteDisableInited() {
    onInitDisableFieldsList.each( function(item) {
        disableFieldEditMode(item);
    });
}

function onUrlkeyChanged(urlKey) {
    urlKey = $(urlKey);
    var hidden = urlKey.next('input[type=hidden]');
    var chbx = urlKey.next('input[type=checkbox]');
    var oldValue = chbx.value;
    chbx.disabled = (oldValue == urlKey.value);
    hidden.disabled = chbx.disabled;
}

function onCustomUseParentChanged(element) {
    var useParent = (element.value == 1) ? true : false;
    element.up(2).select('input', 'select', 'textarea').each(function(el){
        if (element.id != el.id) {
            el.disabled = useParent;
        }
    });
    element.up(2).select('img').each(function(el){
        if (useParent) {
            el.hide();
        } else {
            el.show();
        }
    });
}

Event.observe(window, 'load', onCompleteDisableInited);
