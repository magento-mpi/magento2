<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Paas
 * @package     integration_tests
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * CustomerBalance tests
 *
 * @category   Paas
 * @package    integration_tests
 * @author     Magento PaaS Team <paas-team@magento.com>
 */

/**
 * @magentoDataFixture CustomerBalance/_fixtures/CustomerBalance.php
 */
class CustomerBalance_CustomerBalanceTest extends Magento_Test_Webservice
{
    /**
     * Test successful customer balance info
     *
     * @return void
     */
    public function testCustomerBalanceBalance()
    {
        $customerBalanceFixture = simplexml_load_file(dirname(__FILE__) . '/_fixtures/CustomerBalance.xml');
        $data = self::simpleXmlToArray($customerBalanceFixture);

        $result = $this->call('storecredit.balance', $data['input']);

        $this->assertEquals($data['expected']['balance'], $result);
    }

    /**
     * Test customer balance info exception: balance not found
     *
     * @depends testCustomerBalanceBalance
     * @expectedException Exception
     * @return void
     */
    public function testCustomerBalanceBalanceExceptionBalanceNotFound()
    {
        //Get customer id without balance
        $customerBalanceFixture = simplexml_load_file(
            dirname(__FILE__) . '/_fixtures/CustomerBalanceExceptionBalanceNotFound.xml'
        );
        $data = self::simpleXmlToArray($customerBalanceFixture);

        $this->call('storecredit.balance', $data['input'], 'This balance value is not expected');
    }

    /**
     * Test successful customer balance history
     *
     * @depends testCustomerBalanceBalance
     * @return void
     */
    public function testCustomerBalanceHistory()
    {
        $customerBalanceHistoryFixture = simplexml_load_file(
            dirname(__FILE__) . '/_fixtures/CustomerBalanceHistory.xml'
        );
        $data = self::simpleXmlToArray($customerBalanceHistoryFixture);

        $result = $this->call('storecredit.history', $data['input']);

        $this->assertEquals(count($data['expected']['history_items']), count($result), 'History checksum fail');

        foreach($data['expected']['history_items'] as $index => $expectedHistoryItem) {
            foreach($expectedHistoryItem as $key => $value ) {
                $this->assertEquals($value, $result[$index][$key], 'History item value fail');
            }
        }
    }

    /**
     * Test customer balance history exception: history not found
     *
     * @depends testCustomerBalanceHistory
     * @expectedException Exception
     * @return void
     */
    public function testCustomerBalanceHistoryExceptionHistoryNotFound()
    {
        //Get customer id without balance history
        $customerBalanceHistoryFixture = simplexml_load_file(
            dirname(__FILE__) . '/_fixtures/CustomerBalanceExceptionHistoryNotFound.xml'
        );
        $data = self::simpleXmlToArray($customerBalanceHistoryFixture);

        $this->call('storecredit.history', $data['input']);
    }
}
