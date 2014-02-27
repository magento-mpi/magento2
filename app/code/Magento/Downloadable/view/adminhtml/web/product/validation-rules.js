/**
 * Downloadable client side validation rules
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*global newFileContainer:true, alertAlreadyDisplayed:true, alert:true, linkType:true*/
(function ($) {
    $.validator.addMethod('validate-downloadable-file', function(v,element) {
        var elmParent = $(element).parent(),
            linkType = elmParent.find('input[value="file"]');
        if (linkType.is(':checked') && (v === '' || v === '[]')) {
            newFileContainer = elmParent.find('.new-file');
            if (!alertAlreadyDisplayed && (newFileContainer.empty() || newFileContainer.is(':visible'))) {
                alertAlreadyDisplayed = true;
                alert($.mage.__('There are files that were selected but not uploaded yet. ' +
                    'Please upload or remove them first'));
            }
            return false;
        }
        return true;
    }, 'Please upload a file.');
    $.validator.addMethod('validate-downloadable-url', function(v,element) {
        linkType = $(element).parent().find('input[value="file"]');
        if (linkType.is(':checked') && v === '') {
            return false;
        }
        return true;
    }, 'Please specify Url.');
    $.validator.addMethod('validate-downloadable-link-sample-file', function(v,element) {
        var fileSaveElm = $(element).closest('div').next('input[type="hidden"]');
        if ($(element).is(':checked') && (fileSaveElm.val() === '' || fileSaveElm.val() === '[]')) {
            return false;
        }
        return true;
    }, 'Please specify File.');
    $.validator.addMethod('validate-downloadable-link-sample-url', function(v,element) {
        if ($(element).is(':checked') && $(element).closest('p').find('input[type="text"]').val() === '') {
            return false;
        }
        return true;
    }, 'Please specify Sample URL.');
})(jQuery);
