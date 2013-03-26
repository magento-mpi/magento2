<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Backend
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Backend_Model_Locale_ValidatorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Backend_Model_Locale_Validator
     */
    protected $_validatorModel;

    /**
     * Setup before tests
     */
    public function setUp()
    {
        $localeModelMock = $this->getMock('Mage_Core_Model_Locale', array());
        $localeModelMock->expects($this->any())
            ->method('getTranslatedOptionLocales')
            ->will($this->returnValue(array('en_US' => 'English', 'de_DE' => 'Deutsch')));

        $this->_validatorModel = new Mage_Backend_Model_Locale_Validator($localeModelMock);
    }

    /**
     * Test isValid method with correct locale
     *
     * @covers Mage_Backend_Model_Locale_Validator::isValid
     */
    public function testIsValidCorrectLocale()
    {
        $this->assertTrue($this->_validatorModel->isValid('en_US'));
    }

    /**
     * Test isValid method with incorrect locale
     *
     * @covers Mage_Backend_Model_Locale_Validator::isValid
     */
    public function testIsValidIncorrectLocale()
    {
        $this->assertFalse($this->_validatorModel->isValid('pp_PP'));
    }
}
