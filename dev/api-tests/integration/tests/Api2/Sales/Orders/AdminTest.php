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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Magento
 * @package     Magento_Test
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Test for orders collection API2
 *
 * @category    Magento
 * @package     Magento_Test
 * @author      Magento Api Team <api-team@magento.com>
 */
class Api2_Sales_Orders_AdminTest extends Magento_Test_Webservice_Rest_Admin
{
    /**
     * Delete fixtures
     */
    protected function tearDown()
    {
        $fixtureOrders = $this->getFixture('orders_list');
        if ($fixtureOrders && count($fixtureOrders)) {
            foreach ($fixtureOrders as $fixtureOrder) {
                $this->callModelDelete($fixtureOrder, true);
            }
        }

        parent::tearDown();
    }

    /**
     * Delete acl fixture after test case
     */
    public static function tearDownAfterClass()
    {
        Magento_TestCase::deleteFixture('role', true);
        Magento_TestCase::deleteFixture('rule', true);
        Magento_TestCase::deleteFixture('attribute', true);
        Magento_Test_Webservice::setFixture('admin_acl_is_prepared', false);

        parent::tearDownAfterClass();
    }


    /**
     * Test get orders for admin
     *
     * @magentoDataFixture Api2/Sales/_fixtures/admin_acl.php
     * @magentoDataFixture Api2/Sales/_fixtures/orders_list.php
     */
    public function testGetOrders()
    {
        $restResponse = $this->callGet('orders', array('order' => 'entity_id', 'dir' => Zend_Db_Select::SQL_DESC));
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());

        $orders = $restResponse->getBody();
        $this->assertNotEmpty($orders);
        $ordersIds = array();
        foreach ($orders as $order) {
            $ordersIds[] = $order['entity_id'];
        }

        $fixtureOrders = $this->getFixture('orders_list');
        foreach ($fixtureOrders as $fixtureOrder) {
            $this->assertContains($fixtureOrder->getId(), $ordersIds,
                'Order should be in response');
        }
    }
}
