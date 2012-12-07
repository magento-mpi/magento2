/**
 * {license_notice}
 *
 * @category    mage.validation
 * @package     test
 * @copyright   {copyright}
 * @license     {license_link}
 */
MageValidationTest = TestCase('MageValidationTest');

MageValidationTest.prototype.testValidateNoHtmlTags = function () {
    assertEquals(true, $.validator.methods['validate-no-html-tags'].call(this, ""));
    assertEquals(true, $.validator.methods['validate-no-html-tags'].call(this, null));
    assertEquals(true, $.validator.methods['validate-no-html-tags'].call(this, "abc"));
    assertEquals(false, $.validator.methods['validate-no-html-tags'].call(this, "<div>abc</div>"));
};

MageValidationTest.prototype.testAllowContainerClassName = function () {
    /*:DOC radio = <input type="radio" class="change-container-classname"/>*/
    assertEquals(true, $.validator.methods['allow-container-className'].call(this, this.radio));
    /*:DOC checkbox = <input type="checkbox" class="change-container-classname"/>*/
    assertEquals(true, $.validator.methods['allow-container-className'].call(this, this.checkbox));
    /*:DOC radio2 = <input type="radio"/>*/
    assertEquals(false, $.validator.methods['allow-container-className'].call(this, this.radio2));
    /*:DOC checkbox2 = <input type="checkbox"/>*/
    assertEquals(false, $.validator.methods['allow-container-className'].call(this, this.checkbox2));
};

MageValidationTest.prototype.testValidateSelect = function () {
    assertEquals(false, $.validator.methods['validate-select'].call(this, ""));
    assertEquals(false, $.validator.methods['validate-select'].call(this, "none"));
    assertEquals(false, $.validator.methods['validate-select'].call(this, null));
    assertEquals(false, $.validator.methods['validate-select'].call(this, undefined));
    assertEquals(true, $.validator.methods['validate-select'].call(this, "abc"));
};

MageValidationTest.prototype.testIsEmpty = function () {
    assertEquals(true, $.validator.methods['is-empty'].call(this, ""));
    assertEquals(true, $.validator.methods['is-empty'].call(this, null));
    assertEquals(true, $.validator.methods['is-empty'].call(this, undefined));
    assertEquals(true, $.validator.methods['is-empty'].call(this, "   "));
};

MageValidationTest.prototype.testValidateAlphanumWithSpaces = function () {
    assertEquals(true, $.validator.methods['validate-alphanum-with-spaces'].call(this, ""));
    assertEquals(true, $.validator.methods['validate-alphanum-with-spaces'].call(this, null));
    assertEquals(true, $.validator.methods['validate-alphanum-with-spaces'].call(this, undefined));
    assertEquals(true, $.validator.methods['validate-alphanum-with-spaces'].call(this, "   "));
    assertEquals(true, $.validator.methods['validate-alphanum-with-spaces'].call(this, "abc   "));
    assertEquals(true, $.validator.methods['validate-alphanum-with-spaces'].call(this, " 123  "));
    assertEquals(true, $.validator.methods['validate-alphanum-with-spaces'].call(this, "  abc123 "));
    assertEquals(false, $.validator.methods['validate-alphanum-with-spaces'].call(this, "  !@# "));
    assertEquals(false, $.validator.methods['validate-alphanum-with-spaces'].call(this, "  abc.123 "));
};

MageValidationTest.prototype.testValidateStreet = function () {
    assertEquals(true, $.validator.methods['validate-street'].call(this, ""));
    assertEquals(true, $.validator.methods['validate-street'].call(this, null));
    assertEquals(true, $.validator.methods['validate-street'].call(this, undefined));
    assertEquals(false, $.validator.methods['validate-street'].call(this, "   "));
    assertEquals(true, $.validator.methods['validate-street'].call(this, "1234 main st"));
    assertEquals(true, $.validator.methods['validate-street'].call(this, "7700 w parmer ln"));
    assertEquals(true, $.validator.methods['validate-street'].call(this, "7700 w parmer ln #125"));
    assertEquals(false, $.validator.methods['validate-street'].call(this, "!@# w parmer ln $125"));
};

