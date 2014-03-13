<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Review\Block\Customer;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class RecentTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Review\Block\Customer\Recent */
    protected $object;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    /** @var \Magento\View\Element\Template\Context|\PHPUnit_Framework_MockObject_MockObject */
    protected $context;

    /** @var \Magento\Review\Model\Resource\Review\Product\Collection|\PHPUnit_Framework_MockObject_MockObject */
    protected $collection;

    /** @var \Magento\Review\Model\Resource\Review\Product\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject */
    protected $collectionFactory;

    /** @var \Magento\Customer\Model\Session|\PHPUnit_Framework_MockObject_MockObject */
    protected $session;

    /** @var \Magento\Core\Model\StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $storeManager;

    protected function setUp()
    {
        $this->storeManager = $this->getMock('\Magento\Core\Model\StoreManagerInterface');
        $this->context = $this->getMock('Magento\View\Element\Template\Context', [], [], '', false);
        $this->context->expects($this->any())->method('getStoreManager')->will($this->returnValue($this->storeManager));
        $this->collection
            = $this->getMock('Magento\Review\Model\Resource\Review\Product\Collection', [], [], '', false);
        $this->collectionFactory = $this->getMock(
            'Magento\Review\Model\Resource\Review\Product\CollectionFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->collectionFactory->expects($this->once())->method('create')
            ->will($this->returnValue($this->collection));
        $this->session = $this->getMock('Magento\Customer\Model\Session', [], [], '', false);

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->object = $this->objectManagerHelper->getObject('Magento\Review\Block\Customer\Recent', [
            'context' => $this->context,
            'collectionFactory' => $this->collectionFactory,
            'customerSession' => $this->session
        ]);
    }

    public function testGetCollection()
    {
        $this->storeManager->expects($this->any())->method('getStore')
            ->will($this->returnValue(new \Magento\Object(['id' => 42])));
        $this->session->expects($this->any())->method('getCustomerId')->will($this->returnValue(4242));

        $this->collection->expects($this->any())->method('addStoreFilter')->with(42)
            ->will($this->returnValue($this->collection));
        $this->collection->expects($this->any())->method('addCustomerFilter')->with(4242)
            ->will($this->returnValue($this->collection));
        $this->collection->expects($this->any())->method('setDateOrder')->with()
            ->will($this->returnValue($this->collection));
        $this->collection->expects($this->any())->method('setPageSize')->with(5)
            ->will($this->returnValue($this->collection));
        $this->collection->expects($this->any())->method('load')->with()
            ->will($this->returnValue($this->collection));
        $this->collection->expects($this->any())->method('addReviewSummary')->with()
            ->will($this->returnValue($this->collection));

        $this->assertSame($this->collection, $this->object->getCollection());
    }
}
