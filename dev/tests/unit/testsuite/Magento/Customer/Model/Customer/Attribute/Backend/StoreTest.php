<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Model\Customer\Attribute\Backend;

class StoreTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Store
     */
    protected $testable;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManager;

    public function setUp()
    {
        $logger = $this->getMockBuilder('Magento\Framework\Logger')->disableOriginalConstructor()->getMock();
        /** @var \Magento\Framework\Logger $logger */
        $storeManager = $this->storeManager = $this->getMockBuilder('Magento\Store\Model\StoreManagerInterface')
            ->getMock();
        /** @var \Magento\Store\Model\StoreManagerInterface $storeManager */
        $this->testable = new Store($logger, $storeManager);
    }

    public function testBeforeSaveWithId()
    {
        $object = $this->getMockBuilder('Magento\Framework\Object')
            ->disableOriginalConstructor()
            ->setMethods(array('getId'))
            ->getMock();

        $object->expects($this->once())->method('getId')->will($this->returnValue(1));
        /** @var \Magento\Framework\Object $object */

        $this->assertInstanceOf(
            'Magento\Customer\Model\Customer\Attribute\Backend\Store',
            $this->testable->beforeSave($object)
        );
    }

    public function testBeforeSave()
    {
        $storeId = 1;
        $storeName = 'store';
        $object = $this->getMockBuilder('Magento\Framework\Object')
            ->disableOriginalConstructor()
            ->setMethods(array('getId', 'hasStoreId', 'setStoreId', 'hasData', 'setData', 'getStoreId'))
            ->getMock();

        $store = $this->getMockBuilder('Magento\Framework\Object')->setMethods(array('getId', 'getName'))->getMock();
        $store->expects($this->once())->method('getId')->will($this->returnValue($storeId));
        $store->expects($this->once())->method('getName')->will($this->returnValue($storeName));

        $this->storeManager->expects($this->exactly(2))
            ->method('getStore')
            ->will($this->returnValue($store));

        $object->expects($this->once())->method('getId')->will($this->returnValue(false));
        $object->expects($this->once())->method('hasStoreId')->will($this->returnValue(false));
        $object->expects($this->once())->method('setStoreId')->with($storeId)->will($this->returnValue(false));
        $object->expects($this->once())->method('getStoreId')->will($this->returnValue($storeId));
        $object->expects($this->once())->method('hasData')->with('created_in')->will($this->returnValue(false));
        $object->expects($this->once())
            ->method('setData')
            ->with($this->logicalOr('created_in', $storeName))
            ->will($this->returnSelf());
        /** @var \Magento\Framework\Object $object */

        $this->assertInstanceOf(
            'Magento\Customer\Model\Customer\Attribute\Backend\Store',
            $this->testable->beforeSave($object)
        );
    }
}
