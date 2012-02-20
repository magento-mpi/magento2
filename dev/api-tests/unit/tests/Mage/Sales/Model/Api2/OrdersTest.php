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
 * API2 Sales Connection Mock Class
 *
 * @category   Mage
 * @package    Mage_Api2
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Api2_ConnectionMock extends Varien_Db_Adapter_Pdo_Mysql
{
    /**
     * Test available attributes
     *
     * @var string
     */
    protected $_testTableName;

    /**
     * Test tableName
     *
     * @var array
     */
    protected $_testAvailableAttributes = array();

    /**
     * Construct
     *
     * @param string $tableName
     * @param array $testAvailableAttributes
     */
    public function __construct($testTableName, $testAvailableAttributes)
    {
        $this->_testTableName = $testTableName;
        foreach ($testAvailableAttributes as $testAvailableAttribute) {
            $this->_testAvailableAttributes[$testAvailableAttribute] = array();
        }
    }

    /**
     * Returns the column descriptions for a table.
     *
     * @param string $tableName
     * @param string $schemaName OPTIONAL
     * @return array
     */
    public function describeTable($tableName, $schemaName = null)
    {
        return $this->_testAvailableAttributes;
    }
}


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
        $testAvailableAttributes = array('field1', 'fileld2', 'field3');

        $resourceOrderMock = $this->getResourceModelMockBuilder('sales/order')
            ->getMock();

        $resourceOrderMock->expects($this->once())
            ->method('getMainTable')
            ->will($this->returnValue($testTableName));

        $resourceOrderMock->expects($this->once())
            ->method('getReadConnection')
            ->will($this->returnValue(
                new Mage_Sales_Model_Api2_ConnectionMock($testTableName, $testAvailableAttributes)
            ));

        /* @var $apiOrdersModel Mage_Sales_Model_Api2_Orders */
        $ordersModel = Mage::getModel('sales/api2_orders');
        $ordersModel->setResourceType('orders');
        $this->assertEquals(
            $ordersModel->getAvailableAttributes(),
            array_combine($testAvailableAttributes, $testAvailableAttributes)
        );
    }
}
