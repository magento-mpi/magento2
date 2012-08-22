/**
 * {license_notice}
 *
 * @category    mage.translate
 * @package     test
 * @copyright   {copyright}
 * @license     {license_link}
 */
TranslateTest = TestCase('TranslateTest');

TranslateTest.prototype.testTranslateExist = function() {
    assertEquals(true, jQuery.mage.translate != undefined ? true : false);
};
TranslateTest.prototype.testTranslationParametersAsSingleObject = function() {
    var translation = {'Hello World!': 'Bonjour tout le monde!'};
    jQuery.mage.translate.add(translation);
    assertEquals(
        translation['Hello World!'],
        jQuery.mage.translate.translate('Hello World!'));
};
TranslateTest.prototype.testTranslationParametersAsTwoArguments = function() {
    jQuery.mage.translate.add('Hello World!', 'Bonjour tout le monde!');
    assertEquals(
        'Bonjour tout le monde!',
        jQuery.mage.translate.translate('Hello World!'));
};
TranslateTest.prototype.testTranslationAlias = function() {
    var translation = {'Hello World!': 'Bonjour tout le monde!'};
    jQuery.mage.translate.add(translation);
    assertEquals(translation['Hello World!'], jQuery.mage.__('Hello World!'));
};