/**
 * {license_notice}
 *
 * @category    mage
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint jquery:true*/
(function($) {

    var parentWidget = ($.blueimpFP || $.blueimp).fileupload;
    $.widget("vde.quickStyleLogoUploader", parentWidget, {
        options: {
            dataType: 'json',
            replaceFileInput:  true,
            sequentialUploads: true,
            url:               null,

            /**
             * Add file
             * @param e
             * @param data
             */
            add: function (e, data) {
                data.submit();
            },

            /**
             * Fail event
             * @param e
             * @param data
             */
            fail: function(e, data) {
                alert($.mage.__('File extension not known or unsupported type.'));
            }
        }
    });
})(jQuery);
