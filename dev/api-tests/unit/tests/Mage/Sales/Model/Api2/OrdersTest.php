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
 * @category    Mage
 * @package     Mage_Api2
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Test API2 class for orders
 *
 * @category   Mage
 * @package    Magento_Test
 * @author     Magento Api Team <api-team@magento.com>
 */
class Mage_Sales_Model_Api2_OrdersTest extends Mage_PHPUnit_TestCase
{
    /**
     * Test get available attributes
     */
    public function testGetAvailableAttributes()
    {
        $testTableName = 'sales_flat_order';
        $describeTable = array(
            'field1' => array('someinfo1'),
            'field2' => array('someinfo2'),
            'field3' => array('someinfo3')
        );
        $testAvailableAttributes = array('field1' => 'field1', 'field2' => 'field2', 'field3' => 'field3');

        $readConnectionMock = $this->getMockForAbstractClass('Zend_Db_Adapter_Abstract', array(), '', false);

        $readConnectionMock->expects($this->once())
            ->method('describeTable')
            ->with($testTableName)
            ->will($this->returnValue($describeTable));

        $resourceOrderMock = $this->getResourceModelMockBuilder('sales/order')
            ->getMock();

        $resourceOrderMock->expects($this->once())
            ->method('getMainTable')
            ->will($this->returnValue($testTableName));

        $resourceOrderMock->expects($this->once())
            ->method('getReadConnection')
            ->will($this->returnValue($readConnectionMock));

        /** @var $ordersModel Mage_Sales_Model_Api2_Orders */
        $ordersModel = Mage::getModel('sales/api2_orders');
        $ordersModel->setResourceType('orders');

        $this->assertSame($ordersModel->getAvailableAttributes('admin', 'read'), $testAvailableAttributes);
    }
}
