<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Model\Resource;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class GroupTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Customer\Model\Resource\Group */
    protected $groupResourceModel;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    /** @var \Magento\App\Resource|\PHPUnit_Framework_MockObject_MockObject */
    protected $resource;

    /** @var \Magento\Customer\Helper\Data|\PHPUnit_Framework_MockObject_MockObject */
    protected $customerHelper;

    /** @var \Magento\Customer\Model\Group|\PHPUnit_Framework_MockObject_MockObject */
    protected $groupModel;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $customersFactory;

    protected function setUp()
    {
        $this->resource = $this->getMock('Magento\App\Resource', [], [], '', false);
        $this->customerHelper = $this->getMock('Magento\Customer\Helper\Data', [], [], '', false);
        $this->customersFactory = $this->getMock(
            'Magento\Customer\Model\Resource\Customer\CollectionFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->groupModel = $this->getMock('Magento\Customer\Model\Group', [], [], '', false);

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->groupResourceModel = $this->objectManagerHelper->getObject(
            'Magento\Customer\Model\Resource\Group',
            [
                'resource' => $this->resource,
                'customerData' => $this->customerHelper,
                'customersFactory' => $this->customersFactory,
            ]
        );
    }

    public function testDelete()
    {
        $dbAdapter = $this->getMock('Magento\DB\Adapter\AdapterInterface');
        $this->resource->expects($this->once())->method('getConnection')->will($this->returnValue($dbAdapter));

        $customerCollection = $this->getMock('Magento\Customer\Model\Resource\Customer\Collection', [], [], '', false);
        $customerCollection->expects($this->once())->method('addAttributeToFilter')->will($this->returnSelf());
        $customerCollection->expects($this->once())->method('load')->will($this->returnSelf());
        $customer = $this->getMock(
            'Magento\Customer\Model\Customer',
            ['__wakeup', 'load', 'getId', 'getStoreId', 'setGroupId', 'save'],
            [],
            '',
            false
        );
        $customerId = 1;
        $customer->expects($this->once())->method('getId')->will($this->returnValue($customerId));
        $customer->expects($this->once())->method('load')->with($customerId)->will($this->returnSelf());
        $defaultCustomerGroup = 1;
        $this->customerHelper->expects($this->once())->method('getDefaultCustomerGroupId')
            ->will($this->returnValue($defaultCustomerGroup));
        $customer->expects($this->once())->method('setGroupId')->with($defaultCustomerGroup);
        $iterator = $this->onConsecutiveCalls(new \ArrayIterator(array($customer)));
        $customerCollection->expects($this->once())->method('getIterator')->will($iterator);
        $this->customersFactory->expects($this->once())->method('create')
            ->will($this->returnValue($customerCollection));

        $this->groupResourceModel->delete($this->groupModel);
    }
}
