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

    /** @var \Magento\Framework\View\Element\Template\Context|\PHPUnit_Framework_MockObject_MockObject */
    protected $context;

    /** @var \Magento\Review\Model\Resource\Review\Product\Collection|\PHPUnit_Framework_MockObject_MockObject */
    protected $collection;

    /** @var \Magento\Review\Model\Resource\Review\Product\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject */
    protected $collectionFactory;

    /** @var \Magento\Customer\Helper\Session\CurrentCustomer|\PHPUnit_Framework_MockObject_MockObject */
    protected $currentCustomer;

    /** @var \Magento\Framework\StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $storeManager;

    protected function setUp()
    {
        $this->storeManager = $this->getMock('\Magento\Framework\StoreManagerInterface');
        $this->context = $this->getMock('Magento\Framework\View\Element\Template\Context', array(), array(), '', false);
        $this->context->expects(
            $this->any()
        )->method(
            'getStoreManager'
        )->will(
            $this->returnValue($this->storeManager)
        );
        $this->collection = $this->getMock(
            'Magento\Review\Model\Resource\Review\Product\Collection',
            array(),
            array(),
            '',
            false
        );
        $this->collectionFactory = $this->getMock(
            'Magento\Review\Model\Resource\Review\Product\CollectionFactory',
            array('create'),
            array(),
            '',
            false
        );
        $this->collectionFactory->expects(
            $this->once()
        )->method(
            'create'
        )->will(
            $this->returnValue($this->collection)
        );
        $this->currentCustomer = $this->getMock(
            'Magento\Customer\Helper\Session\CurrentCustomer',
            array(),
            array(),
            '',
            false
        );

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->object = $this->objectManagerHelper->getObject(
            'Magento\Review\Block\Customer\Recent',
            array(
                'context' => $this->context,
                'collectionFactory' => $this->collectionFactory,
                'currentCustomer' => $this->currentCustomer
            )
        );
    }

    public function testGetCollection()
    {
        $this->storeManager->expects(
            $this->any()
        )->method(
            'getStore'
        )->will(
            $this->returnValue(new \Magento\Framework\Object(array('id' => 42)))
        );
        $this->currentCustomer->expects($this->any())->method('getCustomerId')->will($this->returnValue(4242));

        $this->collection->expects(
            $this->any()
        )->method(
            'addStoreFilter'
        )->with(
            42
        )->will(
            $this->returnValue($this->collection)
        );
        $this->collection->expects(
            $this->any()
        )->method(
            'addCustomerFilter'
        )->with(
            4242
        )->will(
            $this->returnValue($this->collection)
        );
        $this->collection->expects(
            $this->any()
        )->method(
            'setDateOrder'
        )->with()->will(
            $this->returnValue($this->collection)
        );
        $this->collection->expects(
            $this->any()
        )->method(
            'setPageSize'
        )->with(
            5
        )->will(
            $this->returnValue($this->collection)
        );
        $this->collection->expects($this->any())->method('load')->with()->will($this->returnValue($this->collection));
        $this->collection->expects(
            $this->any()
        )->method(
            'addReviewSummary'
        )->with()->will(
            $this->returnValue($this->collection)
        );

        $this->assertSame($this->collection, $this->object->getCollection());
    }
}
