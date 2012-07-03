/**
 * {license_notice}
 *
 * @category    mage.localization
 * @package     test
 * @copyright   {copyright}
 * @license     {license_link}
 */
LocalizeTest = TestCase('LocalizeTest');

LocalizeTest.prototype.testInit = function () {
  mage.locale('fr');
  assertEquals('fr', mage.localize.name());
};

LocalizeTest.prototype.testDate = function () {
  mage.locale();
  assertEquals('6/7/2012', mage.localize.date('06/07/2012 3:30 PM', 'd'));
  assertEquals('Thursday, June 07, 2012', mage.localize.date('06/07/2012 3:30 PM', 'D'));
  assertEquals('Thursday, June 07, 2012 3:30 PM', mage.localize.date('6/7/2012 3:30 PM', 'f'));
  assertEquals('Thursday, June 07, 2012 3:30:00 PM', mage.localize.date('6/7/2012 3:30 PM', 'F'));
  assertEquals('June 07', mage.localize.date('6/7/2012 3:30 PM', 'M'));
  assertEquals('2012-06-07T15:30:00', mage.localize.date('6/7/2012 3:30 PM', 'S'));
  assertEquals('3:30 PM', mage.localize.date('6/7/2012 3:30 PM', 't'));
  assertEquals('3:30:00 PM', mage.localize.date('6/7/2012 3:30 PM', 'T'));
  assertEquals('2012 June', mage.localize.date('6/7/2012 3:30 PM', 'Y'));
};

LocalizeTest.prototype.testNumber = function () {
  mage.locale();
  assertEquals('0.00', mage.localize.number('0', 'n'));
};

LocalizeTest.prototype.testCurrency = function () {
  mage.locale();
  assertEquals('$0.00', mage.localize.currency('0'));
};

LocalizeTest.prototype.testTranslate = function () {
  mage.locale();
  assertEquals('Dieses Feld ist ein Pflichtfeld.', mage.localize.translate('This field is required.'));
  assertEquals('Invalid Entry.', mage.localize.translate('Invalid Entry.'));
};

LocalizeTest.prototype.testTranslateEvent = function () {
  mage.locale('de');
  assertEquals("Dieses Feld ist ein Pflichtfeld.", mage.localize.translate('This field is required.'));
   mage.event.observe('mage.translate', function (e, o) {
    assertEquals('Dieses Feld ist ein Pflichtfeld.', o.translatedMessage);
    o.translatedMessage = "test";
  });
  assertEquals("test", mage.localize.translate('This field is required.'));
};