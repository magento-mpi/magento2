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

    var clearParentCategory = function () {
        $('#new_category_parent').find('option').each(function(){
            $('#new_category_parent-suggest').treeSuggest('removeOption', null, this);
        });
    };

    $.widget('mage.newCategoryDialog', {
        _create: function () {
            var widget = this;
            $('#new_category_parent').before($('<input>', {
                id: 'new_category_parent-suggest',
                placeholder: 'start typing to search category'
            }));
            $('#new_category_parent-suggest').mage('treeSuggest', this.options.suggestOptions)
                .on('suggestbeforeselect', function (event) {
                    clearParentCategory();
                    $(event.target).treeSuggest('close');
                    $('#new_category_name').focus();
                });

            /* @todo rewrite using jQuery validation */
            /* Validation doesn't work for this invisible <select> after recent changes for some reason */
            $('#new_category_parent').css({border: 0, height: 0,padding: 0, width: 0}).show();
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
                    var enteredName = $('#category_ids-suggest').val();
                    $('#new_category_name').val(enteredName);
                    if (enteredName === '') {
                        $('#new_category_name').focus();
                    }
                    $('#new_category_messages').html('');
                },
                close: function() {
                    $('#new_category_name, #new_category_parent-suggest').val('');
                    clearParentCategory();
                    newCategoryForm.reset();
                    $('#category_ids-suggest').focus();
                },
                buttons: [{
                    text: 'Create Category',
                    'class': 'action-create primary',
                    'data-action': 'save',
                    click: function(event) {
                        if (!newCategoryForm.validate()) {
                            return;
                        }

                        var thisButton = $(event.target).closest('[data-action=save]');
                        thisButton.prop('disabled', true);
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
                                        $('#new_category_name, #new_category_parent-suggest').val('');
                                        $('#category_ids-suggest').val('');
                                        widget.element.dialog('close');
                                    } else {
                                        $('#new_category_messages').html(data.messages);
                                    }
                                }
                            )
                            .complete(
                                function () {
                                    thisButton.prop('disabled', false);
                                }
                            );
                    }
                },
                {
                    text: 'Cancel',
                    'class': 'action-cancel',
                    'data-action': 'cancel',
                    click: function() {
                        $(this).dialog('close');
                    }
                }]
            });
        }
    });
})(jQuery);
