/**
 * {license_notice}
 *
 * @category    mage.js
 * @package     test
 * @copyright   {copyright}
 * @license     {license_link}
 */
TranslateInlineDialogVdeTest = TestCase('TranslateInlineDialogVdeTest');

TranslateInlineDialogVdeTest.prototype.testInit = function() {
    var translateInlineDialogVde = jQuery('body').translateInlineDialogVde();
    assertEquals(true, translateInlineDialogVde.is(':mage-translateInlineDialogVde'));
    translateInlineDialogVde.translateInlineDialogVde('destroy');
};
TranslateInlineDialogVdeTest.prototype.testWithTemplate = function() {
    /*:DOC +=
        <script id="translate-inline-dialog-form-template" type="text/x-jQuery-tmpl">
            <form id="${data.id}">
                {{each(i, item) data.items}}
                <input id="perstore_${i}" name="translate[${i}][perstore]" type="hidden" value="0"/>
                <input name="translate[${i}][original]" type="hidden" value="${item.scope}::${escape(item.original)}"/>
                <input id="custom_${i}" name="translate[${i}][custom]" value="${escape(item.translated)}" data-translate-input-index="${i}"/>
                {{/each}}
            </form>
        </script>
    */
    var translateInlineDialogVde = jQuery('body').translateInlineDialogVde();
    assertEquals(true, translateInlineDialogVde.is(':mage-translateInlineDialogVde'));
    translateInlineDialogVde.translateInlineDialogVde('destroy');
};
TranslateInlineDialogVdeTest.prototype.testOpenAndClose = function() {
    /*:DOC += 
        <div id="randomElement"></div>
        <div id="translate-dialog"></div>
        <script id="translate-inline-dialog-form-template" type="text/x-jQuery-tmpl">
            <form id="${data.id}">
                {{each(i, item) data.items}}
                <input id="perstore_${i}" name="translate[${i}][perstore]" type="hidden" value="0"/>
                <input name="translate[${i}][original]" type="hidden" value="${item.scope}::${escape(item.original)}"/>
                <input id="custom_${i}" name="translate[${i}][custom]" value="${escape(item.translated)}" data-translate-input-index="${i}"/>
                {{/each}}
            </form>
        </script>
    */
    var translateInlineDialogVde = jQuery('body').translateInlineDialogVde();

    jQuery('body').translateInlineDialogVde('open', jQuery('#randomElement'), function() { });
    assertTrue(jQuery('#translate-dialog').dialog('isOpen'));

    jQuery('body').translateInlineDialogVde('close');
    assertFalse(jQuery('#translate-dialog').dialog('isOpen'));

    translateInlineDialogVde.translateInlineDialogVde('destroy');
};
