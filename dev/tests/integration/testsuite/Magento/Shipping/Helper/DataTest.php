<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Shipping
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Shipping_Helper_DataTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Shipping_Helper_Data
     */
    protected $_helper = null;

    public function setUp()
    {
        $this->_helper = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Shipping_Helper_Data');
    }

    /**
     * @param string $modelName
     * @param string $getIdMethod
     * @param int $entityId
     * @param string $code
     * @param string $expected
     * @dataProvider getTrackingPopupUrlBySalesModelDataProvider
     */
    public function testGetTrackingPopupUrlBySalesModel($modelName, $getIdMethod, $entityId, $code, $expected)
    {
        $model = $this->getMock($modelName, array($getIdMethod, 'getProtectCode'), array(), '', false);
        $model->expects($this->any())
            ->method($getIdMethod)
            ->will($this->returnValue($entityId));
        $model->expects($this->any())
            ->method('getProtectCode')
            ->will($this->returnValue($code));

        $actual = $this->_helper->getTrackingPopupUrlBySalesModel($model);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @return array
     */
    public function getTrackingPopupUrlBySalesModelDataProvider()
    {
        return array(
            array(
                'Magento_Sales_Model_Order',
                'getId',
                42,
                'abc',
                'http://localhost/index.php/shipping/tracking/popup/hash/b3JkZXJfaWQ6NDI6YWJj/'
            ),
            array(
                'Magento_Sales_Model_Order_Shipment',
                'getId',
                42,
                'abc',
                'http://localhost/index.php/shipping/tracking/popup/hash/c2hpcF9pZDo0MjphYmM,/'
            ),
            array(
                'Magento_Sales_Model_Order_Shipment_Track',
                'getEntityId',
                42,
                'abc',
                'http://localhost/index.php/shipping/tracking/popup/hash/dHJhY2tfaWQ6NDI6YWJj/'
            )
        );
    }
}
