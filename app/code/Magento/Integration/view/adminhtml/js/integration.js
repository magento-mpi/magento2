/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint jquery:true*/
/*global FORM_KEY*/
/*global integration*/
(function($, window) {
    window.Integration = function (permissionsDialogUrl, tokensDialogUrl, deactivateDialogUrl, reauthorizeDialogUrl) {
        var url = {
            permissions: permissionsDialogUrl,
            tokens: tokensDialogUrl,
            deactivate: deactivateDialogUrl,
            reauthorize: reauthorizeDialogUrl
        };

        var _showPopup = function (dialog, title, okButton, url) {
            var that = this;

            $.ajax({
                url: url,
                cache: false,
                dataType: 'html',
                data: {form_key: window.FORM_KEY},
                method: 'GET',
                beforeSend: function () {
                    // Show the spinner
                    $('body').trigger('processStart');
                },
                success: function (html) {
                    var popup = $('#integration-popup-container');

                    popup.html(html);

                    var buttons = [{
                        text: $.mage.__('Cancel'),
                        click: function() {
                            $(this).dialog('destroy');
                        }
                    }];

                    // Add confirmation button to the list of dialog buttons
                    buttons.push(okButton);

                    popup.dialog({
                        title: title,
                        modal: true,
                        autoOpen: true,
                        minHeight: 450,
                        minWidth: 600,
                        dialogClass: 'integration-dialog',
                        position: {at: 'center'},
                        buttons: buttons
                    });
                },
                complete: function () {
                    // Hide the spinner
                    $('body').trigger('processStop');
                }
            })
        };

        return {
            popup: {
                show: function (ctx) {
                    var dialog = $(ctx).attr('data-row-dialog');

                    if (['permissions', 'deactivate', 'reauthorize', 'tokens'].indexOf(dialog) === -1) {
                        throw 'Invalid dialog type';
                    }

                    var integrationId = $(ctx).attr('data-row-id');

                    if (!integrationId) {
                        throw 'Unable to find integration ID';
                    }

                    // Replace placeholder in URL with actual ID
                    var ajaxUrl = url[dialog].replace(':id', integrationId);

                    try {
                        // Get integration name either from current element or from neighbor column
                        var integrationName = $(ctx).attr('data-row-name')
                            || $(ctx).parents('tr').find('.col-name').html().trim();
                    } catch (e) {
                        throw 'Unable to find integration name';
                    }

                    var okButton = {
                        permissions: {
                            text: $.mage.__('Allow'),
                            'class': 'primary',
                            // This data is going to be used in the next dialog
                            'data-row-id': integrationId,
                            'data-row-name': integrationName,
                            'data-row-dialog': 'tokens',
                            click: function () {
                                // Find the 'Allow' button and clone - it has all necessary data, but is going to be
                                // destroyed along with the current dialog
                                var ctx = $(this).parent().find('button.primary').clone(true);
                                $(this).dialog('destroy');
                                // Make popup out of data we saved from 'Allow' button
                                window.integration.popup.show(ctx);
                            }
                        },
                        tokens: {
                            text: $.mage.__('Activate'),
                            'class': 'primary',
                            click: function () {
                                alert('Not implemented');
                            }
                        }
                    };

                    _showPopup(dialog, integrationName, okButton[dialog], ajaxUrl);
                }
            }
        };
    };

    /**
     * Confirm dialog for delete integration action
     */
    $(function() {
        $('#integrationGrid_table').on('click', 'button#delete', function(e){
            $('#integration-delete-container').dialog({
                title: $.mage.__('Are you sure ?'),
                modal: true,
                autoOpen: true,
                resizable: false,
                minHeight: 200,
                minWidth: 300,
                dialogClass: "no-close",
                position: {at: 'top'},
                buttons: {
                    Cancel: function() {
                        $(this).dialog( "close" );
                    },
                    Delete: function() {
                        $(this).dialog( "close" );
                        window.location.href = $(e.target).data('url');
                    }
                }
            });
            e.stopPropagation();
        });
    });

})(jQuery, window);