MageValidationTest.prototype.testValidatePhoneStrict = function () {
    assertEquals(true, $.validator.methods['validate-phoneStrict'].call(this, ""));
    assertEquals(true, $.validator.methods['validate-phoneStrict'].call(this, null));
    assertEquals(true, $.validator.methods['validate-phoneStrict'].call(this, undefined));
    assertEquals(false, $.validator.methods['validate-phoneStrict'].call(this, "   "));
    assertEquals(false, $.validator.methods['validate-phoneStrict'].call(this, "5121231234"));
    assertEquals(false, $.validator.methods['validate-phoneStrict'].call(this, "512.123.1234"));
    assertEquals(true, $.validator.methods['validate-phoneStrict'].call(this, "512-123-1234"));
    assertEquals(true, $.validator.methods['validate-phoneStrict'].call(this, "(512)123-1234"));
    assertEquals(true, $.validator.methods['validate-phoneStrict'].call(this, "(512) 123-1234"));
};

MageValidationTest.prototype.testValidatePhoneLax = function () {
    assertEquals(true, $.validator.methods['validate-phoneLax'].call(this, ""));
    assertEquals(true, $.validator.methods['validate-phoneLax'].call(this, null));
    assertEquals(true, $.validator.methods['validate-phoneLax'].call(this, undefined));
    assertEquals(false, $.validator.methods['validate-phoneLax'].call(this, "   "));
    assertEquals(true, $.validator.methods['validate-phoneLax'].call(this, "5121231234"));
    assertEquals(true, $.validator.methods['validate-phoneLax'].call(this, "512.123.1234"));
    assertEquals(true, $.validator.methods['validate-phoneLax'].call(this, "512-123-1234"));
    assertEquals(true, $.validator.methods['validate-phoneLax'].call(this, "(512)123-1234"));
    assertEquals(true, $.validator.methods['validate-phoneLax'].call(this, "(512) 123-1234"));
    assertEquals(true, $.validator.methods['validate-phoneLax'].call(this, "(512)1231234"));
    assertEquals(false, $.validator.methods['validate-phoneLax'].call(this, "(512)_123_1234"));
};

MageValidationTest.prototype.testValidateFax = function () {
    assertEquals(true, $.validator.methods['validate-fax'].call(this, ""));
    assertEquals(true, $.validator.methods['validate-fax'].call(this, null));
    assertEquals(true, $.validator.methods['validate-fax'].call(this, undefined));
    assertEquals(false, $.validator.methods['validate-fax'].call(this, "   "));
    assertEquals(false, $.validator.methods['validate-fax'].call(this, "5121231234"));
    assertEquals(false, $.validator.methods['validate-fax'].call(this, "512.123.1234"));
    assertEquals(true, $.validator.methods['validate-fax'].call(this, "512-123-1234"));
    assertEquals(true, $.validator.methods['validate-fax'].call(this, "(512)123-1234"));
    assertEquals(true, $.validator.methods['validate-fax'].call(this, "(512) 123-1234"));
};

MageValidationTest.prototype.testValidateEmail = function () {
    assertEquals(true, $.validator.methods['validate-email'].call(this, ""));
    assertEquals(true, $.validator.methods['validate-email'].call(this, null));
    assertEquals(true, $.validator.methods['validate-email'].call(this, undefined));
    assertEquals(false, $.validator.methods['validate-email'].call(this, "   "));
    assertEquals(true, $.validator.methods['validate-email'].call(this, "123@123.com"));
    assertEquals(true, $.validator.methods['validate-email'].call(this, "abc@124.en"));
    assertEquals(true, $.validator.methods['validate-email'].call(this, "abc@abc.commmmm"));
    assertEquals(true, $.validator.methods['validate-email'].call(this, "abc.abc.abc@abc.commmmm"));
    assertEquals(true, $.validator.methods['validate-email'].call(this, "abc.abc-abc@abc.commmmm"));
    assertEquals(true, $.validator.methods['validate-email'].call(this, "abc.abc_abc@abc.commmmm"));
    assertEquals(false, $.validator.methods['validate-email'].call(this, "abc.abc_abc@abc"));
};

