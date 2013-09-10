<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Sales_Model_Order_StatusTest extends PHPUnit_Framework_TestCase
{
    /**
     * Retrieve prepared for test Magento_Sales_Model_Order_Status
     *
     * @param null|PHPUnit_Framework_MockObject_MockObject $resource
     * @param null|PHPUnit_Framework_MockObject_MockObject $eventDispatcher
     * @return Magento_Sales_Model_Order_Status
     */
    protected function _getPreparedModel($resource = null, $eventDispatcher = null)
    {
        if (!$resource) {
            $resource = $this->getMock('Magento_Sales_Model_Resource_Order_Status', array(), array(), '', false);
        }
        if (!$eventDispatcher) {
            $eventDispatcher = $this->getMock('Magento_Core_Model_Event_Manager', array(), array(), '', false);
        }
        $helper = new Magento_TestFramework_Helper_ObjectManager($this);
        $model = $helper->getObject('Magento_Sales_Model_Order_Status', array(
            'resource' => $resource,
            'eventDispatcher' => $eventDispatcher
        ));
        return $model;
    }

    public function testUnassignState()
    {
        $state = 'test_state';
        $status = 'test_status';

        $resource = $this->getMock('Magento_Sales_Model_Resource_Order_Status', array(), array(), '', false);
        $resource->expects($this->once())->method('beginTransaction');
        $resource->expects($this->once())->method('unassignState')
            ->with($this->equalTo($status), $this->equalTo($state));
        $resource->expects($this->once())->method('commit');

        $params = array('status' => $status, 'state' => $state);
        $eventDispatcher = $this->getMock('Magento_Core_Model_Event_Manager', array(), array(), '', false);
        $eventDispatcher->expects($this->once())->method('dispatch')
            ->with($this->equalTo('sales_order_status_unassign'), $this->equalTo($params));

        $model = $this->_getPreparedModel($resource, $eventDispatcher);
        $model->setStatus($status);
        $this->assertInstanceOf('Magento_Sales_Model_Order_Status', $model->unassignState($state));
    }
}
