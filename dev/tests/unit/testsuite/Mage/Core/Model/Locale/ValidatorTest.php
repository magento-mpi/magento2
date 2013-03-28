<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_Locale_ValidatorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Locale_Validator
     */
    protected $_validatorModel;

    public function setUp()
    {
        $localeModelMock = $this->getMock('Mage_Core_Model_LocaleInterface', array());

        $localeModelMock->expects($this->any())
            ->method('getLocale')
            ->will($this->returnValue(new Zend_Locale()));

        $this->_validatorModel = new Mage_Core_Model_Locale_Validator($localeModelMock);
    }

    /**
     * @return array
     */
    public function testIsValidDataProvider()
    {
        return array(
            'case1' => array(
                'locale' => 'en_US',
                'valid'  => true
            ),
            'case2' => array(
                'locale' => 'pp_PP',
                'valid'  => false
            ),
        );
    }

    /**
     * @dataProvider testIsValidDataProvider
     * @param string $locale
     * @param boolean $valid
     * @covers Mage_Core_Model_Locale_Validator::isValid
     */
    public function testIsValid($locale, $valid)
    {
        $this->assertEquals($valid, $this->_validatorModel->isValid($locale));
    }
}