MageValidationTest.prototype.testValidateEmailSender = function () {
    assertEquals(true, $.validator.methods['validate-emailSender'].call(this, ""));
    assertEquals(true, $.validator.methods['validate-emailSender'].call(null));
    assertEquals(true, $.validator.methods['validate-emailSender'].call(undefined));
    assertEquals(true, $.validator.methods['validate-emailSender'].call("   "));
    assertEquals(true, $.validator.methods['validate-emailSender'].call("123@123.com"));
    assertEquals(true, $.validator.methods['validate-emailSender'].call("abc@124.en"));
    assertEquals(true, $.validator.methods['validate-emailSender'].call("abc@abc.commmmm"));
    assertEquals(true, $.validator.methods['validate-emailSender'].call("abc.abc.abc@abc.commmmm"));
    assertEquals(true, $.validator.methods['validate-emailSender'].call("abc.abc-abc@abc.commmmm"));
    assertEquals(true, $.validator.methods['validate-emailSender'].call("abc.abc_abc@abc.commmmm"));
};

MageValidationTest.prototype.testValidatePassword = function () {
    assertEquals(true, $.validator.methods['validate-password'].call(this, ""));
    assertEquals(false, $.validator.methods['validate-password'].call(this, null));
    assertEquals(false, $.validator.methods['validate-password'].call(this, undefined));
    assertEquals(true, $.validator.methods['validate-password'].call(this, "   "));
    assertEquals(true, $.validator.methods['validate-password'].call(this, "123@123.com"));
    assertEquals(false, $.validator.methods['validate-password'].call(this, "abc"));
    assertEquals(false, $.validator.methods['validate-password'].call(this, "abc       "));
    assertEquals(false, $.validator.methods['validate-password'].call(this, "     abc      "));
    assertEquals(false, $.validator.methods['validate-password'].call(this, "dddd"));
};

MageValidationTest.prototype.testValidateAdminPassword = function () {
    assertEquals(true, $.validator.methods['validate-admin-password'].call(this, ""));
    assertEquals(false, $.validator.methods['validate-admin-password'].call(this, null));
    assertEquals(false, $.validator.methods['validate-admin-password'].call(this, undefined));
    assertEquals(true, $.validator.methods['validate-admin-password'].call(this, "   "));
    assertEquals(true, $.validator.methods['validate-admin-password'].call(this, "123@123.com"));
    assertEquals(false, $.validator.methods['validate-admin-password'].call(this, "abc"));
    assertEquals(false, $.validator.methods['validate-admin-password'].call(this, "abc       "));
    assertEquals(false, $.validator.methods['validate-admin-password'].call(this, "     abc      "));
    assertEquals(false, $.validator.methods['validate-admin-password'].call(this, "dddd"));
};

MageValidationTest.prototype.testValidateUrl = function () {
    assertEquals(true, $.validator.methods['validate-url'].call(this, ""));
    assertEquals(true, $.validator.methods['validate-url'].call(this, null));
    assertEquals(true, $.validator.methods['validate-url'].call(this, undefined));
    assertEquals(false, $.validator.methods['validate-url'].call(this, "   "));
    assertEquals(true, $.validator.methods['validate-url'].call(this, "http://www.google.com"));
    assertEquals(true, $.validator.methods['validate-url'].call(this, "http://127.0.0.1:8080/index.php"));
    assertEquals(true, $.validator.methods['validate-url'].call(this, "http://app-spot.com/index.php"));
    assertEquals(true, $.validator.methods['validate-url'].call(this, "http://app-spot_space.com/index.php"));
};

MageValidationTest.prototype.testValidateCleanUrl = function () {
    assertEquals(true, $.validator.methods['validate-clean-url'].call(this, ""));
    assertEquals(true, $.validator.methods['validate-clean-url'].call(this, null));
    assertEquals(true, $.validator.methods['validate-clean-url'].call(this, undefined));
    assertEquals(false, $.validator.methods['validate-clean-url'].call(this, "   "));
    assertEquals(true, $.validator.methods['validate-clean-url'].call(this, "http://www.google.com"));
    assertEquals(false, $.validator.methods['validate-clean-url'].call(this, "http://127.0.0.1:8080/index.php"));
    assertEquals(false, $.validator.methods['validate-clean-url'].call(this, "http://127.0.0.1:8080"));
    assertEquals(false, $.validator.methods['validate-clean-url'].call(this, "http://127.0.0.1"));
};

