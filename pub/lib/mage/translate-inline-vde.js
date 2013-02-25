/**
 * {license_notice}
 *
 * @category    Mage
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true */
(function($) {
    $.widget("mage.translateInlineIconVde", {
        options: {
          img: '',
          offsetLeft: -33,
          template: '<img src="${img}">',
          translateForm: {
              template: '<form id="${data.id}">{{each(i, item) data.items}}' +
                  '<input id="perstore_${i}" name="translate[${i}][perstore]" type="hidden" value="0"/>' +
                  '<input name="translate[${i}][original]" type="hidden" value="${item.scope}::${escape(item.original)}"/>' +
                  '<input id="custom_${i}" name="translate[${i}][custom]" value="${escape(item.translated)}" />' +
                  '{{/each}}</form>',
              data: {
                  id: 'translate-inline-form',
                  message: 'Please refresh the page to see your changes after submitting this form.'
              }
          },
        },

        /**
         * Translate Inline creation
         * @protected
         */
        _create: function() {
            var offset = this.element.offset();
            this.template = $.tmpl(this.options.template, this.options).css({
              position: 'absolute',
              cursor: 'pointer',
              'z-index': 2000,
              top: offset.top,
              left: offset.left + this.element.outerWidth() + this.options.offsetLeft
            }).appendTo('body');

            var self = this;
            this.template.on('click', function() {
                $.template("translateInlineVde", self.options.translateForm.template);

                $('#translateDialog')
                    .html($.tmpl("translateInlineVde", {
                    data: $.extend({items: $(self.element).data('translate')},
                        self.options.translateForm.data),
                    escape: $.mage.escapeHTML
                }));

                $("#translateDialog").dialog("option", {
                    position: { of : self.element, my: "left top", at: "left-3 top-3" },
                    width: self.element.width(),
                    height: "auto",
                    minHeight : 0,
                    buttons: [
                        {
                            text: "Cancel",
                            click: function() {
                                $('body').addClass('trnslate-inline-area');
                                $("#translateDialog").dialog('close');
                                $('[data-translate]').translateInlineIconVde('show');
                            }
                        },
                        {
                            id: "saveTranslate",
                            text: "Save",
                            click: $.proxy(self._formSubmit, self),
                        }
                    ]
                });
                $('body').removeClass('trnslate-inline-area');
                $('[data-translate]').translateInlineIconVde('hide');
                $("#translateDialog").dialog("open");
            });
        },

        hide: function() {
            this.template.hide();
        },

        show: function() {
            this.template.show();
        },

        _destroy: function() {
            this.template.remove();
        },

        _formSubmit: function() {
            console.log("hello");

            var parameters = jQuery.param({area: this.options.area}) +
                '&' + jQuery('#' + this.options.translateForm.data.id).serialize();

            jQuery.ajax({
                url: this.options.ajaxUrl,
                type: 'POST',
                data: parameters,
                context: $('#translateDialog'),
                showLoader: true
            }).complete(jQuery.proxy(function() {
                $('body').addClass('trnslate-inline-area');
                $("#translateDialog").dialog('close');
                $('[data-translate]').translateInlineIconVde('show');
            }, this));
        }
    });

    /*
     * @TODO move the "escapeHTML" method into the file with global utility functions
     */
    $.extend(true, $, {
        mage: {
            escapeHTML: function(str) {
                return str ?
                    jQuery('<div/>').text(str).html().replace(/"/g, '&quot;'):
                    false;
            }
        }
    });
})(jQuery);
