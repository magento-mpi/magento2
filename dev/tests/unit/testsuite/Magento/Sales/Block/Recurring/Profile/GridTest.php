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

/**
 * Test class for \Magento\Sales\Block\Recurring\Profile\Grid
 */
class Magento_Sales_Block_Recurring_Profile_GridTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_TestFramework_Helper_ObjectManager
     */
    protected $_objectManagerHelper;

    protected function setUp()
    {
        $this->_objectManagerHelper = new Magento_TestFramework_Helper_ObjectManager($this);
    }

    public function testPrepareLayout()
    {
        $customer = $this->getMockBuilder('Magento\Customer\Model\Customer')
            ->disableOriginalConstructor()
            ->setMethods(array('getId'))
            ->getMock();
        $customer->expects($this->once())->method('getId')->will($this->returnValue(1));

        $registry = $this->getMockBuilder('Magento\Core\Model\Registry')
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

        $collectionElement = $this->getMockBuilder('Magento\Sales\Model\Recurring\Profile')
            ->disableOriginalConstructor()
            ->setMethods(array('setStore', 'setLocale', 'renderData', 'getReferenceId'))
            ->getMock();
        $collectionElement->expects($this->once())->method('setStore')
            ->with($store)
            ->will($this->returnValue($collectionElement));
        $collectionElement->expects($this->once())->method('getReferenceId')
            ->will($this->returnValue(1));
        $collectionElement->expects($this->atLeastOnce())->method('renderData')
            ->will($this->returnValue(2));

        $collection = $this->getMockBuilder('Magento\Sales\Model\Resource\Recurring\Profile\Collection')
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

        $profile = $this->getMockBuilder('Magento\Sales\Model\Recurring\Profile')
            ->disableOriginalConstructor()
            ->setMethods(array('getCollection', 'getFieldLabel'))
            ->getMock();
        $profile->expects($this->once())->method('getCollection')
            ->will($this->returnValue($collection));

        $storeManager = $this->getMockBuilder('Magento\Core\Model\StoreManager')
            ->disableOriginalConstructor()
            ->setMethods(array('getStore'))
            ->getMock();
        $storeManager->expects($this->once())->method('getStore')
            ->will($this->returnValue($store));

        $context = $this->_getContext();

        $block = $this->_objectManagerHelper->getObject(
            'Magento\Sales\Block\Recurring\Profile\Grid',
            array(
                'context' => $context,
                'profile' => $profile,
                'registry' => $registry,
                'storeManager' => $storeManager,
            )
        );

        $pagerBlock = $this->getMockBuilder('Magento\Page\Block\Html\Pager')
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
        $expectedResult = array(new \Magento\Object(array(
            'reference_id' => 1,
            'reference_id_link_url' => null,
            'state'       => 2,
            'created_at'  => '11-11-1999',
            'updated_at'  => '',
            'method_code' => 2,
        )));
        $this->assertEquals($expectedResult, $block->getGridElements());
    }

    /**
     * Get layout mock
     *
     * @return \Magento\Core\Model\Layout
     */
    protected function _getMockLayout()
    {
        $layout = $this->getMockBuilder('Magento\Core\Model\Layout')
            ->disableOriginalConstructor()
            ->setMethods(array('createBlock', 'getChildName', 'setChild'))
            ->getMock();

        return $layout;
    }

    /**
     * Get context object
     *
     * @return \Magento\Core\Block\Template\Context
     */
    protected function _getContext()
    {
        $helper = $this->getMockBuilder('Magento\Customer\Helper\Data')
            ->disableOriginalConstructor()
            ->setMethods(array('formatDate'))
            ->getMock();
        $helper->expects($this->once())->method('formatDate')
            ->will($this->returnValue('11-11-1999'));

        $helperFactory = $this->getMockBuilder('Magento\Core\Model\Factory\Helper')
            ->disableOriginalConstructor()
            ->setMethods(array('get'))
            ->getMock();
        $helperFactory->expects($this->any())->method('get')->will($this->returnValue($helper));

        /** @var  \Magento\Core\Block\Template\Context $context */
        $context = $this->_objectManagerHelper->getObject(
            'Magento\Core\Block\Template\Context',
            array('helperFactory' => $helperFactory)
        );
        return $context;
    }
}
