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

namespace Magento\Sales\Model\Order;

class StatusTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Retrieve prepared for test \Magento\Sales\Model\Order\Status
     *
     * @param null|PHPUnit_Framework_MockObject_MockObject $resource
     * @param null|PHPUnit_Framework_MockObject_MockObject $eventDispatcher
     * @return \Magento\Sales\Model\Order\Status
     */
    protected function _getPreparedModel($resource = null, $eventDispatcher = null)
    {
        if (!$resource) {
            $resource = $this->getMock('Magento\Sales\Model\Resource\Order\Status', array(), array(), '', false);
        }
        if (!$eventDispatcher) {
            $eventDispatcher = $this->getMock('Magento\Core\Model\Event\Manager', array(), array(), '', false);
        }
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $model = $helper->getObject('Magento\Sales\Model\Order\Status', array(
            'resource' => $resource,
            'eventDispatcher' => $eventDispatcher
        ));
        return $model;
    }

    public function testUnassignState()
    {
        $state = 'test_state';
        $status = 'test_status';

        $resource = $this->getMock('Magento\Sales\Model\Resource\Order\Status', array(), array(), '', false);
        $resource->expects($this->once())->method('beginTransaction');
        $resource->expects($this->once())->method('unassignState')
            ->with($this->equalTo($status), $this->equalTo($state));
        $resource->expects($this->once())->method('commit');

        $params = array('status' => $status, 'state' => $state);
        $eventDispatcher = $this->getMock('Magento\Core\Model\Event\Manager', array(), array(), '', false);
        $eventDispatcher->expects($this->once())->method('dispatch')
            ->with($this->equalTo('sales_order_status_unassign'), $this->equalTo($params));

        $model = $this->_getPreparedModel($resource, $eventDispatcher);
        $model->setStatus($status);
        $this->assertInstanceOf('Magento\Sales\Model\Order\Status', $model->unassignState($state));
    }
}
