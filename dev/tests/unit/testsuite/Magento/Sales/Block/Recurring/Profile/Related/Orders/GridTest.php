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
namespace Magento\Sales\Block\Recurring\Profile\Related\Orders;

/**
 * Test class for \Magento\Sales\Block\Recurring\Profile\Related\Orders\Grid
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
        $customer = $this->getMock('Magento\Customer\Model\Customer', array(), array(), '', false);
        $customer->expects($this->once())->method('getId')->will($this->returnValue(1));
        $store = $this->getMock('Magento\Core\Model\Store', array(), array(), '', false);
        $args = array(
            'getIncrementId', 'getCreatedAt', 'getCustomerName', 'getBaseGrandTotal', 'getStatusLabel', 'getId',
            '__wakeup'
        );
        $collectionElement = $this->getMock('Magento\Sales\Model\Recurring\Profile', $args, array(), '', false);
        $collectionElement->expects($this->once())->method('getIncrementId')
            ->will($this->returnValue(1));
        $collection = $this->getMock('Magento\Sales\Model\Resource\Order\Collection', array(), array(), '', false);
        $collection->expects($this->any())->method('addFieldToFilter')
            ->will($this->returnValue($collection));
        $collection->expects($this->once())->method('addFieldToSelect')
            ->will($this->returnValue($collection));
        $collection->expects($this->once())->method('addRecurringProfilesFilter')
            ->will($this->returnValue($collection));
        $collection->expects($this->once())->method('setOrder')
            ->will($this->returnValue($collection));
        $collection->expects($this->once())->method('getIterator')
            ->will($this->returnValue(new \ArrayIterator(array($collectionElement))));
        $profile = $this->getMock('Magento\Sales\Model\Recurring\Profile', array(), array(), '', false);
        $registry = $this->getMock('Magento\Core\Model\Registry', array(), array(), '', false);
        $registry->expects($this->at(0))
            ->method('registry')
            ->with('current_recurring_profile')
            ->will($this->returnValue($profile));
        $registry->expects($this->at(1))
            ->method('registry')
            ->with('current_customer')
            ->will($this->returnValue($customer));
        $profile->expects($this->once())->method('setStore')->with($store)->will($this->returnValue($profile));
        $profile->expects($this->once())->method('setLocale')->will($this->returnValue($profile));
        $storeManager = $this->getMock('Magento\Core\Model\StoreManagerInterface');
        $storeManager->expects($this->once())->method('getStore')
            ->will($this->returnValue($store));
        $locale = $this->getMock('\Magento\Core\Model\LocaleInterface');
        $locale->expects($this->once())->method('formatDate')
            ->will($this->returnValue('11-11-1999'));
        $helper = $this->getMock('Magento\Core\Helper\Data', array(), array(), '', false);
        $helper->expects($this->once())->method('formatCurrency')
            ->will($this->returnValue('10 USD'));
        $block = $this->_objectManagerHelper->getObject(
            'Magento\Sales\Block\Recurring\Profile\Related\Orders\Grid',
            array(
                'registry' => $registry,
                'storeManager' => $storeManager,
                'collection' => $collection,
                'locale' => $locale,
                'coreHelper' => $helper
            )
        );
        $pagerBlock = $this->getMockBuilder('Magento\Theme\Block\Html\Pager')
            ->disableOriginalConstructor()
            ->setMethods(array('setCollection'))
            ->getMock();
        $pagerBlock->expects($this->once())->method('setCollection')
            ->with($collection)
            ->will($this->returnValue($pagerBlock));
        $layout = $this->getMock('Magento\View\LayoutInterface');
        $layout->expects($this->once())->method('createBlock')
            ->will($this->returnValue($pagerBlock));
        $block->setLayout($layout);

        /**
         * @var \Magento\Sales\Block\Recurring\Profile\Related\Orders\Grid
         */
        $this->assertNotEmpty($block->getGridColumns());
        $expectedResult = array(new \Magento\Object(array(
            'increment_id' => 1,
            'increment_id_link_url' => null,
            'created_at'  => '11-11-1999',
            'customer_name' => null,
            'status' => null,
            'base_grand_total' => '10 USD'
        )));
        $this->assertEquals($expectedResult, $block->getGridElements());
    }
}
