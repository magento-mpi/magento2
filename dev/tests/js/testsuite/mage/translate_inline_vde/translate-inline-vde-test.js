/**
 * {license_notice}
 *
 * @category    mage.js
 * @package     test
 * @copyright   {copyright}
 * @license     {license_link}
 */
TranslateInlineIconVdeTest = TestCase('TranslateInlineIconVdeTest');
TranslateInlineIconVdeTest.prototype.testInit = function() {
    /*:DOC += <div data-translate="true">text</div>
    <script id="translate-inline-icon" type="text/x-jQuery-tmpl">
        <img src="${img}" height="16" width="16">
    </script>
    */
    var translateInlineIconVde = jQuery('[data-translate]').translateInlineIconVde();
    assertEquals(true, translateInlineIconVde.is(':mage-translateInlineIconVde'));
    translateInlineIconVde.translateInlineIconVde('destroy');
};
TranslateInlineIconVdeTest.prototype.testCreate = function() {
    /*:DOC += <div data-translate="true">text</div>
    <script id="translate-inline-icon" type="text/x-jQuery-tmpl">
        <img src="${img}" height="16" width="16">
    </script>
    */
    assertEquals(0, jQuery('[data-translate] > img').size());
    var translateInlineIconVde = jQuery('[data-translate]').translateInlineIconVde();
    assertEquals(1, jQuery('[data-translate] > img').size());
    translateInlineIconVde.translateInlineIconVde('destroy');
};
TranslateInlineIconVdeTest.prototype.testHideAndShow = function() {
    /*:DOC += <div data-translate="true">text</div>
    <script id="translate-inline-icon" type="text/x-jQuery-tmpl">
        <img src="${img}" height="16" width="16">
    </script>
    */
    var translateInlineIconVde = jQuery('[data-translate]').translateInlineIconVde(),
        iconImg = jQuery('[data-translate] > img');
    assertFalse(iconImg.is('.hidden'));

    translateInlineIconVde.translateInlineIconVde('hide');
    assertTrue(iconImg.is('.hidden') );

    translateInlineIconVde.translateInlineIconVde('show');
    assertFalse(iconImg.is('.hidden') );
    assertFalse(jQuery('[data-translate]').is(':hidden') );

    translateInlineIconVde.translateInlineIconVde('destroy');
};
TranslateInlineIconVdeTest.prototype.testReplaceTextNormal = function() {
    /*:DOC += <div id="translateElem"
      data-translate="[{&quot;shown&quot; : &quot;Some value&quot;, &quot;translated&quot; : &quot;Translated value&quot;}]">text</div>
    <script id="translate-inline-icon" type="text/x-jQuery-tmpl">
        <img src="${img}" height="16" width="16">
    </script>
    */
    var translateInlineIconVde = jQuery('[data-translate]').translateInlineIconVde();
    var newValue = 'New value';

    jQuery('[data-translate]').translateInlineIconVde('replaceText', 0, newValue);

    var translateData = jQuery('#translateElem').data('translate');
    assertEquals(newValue, translateData[0]['shown']);
    assertEquals(newValue, translateData[0]['translated']);

    translateInlineIconVde.translateInlineIconVde('destroy');
};
TranslateInlineIconVdeTest.prototype.testReplaceTextNullOrBlank = function() {
    /*:DOC += <div id="translateElem"
      data-translate="[{&quot;shown&quot; : &quot;Some value&quot;, &quot;translated&quot; : &quot;Translated value&quot;}]">text</div>
    <script id="translate-inline-icon" type="text/x-jQuery-tmpl">
        <img src="${img}" height="16" width="16">
    </script>
    */
    var translateInlineIconVde = jQuery('[data-translate]').translateInlineIconVde();
    var newValue = null;

    jQuery('[data-translate]').translateInlineIconVde('replaceText', 0, newValue);

    var translateData = jQuery('#translateElem').data('translate');
    assertEquals('&nbsp;', translateData[0]['shown']);
    assertEquals('&nbsp;', translateData[0]['translated']);

    newValue = 'Some value';
    jQuery('[data-translate]').translateInlineIconVde('replaceText', 0, newValue);

    translateData = jQuery('#translateElem').data('translate');
    assertEquals(newValue, translateData[0]['shown']);
    assertEquals(newValue, translateData[0]['translated']);

    newValue = '';
    jQuery('[data-translate]').translateInlineIconVde('replaceText', 0, newValue);

    translateData = jQuery('#translateElem').data('translate');
    assertEquals('&nbsp;', translateData[0]['shown']);
    assertEquals('&nbsp;', translateData[0]['translated']);

    translateInlineIconVde.translateInlineIconVde('destroy');
};
TranslateInlineIconVdeTest.prototype.testClick = function() {
    /*:DOC += <div id="translateElem" data-translate="[]">text</div>
    <script id="translate-inline-icon" type="text/x-jQuery-tmpl">
        <img src="${img}" height="16" width="16">
    </script>
    */
    var counter = 0;
    var callback = function() {
      counter++;
    };
    var translateInlineIconVde = jQuery('[data-translate]').translateInlineIconVde({
      onClick: callback
      }),
      iconImg = jQuery('[data-translate] > img');

    iconImg.trigger('click');
    assertEquals(1, counter);
    assertEquals('hidden', jQuery('#translateElem').css('visibility'));

    translateInlineIconVde.translateInlineIconVde('destroy');
};
TranslateInlineIconVdeTest.prototype.testDblClick = function() {
    /*:DOC += <div id="translateElem" data-translate="[]">text</div>
    <script id="translate-inline-icon" type="text/x-jQuery-tmpl">
        <img src="${img}" height="16" width="16">
    </script>
    */
    var counter = 0;
    var callback = function() {
      counter++;
    };
    var translateInlineIconVde = jQuery('[data-translate]').translateInlineIconVde({
      onClick: callback
      }),
      iconImg = jQuery('[data-translate] > img');

    assertEquals(1, jQuery('#translateElem').find('img').size());

    translateInlineIconVde.trigger('dblclick');
    assertEquals(1, counter);

    assertEquals(0, jQuery('#translateElem').find('img').size());
    assertEquals('hidden', jQuery('#translateElem').css('visibility'));

    translateInlineIconVde.translateInlineIconVde('destroy');
};
