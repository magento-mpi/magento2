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
    $.widget("mage.translateInlineDialogVde", {
        options: {
            translateForm: {
                template: '<form id="${data.id}">{{each(i, item) data.items}}' +
                    '<input id="perstore_${i}" name="translate[${i}][perstore]" type="hidden" value="0"/>' +
                    '<input name="translate[${i}][original]" type="hidden"' +
                    ' value="${item.scope}::${escape(item.original)}"/>' +
                    '<input id="custom_${i}" name="translate[${i}][custom]" value="${escape(item.translated)}"' +
                    ' class="translate-dialog-translate-value-input"/>' +
                    '{{/each}}</form>',
                data: {
                    id: "translate-inline-form"
                }
            },
            dialog: {
                id: "translate-dialog",
                autoOpen : false,
                dialogClass: "translate-dialog",
                draggable: false,
                modal: false,
                resizable: false,
                height: "auto",
                minHeight: 0,
                buttons: [{
                    text: "Cancel",
                    "class" : "translate-dialog-cancel",
                },
                {
                    text: "Save",
                    "class" : "translate-dialog-save",
                }]
            },
            templateName: "translateInlineDialogVdeTemplate",
            dataAttrName: "translate",
            onSubmitComplete: function() { },
            onCancel: function() { }
        },

        /**
         * Creates the translation dialog widget. Fulfills jQuery WidgetFactory _create hook.
         */
        _create: function() {
	          $.template(this.options.templateName, this.options.translateForm.template);
            this.translateDialog = jQuery("<div />", { id: this.options.dialog.id })
                .prependTo("body")
                .dialog($.extend(true, {
                    buttons : [
                        {
                            click: $.proxy(this.close, this)
                        },
                        {
                            click: $.proxy(this._formSubmit, this)
                        }
                    ]
                }, this.options.dialog));
        },

        /**
         * Opens the dialog.
         *
         * @param {Element} element the element to open the dialog near,
         *     must also contain data-translate attribute
         */
        open: function(element) {
            this.translateElement = element;

            this._fillDialogContent(element);
            this._positionDialog(element);
            this.translateDialog.dialog("open");
        },

        /**
         * Closes the dialog.
         */
        close: function() {
            this.translateDialog.dialog("close");
            this.options.onCancel();
        },

        /**
         * Fills the main dialog content. Replaces the dialog content with a
         * form with translation data.
         *
         * @param {Element} element the element to get the translation data from
         */
        _fillDialogContent: function(element) {
            this.translateDialog
                .html($.tmpl(this.options.templateName, {
                    data: $.extend({items: $(element).data(this.options.dataAttrName)},
                        this.options.translateForm.data),
                    escape: $.mage.escapeHTML
                }));

            var self = this;
            this.translateDialog.find("input.translate-dialog-translate-value-input").each(function(count, input) {
                /* discard changes if pressing esc */
                $(input).keydown(function(e) {
                    if (e.keyCode == 27) {
                        e.preventDefault();
                        $.proxy(self.close, self)();
                    }
                });
            });
        },

        /**
         * Positions the dialog relative to the element.
         *
         * @param {Element} element the element to position the dialog near
         */
        _positionDialog: function(element) {
            this.translateDialog.dialog("option", {
                position: { of : element, my: "left top", at: "left-3 top-9" },
                width: $(element).width(),
            });
        },

        /**
         * Submits the form.
         */
        _formSubmit: function() {
            var parameters = jQuery.param({area: this.options.area}) +
                "&" + jQuery("#" + this.options.translateForm.data.id).serialize();
            $.ajax({
                url: this.options.ajaxUrl,
                type: "POST",
                data: parameters,
                context: this.translateDialog,
                showLoader: true
            }).complete($.proxy(this._formSubmitComplete, this));
        },

        /**
         * Callback for when the AJAX call in _formSubmit is completed.
         */
        _formSubmitComplete: function() {
            var self = this;
            this.translateDialog.find("input").each(function(count, elem) {
                var id = elem.id;
                if (id.indexOf("custom_") === 0) {
                   var index = id.substring(7), 
                       value = elem.value,
                       translateData = $(self.translateElement).data("translate"),
                       innerHtmlStr = $(self.translateElement).html();

                   innerHtmlStr =  innerHtmlStr.replace(translateData[index]["shown"], value);
                   $(self.translateElement).html(innerHtmlStr);

                   translateData[index]["shown"] = value;
                   translateData[index]["translated"] = value;
                   $(self.translateElement).attr("data-translate", JSON.stringify(translateData));
                }
            });

            this.translateDialog.dialog("close");

            this.options.onSubmitComplete();
        }
    });

    $.widget("mage.translateInlineIconVde", {
        options: {
            img: null,
            area: "vde",
            ajaxUrl: null,
            offsetLeft: -16,
            template: '<img src="${img}" height="16" width="16">',
            onClick: function(element) { },
        },

        /**
         * Creates the icon widget to indicate text that can be translated.
         * Fulfills jQuery's WidgetFactory _create hook.
         */
        _create: function() {
            this._initTemplate();

            $(window).on("resize", $.proxy(this._positionTemplate, this));
        },

        /**
         * Shows the widget.
         */
        show: function() {
            this._positionTemplate();
            this.template.show();
        },

        /**
         * Hides the widget.
         */
        hide: function() {
            this.template.hide();
        },

        /**
         * Initializes the template for the widget. Sets the widget up to
         * respond to events.
         */
        _initTemplate: function() {
            this.template = $.tmpl(this.options.template, this.options).css({
	              position: "absolute",
                cursor: "pointer",
                "z-index": 2000,
            }).appendTo("body");
            this._positionTemplate();

            var self = this;
            this.template.on("click", function() {
                self.options.onClick(self.element);
            });
        },

        /**
         * Positions the template to the correct location.
         */
        _positionTemplate: function() {
            var offset = this.element.offset();
            this.template.css({
                top: offset.top,
                left: offset.left + this.element.outerWidth() + this.options.offsetLeft
            });
        },

        /**
         * Destroys the widget. Fulfills jQuery's WidgetFactory _destroy hook.
         */
        _destroy: function() {
            this.template.remove();
        }
    });

    /*
     * @TODO move the "escapeHTML" method into the file with global utility functions
     */
    $.extend(true, $, {
        mage: {
            escapeHTML: function(str) {
                return str ?
                    jQuery("<div/>").text(str).html().replace(/"/g, "&quot;"):
                    false;
            }
        }
    });
})(jQuery);

