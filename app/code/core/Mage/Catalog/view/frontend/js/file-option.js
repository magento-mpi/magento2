/**
 * {license_notice}
 *
 * @category    mage file change/delete
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true*/
(function($) {
    $(document).ready(function() {

        function initializeFile(file) {
            var inputBox = $(file.inputBoxSelector);
            file.fileDeleteFlag = file.fileChangeFlag = false;
            file.inputField = inputBox.find('input[name=' + file.fileName + ']')[0];
            file.inputFieldAction = inputBox.find('input[name=' + file.fieldNameAction + ']')[0];
            file.fileNameSpan = inputBox.parent('dd').find('.' + file.fileNamed);
        }

        function toggleFileChange(file) {
            $(file.inputBoxSelector).toggle();
            file.fileChangeFlag = !file.fileChangeFlag;
            if (!file.fileDeleteFlag) {
                file.inputFieldAction.value = file.fileChangeFlag ? 'save_new' : 'save_old';
                file.inputField.disabled = !file.fileChangeFlag;
            }
        }

        function toggleFileDelete(file) {
            file.fileDeleteFlag = $(file.deleteFileSelector + ':checked').val();
            file.inputFieldAction.value =
                file.fileDeleteFlag ? '' : file.fileChangeFlag ? 'save_new' : 'save_old';
            file.inputField.disabled = file.fileDeleteFlag || !file.fileChangeFlag;
            file.fileNameSpan.css('text-decoration', file.fileDeleteFlag ? 'line-through' : 'none');
        }

        var fileInit = { file: [] };
        $.mage.event.trigger('mage.fileOption.initialize', fileInit);

        $.each(fileInit.file, function(index, file) {
            initializeFile(file);
            $(file.changeFileSelector).on('click', function() {
                toggleFileChange(file);
            });
            $(file.deleteFileSelector).on('click', function() {
                toggleFileDelete(file);
            });
        });

    });
})(jQuery);

