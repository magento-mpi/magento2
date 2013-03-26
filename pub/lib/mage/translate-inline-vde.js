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
                selector: '[data-template="translate-inline-dialog-form-template"]',
                data: {
                    selector: '[data-form="translate-inline-dialog-form"]'
                }
            },
            dialog: {
                selector: "#translate-dialog",
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
            onSubmitComplete: function() {},
            onCancel: function() {},
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
	          $.template(this.options.templateName, $(this.options.translateForm.selector));
            this.translateDialog = $(this.options.dialog.selector)
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

            // Unbind previously bound events that may be present from previous loads of vde container.
            parent.jQuery('[data-frame="editor"]')
                .off('modeChange')
                .on('modeChange', $.proxy(this._checkTranslateEditing, this));
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

        /**
         * Repositions the dialog to be in the location as designed.
         */
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
            if (mode == null)
                mode = this.options.translateMode;
            else
                /* change translateMode */
                this.options.translateMode = mode;

            $('[data-container="body"]').addClass('trnslate-inline-' + mode + '-area');
            $.each(this.options.translateModes, function(){
                if (this != mode) {
                    $('[data-container="body"]').removeClass('trnslate-inline-' + this + '-area');
                }
            });
        },

        /**
         * Determine if user has modified inline translation text, but has not saved it.
         */
        _checkTranslateEditing: function(event, data) {
            if (this.isBeingEdited) {
                alert($.mage.__(data.alert_message));
                data.is_being_edited = true;
            }
            else {
                // Inline translation text is not being edited.  Continue on.
                parent.jQuery('[data-frame="editor"]').trigger(data.next_action, data);
            }
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
            this.translateDialog.find("textarea[data-translate-input-index]").each(function(count, input) {
                /* discard changes if pressing esc */
                $(input).keydown(function(e) {
                    if (e.keyCode == $.ui.keyCode.ESCAPE) {
                        e.preventDefault();
                        $.proxy(self.close, self)();
                    } else if (e.keyCode == $.ui.keyCode.ENTER) {
                        e.preventDefault();
                        $.proxy(self._formSubmit, self)();
                    } else {
                        /* keep track of the fact that translate text has been changed */
                        self.isBeingEdited = true;
                    }
                });
            });

            this.translateDialog.find(this.options.translateForm.data.selector).each(function(count, form) {
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

            $('[data-container="spinner"]').removeClass('hidden');

            var parameters = $.param({area: this.options.area}) +
                "&" + $(this.options.translateForm.data.selector).serialize();
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
            $('[data-container="spinner"]').addClass('hidden');

            var self = this;
            this.translateDialog.find("textarea").each(function(count, elem) {
                var id = elem.id;
                if (id.indexOf("custom_") === 0) {
                    var index = id.substring(7),
                        value = $(elem).val();

                    if (value === null) {
                        value = '';
                    }

                    self.callback(index, value);
                }
                self = null;
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
            iconTemplateSelector: '[data-template="translate-inline-icon"]',
            img: null,
            imgHover: null,

            offsetLeft: -16,

            dataAttrName: "translate",
            translateMode: null,
            onClick: function(widget) {}
        },

        /**
         * Elements to wrap instead of just inserting a child element. This is
         * to work around some different behavior in Firefox vs. WebKit.
         *
         * @type {Array}
         */
        elementsToWrap : [ 'button' ],

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
         * Determines if the element is suppose to be wrapped or just attached.
         *
         * @type {boolean}, null is unset, false/true is set
         */
        isElementWrapped : null,

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

            if (this.element.data('translateMode') != this.options.translateMode)
                this.iconTemplate.addClass('hidden');

            this.element.on("dblclick", $.proxy(this._invokeAction, this));
            this._disableElementClicks();
        },

        /**
         * Show edit icon for given translate mode.
         */
        toggleIcon: function(mode) {
            if (mode == this.element.data('translateMode'))
                this.iconTemplate.removeClass('hidden');
            else
                this.iconTemplate.addClass('hidden');

            this.options.translateMode = mode;
        },

        /**
         * Determines if the element should have an icon element wrapped around it or
         * if an icon element should be added as a child element.
         */
        _shouldWrap: function() {
            if (this.isElementWrapped !== null) {
                return this.isElementWrapped;
            }

            this.isElementWrapped = false;
            for (var c = 0; c < this.elementsToWrap.length; c++) {
                if (this.element.is(this.elementsToWrap[c])) {
                    this.isElementWrapped = true;
                    break;
                }
            }

            return this.isElementWrapped;
        },

        /**
         * Attaches an icon to the widget's element.
         */
       _attachIcon: function() {
            if (this._shouldWrap()) {
                if (!this.isTemplateAttached) {
                    this.iconWrapperTemplate = this.iconTemplate.wrap('<div/>').parent();
                    this.iconWrapperTemplate.addClass('translate-edit-icon-wrapper-text');

                    this.elementWrapperTemplate = this.element.wrap('<div/>').parent();
                    this.elementWrapperTemplate.addClass('translate-edit-icon-container');

                    this.iconTemplate.appendTo(this.iconWrapperTemplate);
                    this.iconWrapperTemplate.appendTo(this.elementWrapperTemplate);
                }
            } else {
                this.iconTemplate.appendTo(this.element);
                this.element.removeClass('invisible');
            }

            this.isTemplateAttached = true;
        },

        /**
         * Disables the element click from actually performing a click.
         */
        _disableElementClicks: function() {
            this.element.find('a').off('click');

            if (this.element.is('A')) {
                this.element.on('click', function(e) {
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
            var translateData = this.element.data(this.options.dataAttrName),
                innerHtmlStr = this.element.html();

            if (value === null || value === '') {
                value = "&nbsp;";
            }

            innerHtmlStr =  innerHtmlStr.replace(translateData[index]["shown"], value);

            this.element.html(innerHtmlStr);

            translateData[index]["shown"] = value;
            translateData[index]["translated"] = value;
            this.element.data(this.options.dataAttrName, translateData);
        },

        /**
         * Initializes all the templates for the widget.
         */
        _initTemplates: function() {
            this._initIconTemplate();
            this.iconTemplate.addClass('translate-edit-icon-text');
        },

        /**
         * Changes depending on hover action.
         */
        _hoverIcon: function() {
            if (this.options.imgHover) {
                this.iconTemplate.prop('src', this.options.imgHover);
            }
        },

        /**
         * Changes depending on hover action.
         */
        _unhoverIcon: function() {
            if (this.options.imgHover) {
                this.iconTemplate.prop('src', this.options.img);
            }
        },

        /**
         * Initializes the icon template for the widget. Sets the widget up to
         * respond to events.
         */
        _initIconTemplate: function() {
            var self = this;

            this.iconTemplate = $(this.options.iconTemplateSelector).tmpl(this.options);

            this.iconTemplate.on("click", $.proxy(this._invokeAction, this))
                             .on("mouseover", $.proxy(this._hoverIcon, this))
                             .on("mouseout", $.proxy(this._unhoverIcon, this));
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

        /**
         * Detaches an icon from the widget's element.
         */
        _detachIcon: function() {
            this._unhoverIcon();

            $(this.iconTemplate).detach();

            if (this._shouldWrap()) {
                this.iconWrapperTemplate.remove();
                this.element.unwrap();
                this.elementWrapperTemplate.remove();
            } else {
                this.element.addClass('invisible');
            }

            this.isTemplateAttached = false;
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

