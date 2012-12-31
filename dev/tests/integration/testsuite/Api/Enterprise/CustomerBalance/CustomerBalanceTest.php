<?php
/**
 * Customer balance tests.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoDataFixture Api/Enterprise/CustomerBalance/_fixture/CustomerBalance.php
 */
class Enterprise_CustomerBalance_CustomerBalanceTest extends PHPUnit_Framework_TestCase
{
    /** @var Magento_Test_Helper_Api */
    protected $_apiHelper;

    /**
     * Customer fixture
     *
     * @var Mage_Customer_Model_Customer
     */
    public static $customer = null;

    /**
     * Customer without balance fixture
     *
     * @var Mage_Customer_Model_Customer
     */
    public static $customerWithoutBalance = null;

    protected function setUp()
    {
        $this->_apiHelper = Magento_Test_Helper_Factory::getHelper('Api');
        parent::setUp();
    }

    public static function tearDownAfterClass()
    {
        Mage::register('isSecureArea', true);

        self::$customer->delete();
        self::$customerWithoutBalance->delete();

        self::$customer = null;
        self::$customerWithoutBalance = null;

        Mage::unregister('isSecureArea');
        parent::tearDownAfterClass();
    }

    /**
     * Test successful customer balance info
     *
     * @return void
     */
    public function testCustomerBalanceBalance()
    {
        $customerBalanceFixture = simplexml_load_file(dirname(__FILE__) . '/_fixture/CustomerBalance.xml');
        $data = Magento_Test_Helper_Api::simpleXmlToObject($customerBalanceFixture);

        $data->input->customerId = self::$customer->getId();

        $result = $this->_apiHelper->call('enterpriseCustomerbalanceBalance', (array)$data->input);
        $this->assertEquals($data->expected->balance, $result, 'This balance value is not expected');
    }

    /**
     * Test customer balance info exception: balance not found
     *
     * @depends testCustomerBalanceBalance
     */
    public function testCustomerBalanceBalanceExceptionBalanceNotFound()
    {
        $this->setExpectedException('SoapFault', 'Balance with requested parameters is not found.');
        $params = array(self::$customerWithoutBalance->getId(), $websiteId = 1);
        $this->_apiHelper->call('enterpriseCustomerbalanceBalance', $params);
    }

    /**
     * Test successful customer balance history
     *
     * @depends testCustomerBalanceBalance
     */
    public function testCustomerBalanceHistory()
    {
        $customerBalanceHistoryFixture = simplexml_load_file(
            dirname(__FILE__) . '/_fixture/CustomerBalanceHistory.xml'
        );
        $data = Magento_Test_Helper_Api::simpleXmlToObject($customerBalanceHistoryFixture);

        $data->input->customerId = self::$customer->getId();

        $result = $this->_apiHelper->call(
            'enterpriseCustomerbalanceHistory',
            array($data->input->customerId, $data->input->websiteId)
        );

        $this->assertEquals(count($data->expected->history_items), count($result), 'History checksum fail');

        foreach ($data->expected->history_items as $index => $expectedHistoryItem) {
            foreach ($expectedHistoryItem as $key => $value) {
                $this->assertEquals($value, $result[$index][$key], 'History item value fail');
            }
        }
    }

    /**
     * Test customer balance history exception: history not found
     *
     * @depends testCustomerBalanceHistory
     */
    public function testCustomerBalanceHistoryExceptionHistoryNotFound()
    {
        $this->setExpectedException('SoapFault', 'History with requested parameters is not found.');
        $params = array(self::$customerWithoutBalance->getId(), $websiteId = 1);
        $this->_apiHelper->call('enterpriseCustomerbalanceHistory', $params);
    }
}