MageValidationTest.prototype.testValidateXmlIdentifier = function () {
    assertEquals(true, $.validator.methods['validate-xml-identifier'].call(this, ""));
    assertEquals(true, $.validator.methods['validate-xml-identifier'].call(this, null));
    assertEquals(true, $.validator.methods['validate-xml-identifier'].call(this, undefined));
    assertEquals(false, $.validator.methods['validate-xml-identifier'].call(this, "   "));
    assertEquals(true, $.validator.methods['validate-xml-identifier'].call(this, "abc"));
    assertEquals(true, $.validator.methods['validate-xml-identifier'].call(this, "abc_123"));
    assertEquals(true, $.validator.methods['validate-xml-identifier'].call(this, "abc-123"));
    assertEquals(false, $.validator.methods['validate-xml-identifier'].call(this, "123-abc"));
};

MageValidationTest.prototype.testValidateSsn = function () {
    assertEquals(true, $.validator.methods['validate-ssn'].call(this, ""));
    assertEquals(true, $.validator.methods['validate-ssn'].call(this, null));
    assertEquals(true, $.validator.methods['validate-ssn'].call(this, undefined));
    assertEquals(false, $.validator.methods['validate-ssn'].call(this, "   "));
    assertEquals(false, $.validator.methods['validate-ssn'].call(this, "abc"));
    assertEquals(true, $.validator.methods['validate-ssn'].call(this, "123-13-1234"));
    assertEquals(true, $.validator.methods['validate-ssn'].call(this, "012-12-1234"));
    assertEquals(false, $.validator.methods['validate-ssn'].call(this, "23-12-1234"));
};

MageValidationTest.prototype.testValidateZip = function () {
    assertEquals(true, $.validator.methods['validate-zip'].call(this, ""));
    assertEquals(true, $.validator.methods['validate-zip'].call(this, null));
    assertEquals(true, $.validator.methods['validate-zip'].call(this, undefined));
    assertEquals(false, $.validator.methods['validate-zip'].call(this, "   "));
    assertEquals(true, $.validator.methods['validate-zip'].call(this, "12345-1234"));
    assertEquals(true, $.validator.methods['validate-zip'].call(this, "02345"));
    assertEquals(false, $.validator.methods['validate-zip'].call(this, "1234"));
    assertEquals(false, $.validator.methods['validate-zip'].call(this, "1234-1234"));
};

MageValidationTest.prototype.testValidateDateAu = function () {
    assertEquals(true, $.validator.methods['validate-date-au'].call(this, ""));
    assertEquals(true, $.validator.methods['validate-date-au'].call(this, null));
    assertEquals(true, $.validator.methods['validate-date-au'].call(this, undefined));
    assertEquals(false, $.validator.methods['validate-date-au'].call(this, "   "));
    assertEquals(true, $.validator.methods['validate-date-au'].call(this, "01/01/2012"));
    assertEquals(true, $.validator.methods['validate-date-au'].call(this, "30/01/2012"));
    assertEquals(false, $.validator.methods['validate-date-au'].call(this, "01/30/2012"));
    assertEquals(false, $.validator.methods['validate-date-au'].call(this, "1/1/2012"));
};

MageValidationTest.prototype.testValidateCurrencyDollar = function () {
    assertEquals(true, $.validator.methods['validate-currency-dollar'].call(this, ""));
    assertEquals(true, $.validator.methods['validate-currency-dollar'].call(this, null));
    assertEquals(true, $.validator.methods['validate-currency-dollar'].call(this, undefined));
    assertEquals(false, $.validator.methods['validate-currency-dollar'].call(this, "   "));
    assertEquals(true, $.validator.methods['validate-currency-dollar'].call(this, "$123"));
    assertEquals(true, $.validator.methods['validate-currency-dollar'].call(this, "$1,123.00"));
    assertEquals(true, $.validator.methods['validate-currency-dollar'].call(this, "$1234"));
    assertEquals(false, $.validator.methods['validate-currency-dollar'].call(this, "$1234.1234"));
};

MageValidationTest.prototype.testValidateNotNegativeNumber = function () {
    assertEquals(true, $.validator.methods['validate-not-negative-number'].call(this, ""));
    assertEquals(true, $.validator.methods['validate-not-negative-number'].call(this, null));
    assertEquals(true, $.validator.methods['validate-not-negative-number'].call(this, undefined));
    assertEquals(false, $.validator.methods['validate-not-negative-number'].call(this, "   "));
    assertEquals(true, $.validator.methods['validate-not-negative-number'].call(this, "0"));
    assertEquals(true, $.validator.methods['validate-not-negative-number'].call(this, "1"));
    assertEquals(true, $.validator.methods['validate-not-negative-number'].call(this, "1234"));
    assertEquals(true, $.validator.methods['validate-not-negative-number'].call(this, "1,234.1234"));
    assertEquals(false, $.validator.methods['validate-not-negative-number'].call(this, "-1"));
    assertEquals(false, $.validator.methods['validate-not-negative-number'].call(this, "-1e"));
    assertEquals(false, $.validator.methods['validate-not-negative-number'].call(this, "-1,234.1234"));
};

