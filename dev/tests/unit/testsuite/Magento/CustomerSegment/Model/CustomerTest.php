<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CustomerSegment\Model;

class CustomerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\CustomerSegment\Model\Customer
     */
    private $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $_registry;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $_customerSession;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $_resource;

    /**
     * @var array
     */
    private $_fixtureSegmentIds = [123, 456];

    protected function setUp()
    {
        $this->_registry = $this->getMock('Magento\Framework\Registry', ['registry'], [], '', false);

        $website = new \Magento\Framework\Object(['id' => 5]);
        $storeManager = $this->getMock('Magento\Framework\StoreManagerInterface');
        $storeManager->expects($this->any())->method('getWebsite')->will($this->returnValue($website));

        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $constructArguments = $objectManager->getConstructArguments(
            'Magento\Customer\Model\Session',
            ['storage' => new \Magento\Framework\Session\Storage()]
        );
        $this->_customerSession = $this->getMock(
            'Magento\Customer\Model\Session',
            ['getCustomer'],
            $constructArguments
        );

        $this->_resource = $this->getMock(
            'Magento\CustomerSegment\Model\Resource\Customer',
            ['getCustomerWebsiteSegments', 'getIdFieldName'],
            [
                $this->getMock('Magento\Framework\App\Resource', [], [], '', false),
                $this->getMock('Magento\Framework\Stdlib\DateTime', null, [], '', true)
            ]
        );

        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->_model = $helper->getObject(
            '\Magento\CustomerSegment\Model\Customer',
            [
                'registry' => $this->_registry,
                'resource' => $this->_resource,
                'resourceCustomer' => $this->getMock(
                    'Magento\Customer\Model\Resource\Customer',
                    [],
                    [],
                    '',
                    false
                ),
                'storeManager' => $storeManager,
                'customerSession' => $this->_customerSession,
                'collectionFactory' => $this->getMock(
                    'Magento\CustomerSegment\Model\Resource\Segment\CollectionFactory',
                    [],
                    [],
                    '',
                    false
                )
            ]
        );
    }

    protected function tearDown()
    {
        $this->_model = null;
        $this->_registry = null;
        $this->_customerSession = null;
        $this->_resource = null;
    }

    public function testGetCurrentCustomerSegmentIdsCustomerInRegistry()
    {
        $customer = new \Magento\Framework\Object(['id' => 100500]);
        $this->_registry->expects(
            $this->once()
        )->method(
            'registry'
        )->with(
            'segment_customer'
        )->will(
            $this->returnValue($customer)
        );
        $this->_resource->expects(
            $this->once()
        )->method(
            'getCustomerWebsiteSegments'
        )->with(
            100500,
            5
        )->will(
            $this->returnValue($this->_fixtureSegmentIds)
        );
        $this->assertEquals($this->_fixtureSegmentIds, $this->_model->getCurrentCustomerSegmentIds());
    }

    public function testGetCurrentCustomerSegmentIdsCustomerInRegistryNoId()
    {
        $customer = new \Magento\Framework\Object();
        $this->_registry->expects(
            $this->once()
        )->method(
            'registry'
        )->with(
            'segment_customer'
        )->will(
            $this->returnValue($customer)
        );
        $this->_customerSession->setData('customer_segment_ids', [5 => $this->_fixtureSegmentIds]);
        $this->assertEquals($this->_fixtureSegmentIds, $this->_model->getCurrentCustomerSegmentIds());
    }

    public function testGetCurrentCustomerSegmentIdsCustomerInSession()
    {
        $customer = new \Magento\Framework\Object(['id' => 100500]);
        $this->_customerSession->expects($this->once())->method('getCustomer')->will($this->returnValue($customer));
        $this->_resource->expects(
            $this->once()
        )->method(
            'getCustomerWebsiteSegments'
        )->with(
            100500,
            5
        )->will(
            $this->returnValue($this->_fixtureSegmentIds)
        );
        $this->assertEquals($this->_fixtureSegmentIds, $this->_model->getCurrentCustomerSegmentIds());
    }

    public function testGetCurrentCustomerSegmentIdsCustomerInSessionNoId()
    {
        $customer = new \Magento\Framework\Object();
        $this->_customerSession->expects($this->once())->method('getCustomer')->will($this->returnValue($customer));
        $this->_customerSession->setData('customer_segment_ids', [5 => $this->_fixtureSegmentIds]);
        $this->assertEquals($this->_fixtureSegmentIds, $this->_model->getCurrentCustomerSegmentIds());
    }
}
