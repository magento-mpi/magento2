<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Model_Locale_ValidatorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_Locale_Validator
     */
    protected $_validatorModel;

    public function setUp()
    {
        $localeConfigMock = $this->getMock('Magento_Core_Model_Locale_Config', array());
        $localeConfigMock->expects($this->any())
            ->method('getAllowedLocales')
            ->will($this->returnValue(array('en_US', 'de_DE', 'es_ES')));

        $this->_validatorModel = new Magento_Core_Model_Locale_Validator($localeConfigMock);
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
     * @covers Magento_Core_Model_Locale_Validator::isValid
     */
    public function testIsValid($locale, $valid)
    {
        $this->assertEquals($valid, $this->_validatorModel->isValid($locale));
    }
}