MageValidationTest.prototype.testValidateGreaterThanZero = function () {
    assertEquals(true, $.validator.methods['validate-greater-than-zero'].call(this, ""));
    assertEquals(true, $.validator.methods['validate-greater-than-zero'].call(this, null));
    assertEquals(true, $.validator.methods['validate-greater-than-zero'].call(this, undefined));
    assertEquals(false, $.validator.methods['validate-greater-than-zero'].call(this, "   "));
    assertEquals(false, $.validator.methods['validate-greater-than-zero'].call(this, "0"));
    assertEquals(true, $.validator.methods['validate-greater-than-zero'].call(this, "1"));
    assertEquals(true, $.validator.methods['validate-greater-than-zero'].call(this, "1234"));
    assertEquals(true, $.validator.methods['validate-greater-than-zero'].call(this, "1,234.1234"));
    assertEquals(false, $.validator.methods['validate-greater-than-zero'].call(this, "-1"));
    assertEquals(false, $.validator.methods['validate-greater-than-zero'].call(this, "-1e"));
    assertEquals(false, $.validator.methods['validate-greater-than-zero'].call(this, "-1,234.1234"));
};

MageValidationTest.prototype.testValidateCssLength = function () {
    assertEquals(true, $.validator.methods['validate-css-length'].call(this, ""));
    assertEquals(true, $.validator.methods['validate-css-length'].call(this, null));
    assertEquals(true, $.validator.methods['validate-css-length'].call(this, undefined));
    assertEquals(false, $.validator.methods['validate-css-length'].call(this, "   "));
    assertEquals(false, $.validator.methods['validate-css-length'].call(this, "0"));
    assertEquals(true, $.validator.methods['validate-css-length'].call(this, "1"));
    assertEquals(true, $.validator.methods['validate-css-length'].call(this, "1234"));
    assertEquals(true, $.validator.methods['validate-css-length'].call(this, "1,234.1234"));
    assertEquals(false, $.validator.methods['validate-css-length'].call(this, "-1"));
    assertEquals(false, $.validator.methods['validate-css-length'].call(this, "-1e"));
    assertEquals(false, $.validator.methods['validate-css-length'].call(this, "-1,234.1234"));
};

MageValidationTest.prototype.testValidateData = function () {
    assertEquals(true, $.validator.methods['validate-data'].call(this, ""));
    assertEquals(true, $.validator.methods['validate-data'].call(this, null));
    assertEquals(true, $.validator.methods['validate-data'].call(this, undefined));
    assertEquals(false, $.validator.methods['validate-data'].call(this, "   "));
    assertEquals(false, $.validator.methods['validate-data'].call(this, "123abc"));
    assertEquals(true, $.validator.methods['validate-data'].call(this, "abc"));
    assertEquals(false, $.validator.methods['validate-data'].call(this, " abc"));
    assertEquals(true, $.validator.methods['validate-data'].call(this, "abc123"));
    assertEquals(false, $.validator.methods['validate-data'].call(this, "abc-123"));
};

MageValidationTest.prototype.testValidateOneRequiredByName = function () {
    /*:DOC += <input type="radio" name="radio" id="radio"/> */
    /*:DOC += <input type="radio" name="radio"/> */
    assertFalse(false, $.validator.methods['validate-one-required-by-name'].call(this,
        null, document.getElementById('radio')));
    /*:DOC += <input type="radio" name="radio" checked/> */
    assertTrue(false, $.validator.methods['validate-one-required-by-name'].call(this,
        null, document.getElementById('radio')));

    /*:DOC += <input type="checkbox" name="checkbox" id="checkbox"/> */
    /*:DOC += <input type="checkbox" name="checkbox"/> */
    assertFalse(false, $.validator.methods['validate-one-required-by-name'].call(this,
        null, document.getElementById('checkbox')));
    /*:DOC += <input type="checkbox" name="checkbox" checked/> */
    assertTrue(false, $.validator.methods['validate-one-required-by-name'].call(this,
        null, document.getElementById('checkbox')));
};