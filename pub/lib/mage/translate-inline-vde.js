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
                    "class" : "translate-dialog-cancel"
                },
                {
                    text: $.mage.__('Save'),
                    "class" : "translate-dialog-save"
                }]
            },
            positionDialog: function(element, dialog) { },
            templateName: "translateInlineDialogVdeTemplate",
            dataAttrName: "translate",
            onSubmitComplete: function() { },
            onCancel: function() { },
            area: "vde",
            ajaxUrl: null,
            translateMode: null,
            translateModes : ["text", "script", "alt"]
        },

        /**
         * Identifies if the form is already being submitted.
         *
         * @type {boolean}
         */
        isSubmitting : false,

        /**
         * Identifies if inline text is being editied.  Only one element can be edited at a time.
         *
         * @type {boolean}
         */
        isBeingEdited : false,

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
         * @param function callback invoked with the new translation data after
         *     form submssion. parameters are index and the translated string
         * @param function callback invoked to position the dialog
         */
        open: function(translateData, callback, positionDialog) {
            this.callback = callback;


            this._fillDialogContent(translateData);
            this.positionDialog = positionDialog;
            positionDialog(this.translateDialog);

            $(window).on('resize.translateInlineVdeDialog', $.proxy(this.reposition, this));

            this.translateDialog.dialog("open");
        },

        reposition: function() {
            this.positionDialog(this.translateDialog);
        },

        /**
         * Closes the dialog. This is if the dialog is closed manually. If the form
         * submit is executed, then the dialog will close via the _formSubmitComplete
         * function.
         */
        close: function() {
            this.translateDialog.dialog("close");
            this.options.onCancel();
            this.isBeingEdited = false;
            $(window).off('resize.translateInlineVdeDialog');
        },

        /**
         * Shows translate mode applicable css styles.
         */
        toggleStyle: function(mode) {
            mode = mode == null ? mode = this.options.translateMode : mode;
            $('body').addClass('trnslate-inline-' + mode + '-area');
            $.each(this.options.translateModes, function(){
                if (this != mode) {
                    $('body').removeClass('trnslate-inline-' + this + '-area');
                }
            });
        },

        /**
         * Fills the main dialog content. Replaces the dialog content with a
         * form with translation data.
         *
         * @param {Element} element the element to get the translation data from
         */
        _fillDialogContent: function(translateData) {
            this.translateDialog
                .html($.tmpl(this.options.templateName, {
                    data: $.extend({items: translateData},
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
                /* keep track of the fact that translate text has been changed */
                $(input).on('change', function(e) {
                    this.isBeingEdited = true;
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
         * Submits the form.
         */
        _formSubmit: function() {
            if (this.isSubmitting) {
                return;
            }
            this.isSubmitting = true;
            this.isBeingEdited = false;

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
            $(window).off('resize.translateInlineVdeDialog');

            this.options.onSubmitComplete();

            this.isSubmitting = false;
        }
    });

    /**
     * Widget for an icon to be displayed indicating that text can be translated.
     */
    $.widget("mage.translateInlineVde", {
        options: {
            iconTemplateId: "translate-inline-icon",
            img: null,
            imgHover: null,

            offsetLeft: -16,

            dataAttrName: "translate",
            translateMode: null,
            onClick: function(widget) { }
        },

        /**
         * Determines if the template is already appended to the element.
         *
         * @type {boolean}
         */
        isTemplateAttached : false,

        iconTemplate: null,
        iconWrapperTemplate: null,
        elementWrapperTemplate: null,

        /**
         * Creates the icon widget to indicate text that can be translated.
         * Fulfills jQuery's WidgetFactory _create hook.
         */
        _create: function() {
            this.element.addClass('translate-edit-icon-container');
            this._initTemplates();
            this.show();
        },

        /**
         * Shows the widget.
         */
        show: function() {
            this._attachIcon();

            this.iconTemplate.removeClass('hidden');

            if (this.element[0].getAttribute("translate-mode") != this.options.translateMode)
                this.iconTemplate.addClass('hidden');

            this.element.on("dblclick", $.proxy(this._invokeAction, this));
            this._disableElementClicks();
        },

        /**
         * Show edit icon for given translate mode.
         */
        toggleIcon: function(mode) {
            if (mode == this.element[0].getAttribute("translate-mode"))
                this.iconTemplate.removeClass('hidden');
            else
                this.iconTemplate.addClass('hidden');
        },

        _attachIcon: function() {
            if (!this.isTemplateAttached) {
                this.iconTemplate.appendTo(this.element);
                this.isTemplateAttached = true;
            }

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

            if ($(this.element).is('A')) {
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
            this.element.off("dblclick");
            this.iconTemplate.addClass('hidden');
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
         * Initializes all the templates for the widget.
         */
        _initTemplates: function() {
            this._initIconTemplate();
            this.iconTemplate.addClass('translate-edit-icon-text');
        },

        /**
         * Initializes the icon template for the widget. Sets the widget up to
         * respond to events.
         */
        _initIconTemplate: function() {
            var self = this;

            this.iconTemplate = $("#" + this.options.iconTemplateId).tmpl(this.options);

            this.iconTemplate.on("click", $.proxy(this._invokeAction, this));

            this.iconTemplate.on("mouseover", function() {
                if (self.options.imgHover) {
                    self.iconTemplate.prop('src', self.options.imgHover);
                }
            });

            this.iconTemplate.on("mouseout", function() {
                self.iconTemplate.prop('src', self.options.img);
            });

        },

        /**
         * Invokes the action (e.g. activate the inline dialog)
         */
        _invokeAction: function() {
            this._detachIcon();
            this.options.onClick(this);
        },

        /**
         * Destroys the widget. Fulfills jQuery's WidgetFactory _destroy hook.
         */
        _destroy: function() {
            this.iconTemplate.remove();
            this._detachIcon();
        },

        _detachIcon: function() {
            $(this.iconTemplate).detach();

            this.isTemplateAttached = false;
            $(this.element).css({
                visibility: "hidden"
            });
        }
    });

    $.widget("mage.translateInlineImageVde", $.mage.translateInlineVde, {
        _attachIcon: function() {
            if (!this.isTemplateAttached) {
                this.iconWrapperTemplate = this.iconTemplate.wrap('<div/>').parent();
                this.iconWrapperTemplate.addClass('translate-edit-icon-wrapper-image');

                this.elementWrapperTemplate = this.element.wrap('<div/>').parent();
                this.elementWrapperTemplate.addClass('translate-edit-icon-container');

                this.iconTemplate.appendTo(this.iconWrapperTemplate);
                this.iconWrapperTemplate.appendTo(this.elementWrapperTemplate);

                this.isTemplateAttached = true;
            }
        },

        _initTemplates: function() {
            this._initIconTemplate();
            this.iconTemplate.addClass('translate-edit-icon-image');
        },

        _detachIcon: function() {
            $(this.iconTemplate).detach();
            this.iconWrapperTemplate.remove();
            this.element.unwrap();
            this.elementWrapperTemplate.remove();

            this.isTemplateAttached = false;
        }
    });

    $.widget("mage.translateInlineScriptVde", $.mage.translateInlineVde, {
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

