<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Locale;

class ValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\Locale\Validator
     */
    protected $_validatorModel;

    protected function setUp()
    {
        $localeConfigMock = $this->getMock('Magento\Framework\Locale\ConfigInterface');
        $localeConfigMock->expects(
            $this->any()
        )->method(
            'getAllowedLocales'
        )->will(
            $this->returnValue(array('en_US', 'de_DE', 'es_ES'))
        );

        $this->_validatorModel = new \Magento\Framework\Locale\Validator($localeConfigMock);
    }

    /**
     * @return array
     */
    public function testIsValidDataProvider()
    {
        return array(
            'case1' => array('locale' => 'en_US', 'valid' => true),
            'case2' => array('locale' => 'pp_PP', 'valid' => false)
        );
    }

    /**
     * @dataProvider testIsValidDataProvider
     * @param string $locale
     * @param boolean $valid
     * @covers \Magento\Framework\Locale\Validator::isValid
     */
    public function testIsValid($locale, $valid)
    {
        $this->assertEquals($valid, $this->_validatorModel->isValid($locale));
    }
}
