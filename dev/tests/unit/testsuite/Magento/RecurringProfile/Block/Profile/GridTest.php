<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\RecurringProfile\Block\Profile;

/**
 * Test class for \Magento\RecurringProfile\Block\Profile\Grid
 */
class GridTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $_objectManagerHelper;

    protected function setUp()
    {
        $this->_objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
    }

    public function testPrepareLayout()
    {
        $customer = $this->getMockBuilder('Magento\Customer\Model\Customer')
            ->disableOriginalConstructor()
            ->setMethods(array('getId', '__wakeup'))
            ->getMock();
        $customer->expects($this->once())->method('getId')->will($this->returnValue(1));
        $registry = $this->getMockBuilder('Magento\Registry')
            ->disableOriginalConstructor()
            ->setMethods(array('registry'))
            ->getMock();
        $registry->expects($this->once())
            ->method('registry')
            ->with('current_customer')
            ->will($this->returnValue($customer));
        $store = $this->getMockBuilder('Magento\Core\Model\Store')
            ->disableOriginalConstructor()
            ->getMock();
        $collectionElement = $this->getMockBuilder('Magento\RecurringProfile\Model\Profile')
            ->disableOriginalConstructor()
            ->setMethods(array('setStore', 'renderData', 'getReferenceId', '__wakeup'))
            ->getMock();
        $collectionElement->expects($this->once())->method('setStore')
            ->with($store)
            ->will($this->returnValue($collectionElement));
        $collectionElement->expects($this->once())->method('getReferenceId')
            ->will($this->returnValue(1));
        $collectionElement->expects($this->atLeastOnce())->method('renderData')
            ->will($this->returnValue(2));
        $collection = $this->getMockBuilder('Magento\RecurringProfile\Model\Resource\Profile\Collection')
            ->disableOriginalConstructor()
            ->setMethods(array('addFieldToFilter', 'addFieldToSelect', 'setOrder'))
            ->getMock();
        $collection->expects($this->once())->method('addFieldToFilter')
            ->with('customer_id', 1)
            ->will($this->returnValue($collection));
        $collection->expects($this->once())->method('addFieldToSelect')
            ->will($this->returnValue($collection));
        $collection->expects($this->once())->method('setOrder')
            ->will($this->returnValue(array($collectionElement)));

        $profile = $this->getMockBuilder('Magento\RecurringProfile\Model\Profile')
            ->disableOriginalConstructor()
            ->setMethods(array('getCollection', 'getFieldLabel', '__wakeup'))
            ->getMock();
        $profile->expects($this->once())->method('getCollection')
            ->will($this->returnValue($collection));

        $storeManager = $this->getMockBuilder('Magento\Core\Model\StoreManager')
            ->disableOriginalConstructor()
            ->setMethods(array('getStore'))
            ->getMock();
        $storeManager->expects($this->once())->method('getStore')
            ->will($this->returnValue($store));

        $locale = $this->getMockBuilder('\Magento\LocaleInterface')
            ->disableOriginalConstructor()
            ->setMethods(array('formatDate'))
            ->getMockForAbstractClass();
        $locale->expects($this->once())->method('formatDate')
            ->will($this->returnValue('11-11-1999'));
        $block = $this->_objectManagerHelper->getObject(
            'Magento\RecurringProfile\Block\Profile\Grid',
            array(
                'recurringProfile' => $profile,
                'registry' => $registry,
                'storeManager' => $storeManager,
                'locale' => $locale
            )
        );
        $pagerBlock = $this->getMockBuilder('Magento\Theme\Block\Html\Pager')
            ->disableOriginalConstructor()
            ->setMethods(array('setCollection'))
            ->getMock();
        $pagerBlock->expects($this->once())->method('setCollection')
            ->with(array($collectionElement))
            ->will($this->returnValue($pagerBlock));

        $layout = $this->_getMockLayout();
        $layout->expects($this->once())->method('createBlock')
            ->will($this->returnValue($pagerBlock));

        $block->setLayout($layout);

        $this->assertNotEmpty($block->getGridColumns());
        $expectedResult = array(
            new \Magento\Object(array(
                'reference_id' => 1,
                'reference_id_link_url' => null,
                'state' => 2,
                'created_at' => '11-11-1999',
                'updated_at' => '',
                'method_code' => 2,
            ))
        );
        $this->assertEquals($expectedResult, $block->getGridElements());
    }

    /**
     * Get layout mock
     *
     * @return \Magento\View\LayoutInterface
     */
    protected function _getMockLayout()
    {
        $layout = $this->getMockBuilder('Magento\View\LayoutInterface')
            ->disableOriginalConstructor()
            ->setMethods(array('createBlock', 'getChildName', 'setChild'))
            ->getMockForAbstractClass();

        return $layout;
    }
}
