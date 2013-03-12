/**
 * {license_notice}
 *
 * @category    Mage
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true */
(function($, window) {
    /**
     * Widget for a dialog to edit translations.
     */
    $.widget("mage.translateInlineDialogVde", {
        options: {
            translateForm: {
                id: "translate-inline-dialog-form-template",
                data: {
                    id: "translate-inline-dialog-form"
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
                    text: $.mage.__('Cancel'),
                    "class" : "translate-dialog-cancel",
                },
                {
                    text: $.mage.__('Save'),
                    "class" : "translate-dialog-save",
                }]
            },
            templateName: "translateInlineDialogVdeTemplate",
            dataAttrName: "translate",
            onSubmitComplete: function() { },
            onCancel: function() { }
        },

        /**
         * Identifies if the form is already being submitted.
         *
         * @type {boolean}
         */
        isSubmitting : false,

        /**
         * Creates the translation dialog widget. Fulfills jQuery WidgetFactory _create hook.
         */
        _create: function() {
	          $.template(this.options.templateName, $("#" + this.options.translateForm.id));
            this.translateDialog = $("#" + this.options.dialog.id)
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
        open: function(element, callback) {
            this.callback = callback;

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
            this.translateDialog.find("input[data-translate-input-index]").each(function(count, input) {
                /* discard changes if pressing esc */
                $(input).keydown(function(e) {
                    if (e.keyCode == 27) {
                        e.preventDefault();
                        $.proxy(self.close, self)();
                    }
                });
            });

            this.translateDialog.find("#" + this.options.translateForm.data.id).each(function(count, form) {
                $(form).on('submit', function(e) {
                    e.preventDefault();
                    $.proxy(self._formSubmit, self)();
                    return true;
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
                width: $(element).width()
            });
        },

        /**
         * Submits the form.
         */
        _formSubmit: function() {
            if (this.isSubmitting) {
                return;
            }
            this.isSubmitting = true;

            var parameters = $.param({area: this.options.area}) +
                "&" + $("#" + this.options.translateForm.data.id).serialize();
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
                        value = elem.value;

                    if (value === null || value === '') {
                        value = '';
                    }

                    self.callback(index, value);
                }
            });

            this.translateDialog.dialog("close");

            this.options.onSubmitComplete();

            this.isSubmitting = false;
        }
    });

    /**
     * Widget for an icon to be displayed indicating that text can be translated.
     */
    $.widget("mage.translateInlineIconVde", {
        options: {
            img: null,
            imgHover: null,
            area: "vde",
            ajaxUrl: null,
            offsetLeft: -16,
            templateId: "translate-inline-icon",
            dataAttrName: "translate",
            onClick: function(element) { },
        },

        /**
         * Determines if the template is already appended to the element.
         *
         * @type {boolean}
         */
        isTemplateAttached : false,

        /**
         * Creates the icon widget to indicate text that can be translated.
         * Fulfills jQuery's WidgetFactory _create hook.
         */
        _create: function() {
            this._initTemplate();
            this.show();
        },

        /**
         * Shows the widget.
         */
        show: function() {
            var self = this;

            if (!this.isTemplateAttached) {
                this.template.appendTo(this.element);
                this.isTemplateAttached = true;
            }

            this.template.removeClass('hidden');

            this.element.on("dblclick", $.proxy(this._onClick, this));
            this._disableElementClicks();

            $(this.element).css({
                visibility: "visible"
            });
        },

        /**
         * Disables the element click from actually performing a click.
         */
        _disableElementClicks: function() {
            this.element.find('a').each(function(count, link) {
                link.on('click', function(e) {
                    e.preventDefault();
                    return false;
                });
            });

            if ($(this.element).prop('tagName') === 'A') {
                this.element.on('click', function(e) {
                    e.preventDefault();
                    return false;
                });
            }
        },

        /**
         * Hides the widget.
         */
        hide: function() {
            this.template.addClass('hidden');
            this.element.off("dblclick");
        },

        /**
         * Replaces the translated text inside the widget with the new value.
         */
        replaceText: function(index, value) {
            var translateData = $(this.element).data(this.options.dataAttrName),
                innerHtmlStr = $(this.element).html();

            if (value === null || value === '') {
                value = "&nbsp;";
            }

            innerHtmlStr =  innerHtmlStr.replace(translateData[index]["shown"], value);

            $(this.element).html(innerHtmlStr);

            translateData[index]["shown"] = value;
            translateData[index]["translated"] = value;
            $(this.element).data(this.options.dataAttrName, translateData);
        },

        /**
         * Initializes the template for the widget. Sets the widget up to
         * respond to events.
         */
        _initTemplate: function() {
            var self = this;

            this.template = $("#" + this.options.templateId).tmpl(this.options)
                .addClass("translate-edit-icon");
            this.element.addClass('translate-edit-icon-container');

            this.template.on("click", $.proxy(this._onClick, this));

            this.template.on("mouseover", function() {
                if (self.options.imgHover) {
                    self.template.prop('src', self.options.imgHover);
                }
            });

            this.template.on("mouseout", function() {
                self.template.prop('src', self.options.img);
            });
        },

        /**
         * Activates the inline vde dialog.
         */
        _onClick: function() {
            $(this.template).detach();
            this.isTemplateAttached = false;
            $(this.element).css({
                visibility: "hidden"
            });
            this.options.onClick(this);
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
                return str ? str.replace(/"/g, '&quot;') : "";
            }
        }
    });
})(jQuery, window);

