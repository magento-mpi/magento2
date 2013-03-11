/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true*/
/*global FORM_KEY*/
/*global Validation*/
(function($) {
    'use strict';
    $.widget('mage.treeSuggestOneChoice', $.mage.treeSuggest, {
        /**
         * @override
         * @todo refactor parent widget to make this possible without method overriding
         */
        _selectItem: function() {
            $(this.elementWrapper).siblings('.mage-suggest-choice').trigger('removeOption');
            this._superApply(arguments);
            this._hideDropdown();
        }
    });

    $.widget('mage.newCategoryDialog', {
        _create: function () {
            var widget = this;
            $('#new_category_parent').after($('<input>', {
                id: 'new_category_parent-suggest',
                placeholder: 'start typing to search category'
            }));
            $('#new_category_parent-suggest').treeSuggest(this.options.suggestOptions);

            /* @todo rewrite using jQuery validation */
            Validation.add('validate-parent-category', 'Choose existing category.', function() {
                return $('#new_category_parent').val() || $('#new_category_parent-suggest').val() === '';
            });
            var newCategoryForm = new Validation(this.element.get(0));

            this.element.dialog({
                title: 'Create Category',
                autoOpen: false,
                minWidth: 560,
                dialogClass: 'mage-new-category-dialog form-inline',
                modal: true,
                multiselect: true,
                resizable: false,
                open: function() {
                    var enteredName = $('#category_ids + .category-selector-container .category-selector-input').val();
                    $('#new_category_name').val(enteredName);
                    if (enteredName === '') {
                        $('#new_category_name').focus();
                    }
                    $('#new_category_messages').html('');
                },
                close: function() {
                    $('#new_category_name, #new_category_parent').val('');
                    newCategoryForm.reset();
                    $('#category_ids + .category-selector-container .category-selector-input').focus();
                },
                buttons: [{
                    text: 'Create Category',
                    'class': 'action-create primary',
                    id: 'mage-new-category-dialog-save-button',
                    click: function() {
                        if (!newCategoryForm.validate()) {
                            return;
                        }

                        $.ajax({
                            type: 'POST',
                            url: widget.options.saveCategoryUrl,
                            data: {
                                general: {
                                    name: $('#new_category_name').val(),
                                    is_active: 1,
                                    include_in_menu: 0
                                },
                                parent: $('#new_category_parent').val(),
                                use_config: ['available_sort_by', 'default_sort_by'],
                                form_key: FORM_KEY,
                                return_session_messages_only: 1
                            },
                            dataType: 'json',
                            context: $('body')
                        })
                            .success(
                                function (data) {
                                    if (!data.error) {
                                        $('#category_ids-suggest').trigger('select', {
                                            id: data.category.entity_id,
                                            label: data.category.name
                                        });
                                        $('#new_category_name, #new_category_parent').val('');
                                        $('#category_ids + .category-selector-container .category-selector-input').val('');
                                        widget.element.dialog('close');
                                    } else {
                                        $('#new_category_messages').html(data.messages);
                                    }
                                }
                            );
                    }
                },
                {
                    text: 'Cancel',
                    'class': 'action-cancel',
                    id: 'mage-new-category-dialog-close-button',
                    click: function() {
                        $(this).dialog('close');
                    }
                }]
            });
        }
    });
})(jQuery);
