<?php
/**
 * Customer balance tests.
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 * @magentoDataFixture Magento/CustomerBalance/_files/CustomerBalance.php
 */
class Magento_CustomerBalance_Model_ApiTest extends PHPUnit_Framework_TestCase
{
    /**
     * Customer fixture
     *
     * @var \Magento\Customer\Model\Customer
     */
    public static $customer = null;

    /**
     * Customer without balance fixture
     *
     * @var \Magento\Customer\Model\Customer
     */
    public static $customerNoBalance = null;

    /**
     * Test successful customer balance info
     */
    public function testCustomerBalanceBalance()
    {
        $customerBalanceData = simplexml_load_file(dirname(__FILE__) . '/../_files/fixture/CustomerBalance.xml');
        $data = Magento_TestFramework_Helper_Api::simpleXmlToArray($customerBalanceData);

        $data['input']['customerId'] = self::$customer->getId();

        $result = Magento_TestFramework_Helper_Api::call($this, 'enterpriseCustomerbalanceBalance', $data['input']);
        $this->assertEquals($data['expected']['balance'], $result, 'This balance value is not expected');
    }

    /**
     * Test customer balance info exception: balance not found
     *
     * @depends testCustomerBalanceBalance
     */
    public function testCustomerBalanceBalanceExceptionBalanceNotFound()
    {
        $params = array('customerId' => self::$customerNoBalance->getId(), 'websiteId' => 1);
        Magento_TestFramework_Helper_Api::callWithException($this, 'enterpriseCustomerbalanceBalance',
            $params, 'Balance with requested parameters is not found.'
        );

    }

    /**
     * Test successful customer balance history
     *
     * @depends testCustomerBalanceBalance
     */
    public function testCustomerBalanceHistory()
    {
        $balanceHistory = simplexml_load_file(
            dirname(__FILE__) . '/../_files/fixture/CustomerBalanceHistory.xml'
        );
        $data = Magento_TestFramework_Helper_Api::simpleXmlToArray($balanceHistory);

        $data['input']['customerId'] = self::$customer->getId();

        $result = Magento_TestFramework_Helper_Api::call(
            $this,
            'enterpriseCustomerbalanceHistory',
            array($data['input']['customerId'], $data['input']['websiteId'])
        );

        $this->assertEquals(count($data['expected']['history_items']), count($result), 'History checksum fail');

        foreach ($data['expected']['history_items'] as $index => $expectedHistoryItem) {
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
        $params = array('customerId' => self::$customerNoBalance->getId(), 'websiteId' => 1);
        Magento_TestFramework_Helper_Api::callWithException($this, 'enterpriseCustomerbalanceHistory',
            $params, 'History with requested parameters is not found.'
        );

    }
}
