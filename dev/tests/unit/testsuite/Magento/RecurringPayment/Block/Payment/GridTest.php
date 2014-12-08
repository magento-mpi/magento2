<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\RecurringPayment\Block\Payment;

/**
 * Test class for \Magento\RecurringPayment\Block\Payment\Grid
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

    /**
     * @return void
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function testPrepareLayout()
    {
        $customerId = 1;
        $registry = $this->getMockBuilder(
            'Magento\Framework\Registry'
        )->disableOriginalConstructor()->setMethods(
            ['registry']
        )->getMock();
        $registry->expects(
            $this->once()
        )->method(
            'registry'
        )->with(
            'current_customer_id'
        )->will(
            $this->returnValue($customerId)
        );
        $store = $this->getMockBuilder('Magento\Store\Model\Store')->disableOriginalConstructor()->getMock();
        $collectionElement = $this->getMockBuilder(
            'Magento\RecurringPayment\Model\Payment'
        )->disableOriginalConstructor()->setMethods(
            ['setStore', 'renderData', 'getReferenceId', '__wakeup']
        )->getMock();
        $collectionElement->expects(
            $this->once()
        )->method(
            'setStore'
        )->with(
            $store
        )->will(
            $this->returnValue($collectionElement)
        );
        $collectionElement->expects($this->once())->method('getReferenceId')->will($this->returnValue(1));
        $collectionElement->expects($this->atLeastOnce())->method('renderData')->will($this->returnValue(2));
        $collection = $this->getMockBuilder(
            'Magento\RecurringPayment\Model\Resource\Payment\Collection'
        )->disableOriginalConstructor()->setMethods(
            ['addFieldToFilter', 'addFieldToSelect', 'setOrder']
        )->getMock();
        $collection->expects(
            $this->once()
        )->method(
            'addFieldToFilter'
        )->with(
            'customer_id',
            1
        )->will(
            $this->returnValue($collection)
        );
        $collection->expects($this->once())->method('addFieldToSelect')->will($this->returnValue($collection));
        $collection->expects($this->once())->method('setOrder')->will($this->returnValue([$collectionElement]));

        $payment = $this->getMockBuilder(
            'Magento\RecurringPayment\Model\Payment'
        )->disableOriginalConstructor()->setMethods(
            ['getCollection', 'getFieldLabel', '__wakeup']
        )->getMock();
        $payment->expects($this->once())->method('getCollection')->will($this->returnValue($collection));

        $storeManager = $this->getMockBuilder(
            'Magento\Store\Model\StoreManager'
        )->disableOriginalConstructor()->setMethods(
            ['getStore']
        )->getMock();
        $storeManager->expects($this->once())->method('getStore')->will($this->returnValue($store));

        $locale = $this->getMockBuilder(
            '\Magento\Framework\Stdlib\DateTime\TimezoneInterface'
        )->disableOriginalConstructor()->setMethods(
            ['formatDate']
        )->getMockForAbstractClass();
        $locale->expects($this->once())->method('formatDate')->will($this->returnValue('11-11-1999'));
        $block = $this->_objectManagerHelper->getObject(
            'Magento\RecurringPayment\Block\Payment\Grid',
            [
                'recurringPayment' => $payment,
                'registry' => $registry,
                'storeManager' => $storeManager,
                'localeDate' => $locale
            ]
        );
        $pagerBlock = $this->getMockBuilder(
            'Magento\Theme\Block\Html\Pager'
        )->disableOriginalConstructor()->setMethods(
            ['setCollection']
        )->getMock();
        $pagerBlock->expects(
            $this->once()
        )->method(
            'setCollection'
        )->with(
            [$collectionElement]
        )->will(
            $this->returnValue($pagerBlock)
        );

        $layout = $this->_getMockLayout();
        $layout->expects($this->once())->method('createBlock')->will($this->returnValue($pagerBlock));

        $block->setLayout($layout);

        $this->assertNotEmpty($block->getGridColumns());
        $expectedResult = [
            new \Magento\Framework\Object(
                [
                    'reference_id' => 1,
                    'reference_id_link_url' => null,
                    'state' => 2,
                    'created_at' => '11-11-1999',
                    'updated_at' => '',
                    'method_code' => 2,
                ]
            ),
        ];
        $this->assertEquals($expectedResult, $block->getGridElements());
    }

    /**
     * Get layout mock
     *
     * @return \Magento\Framework\View\LayoutInterface
     */
    protected function _getMockLayout()
    {
        $layout = $this->getMockBuilder(
            'Magento\Framework\View\LayoutInterface'
        )->disableOriginalConstructor()->setMethods(
            ['createBlock', 'getChildName', 'setChild']
        )->getMockForAbstractClass();

        return $layout;
    }
}
