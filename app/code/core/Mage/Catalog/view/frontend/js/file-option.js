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
            file.fileDeleteFlag = false;
            file.fileChangeFlag = false;
            file.inputField = inputBox.find('input[name=' + file.fileName + ']')[0];
            file.inputFieldAction = inputBox.find('input[name=' + file.fieldNameAction + ']')[0];
            file.fileNameSpan = inputBox.parent('dd').find('.' + file.fileNamed);
        }

        function toggleFileChange(file) {
            $(file.inputBoxSelector).toggle();
            file.fileChangeFlag = ! file.fileChangeFlag;
            if (! file.fileDeleteFlag) {
                if (file.fileChangeFlag) {
                    file.inputFieldAction.value = 'save_new';
                    file.inputField.disabled = false;
                } else {
                    file.inputFieldAction.value = 'save_old';
                    file.inputField.disabled = true;
                }
            }
        }

        function toggleFileDelete(file) {
            file.fileDeleteFlag = $(file.deleteFileSelector + ':checked').val();
            if (file.fileDeleteFlag) {
                file.inputFieldAction.value = '';
                file.inputField.disabled = true;
                file.fileNameSpan.css('text-decoration', 'line-through');
            } else {
                file.inputFieldAction.value = file.fileChangeFlag ? 'save_new' : 'save_old';
                file.inputField.disabled = (file.inputFieldAction.value === 'save_old');
                file.fileNameSpan.css('text-decoration', 'none');
            }
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

