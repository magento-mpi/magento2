<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_UnitPrice
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Saas_PrintedTemplate_Model_Variable_AbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test Saas_PrintedTemplate_Model_Variable_Abstract
     *
     * @dataProvider abstractDataProvider
     */
    public function testAbstract($field, $value, $expectedResult, $variable)
    {
        $config = $this->getMockBuilder('Saas_PrintedTemplate_Model_Config')
            ->setMethods(array('getConfigSectionArray'))
            ->getMock();

        $xml = simplexml_load_file(__DIR__ . '/../../_files/config.xml', 'Magento_Simplexml_Element');
        $array = $xml->asArray();

        $config->expects($this->any())
            ->method('getConfigSectionArray')
            ->will($this->returnValue($array['variables']['invoice']['fields']));

        $coreHelper = $this->getMockBuilder('Mage_Core_Helper_Data')
            ->setMethods(array('formatDate', 'formatCurrency'))
            ->disableOriginalConstructor()
            ->getMock();
        $coreHelper->expects($this->any())
            ->method('formatDate')
            ->will($this->returnValue('test_data'));
        $coreHelper->expects($this->any())
            ->method('formatCurrency')
            ->will($this->returnValue('test_data'));
        $saasHelper = $this->getMockBuilder('Saas_PrintedTemplate_Helper_Data')
            ->disableOriginalConstructor()
            ->setMethods(array('__'))
            ->getMock();
        $saasHelper->expects($this->any())
            ->method('__')
            ->will(
            $this->returnCallback(
                function ($msg)
                {
                    return $msg;
                }
            )
        );

        $value = new Magento_Object(array($field => $value));

        $abstractFake = $this->getMockBuilder('VariableAbstractFake')
            ->disableOriginalConstructor()
            ->setMethods(array('_getCoreHelper','_getHelper'))
            ->getMock();
        $abstractFake->__construct($value, $config);
        $abstractFake->expects($this->any())
            ->method('_getCoreHelper')
            ->will($this->returnValue($coreHelper));
        $abstractFake->expects($this->any())
            ->method('_getHelper')
            ->will($this->returnValue($saasHelper));

        $this->assertEquals($expectedResult, ($abstractFake->$variable()));
    }

    /**
     * Provider for testAbstract
     *
     * @return array
     */
    public function abstractDataProvider()
    {
        $compoundId = new Saas_PrintedTemplate_Model_Tax_CompoundId();
        $compoundId->addAnd('3');
        $compoundId->addAnd('5');
        $compoundId->addAfter('10');

        return array(
            array(
                'shipping_tax_rate', $compoundId, '3% and 5% then 10%', 'getShippingTaxRate'
            ),
            array(
                'customer_balance_amount', '100', 'test_data', 'getCustomerBalanceAmount'
            ),
            array(
                'discount_amount', '200', 'test_data', 'getDiscountAmount'
            ),
            array(
                'created_at', 'test_date', 'test_data', 'getCreatedAt'
            ),
            array(
                'total_qty', '10.5', '10.5', 'getTotalQty'
            ),
            array(
                'currencyabsraw_test', '300', '300', 'getCurrencyabsrawTest'
            ),
            array(
                'currencyraw_test', '400', '400', 'getCurrencyrawTest'
            ),
            array(
                'percent_test', '20', '20%', 'getPercentTest'
            ),
            array(
                'text_test', 'test', 'test', 'getTextTest'
            ),
            array(
                'yesno_test', '1', 'Yes', 'getYesnoTest'
            ),
            array(
                'yesnoraw_test', '1', true, 'getYesnorawTest'
            ),
            array(
                'currencyabsraw_test', null, '', 'getCurrencyabsrawTest'
            ),
            array(
                'currencyraw_test', null, '', 'getCurrencyrawTest'
            ),
            array(
                'percent_test', null, '', 'getPercentTest'
            ),
            array(
                'customer_balance_amount', null, '', 'getCustomerBalanceAmount'
            ),
            array(
                'created_at', null, '', 'getCreatedAt'
            ),
        );
    }
}

class VariableAbstractFake extends Saas_PrintedTemplate_Model_Variable_Abstract
{
    private $_config;

    /**
     * Constructor
     *
     * @see Saas_PrintedTemplate_Model_Template_Variable_Abstract::__construct()
     * @param Mage_Sales_Model_Order_Invoice $value Invoice
     */
    public function __construct(Magento_Object $value, $config)
    {
        parent::__construct($value);
        $this->_config = $config;
        $this->_setListsFromConfig('invoice');

    }

    /**
     * Returns config model singleton
     *
     * @return Saas_PrintedTemplate_Model_Config
     */
    protected function _getConfig()
    {
        return $this->_config;
    }

    protected function _getlocale()
    {
        return new Zend_Locale('en_US');
    }
}
