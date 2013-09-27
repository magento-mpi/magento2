<?php
/**
 * Magento_Payment_Model_Config_Converter
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Payment_Model_Config_ConverterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Page_Model_Config_Converter
     */
    protected $_model;

    /** @var  array */
    protected $_targetArray;

    public function setUp()
    {
        $this->_model = new Magento_Payment_Model_Config_Converter();
    }

    public function testConvert()
    {
        $dom = new DOMDocument();
        $xmlFile = __DIR__ . '/_files/payment.xml';
        $dom->loadXML(file_get_contents($xmlFile));

        $expectedResult = array(
            'credit_cards' => array(
                'SO' => 'Solo',
                'SM' => 'Switch/Maestro',
            ),
            'groups' => array(
                'paypal' => 'PayPal',
            ),
        );
        $this->assertEquals($expectedResult, $this->_model->convert($dom), '', 0, 20);
    }
}
