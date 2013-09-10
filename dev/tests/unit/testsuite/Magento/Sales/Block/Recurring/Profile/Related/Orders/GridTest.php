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
 * Test class for Magento_Sales_Block_Recurring_Profile_Related_Orders_Grid
 */
class Magento_Sales_Block_Recurring_Profile_Related_Orders_GridTest extends PHPUnit_Framework_TestCase
{
    public function testPrepareLayout()
    {
        $objectManager = new Magento_TestFramework_Helper_ObjectManager($this);

        $customer = $this->getMockBuilder('Magento_Customer_Model_Customer')
            ->disableOriginalConstructor()
            ->setMethods(array('getId'))
            ->getMock();
        $customer->expects($this->once())->method('getId')->will($this->returnValue(1));

        $store = $this->getMockBuilder('Magento_Core_Model_Store')
            ->disableOriginalConstructor()
            ->getMock();

        $collectionElement = $this->getMockBuilder('Magento_Sales_Model_Recurring_Profile')
            ->disableOriginalConstructor()
            ->setMethods(array('setLocale', 'getIncrementId'))
            ->getMock();
        $collectionElement->expects($this->once())->method('getIncrementId')
            ->will($this->returnValue(1));

        $collection = $this->getMockBuilder('Magento_Sales_Model_Resource_Order_Collection')
            ->disableOriginalConstructor()
            ->setMethods(array('addFieldToFilter', 'addFieldToSelect', 'setOrder', 'addRecurringProfilesFilter',
                'getIterator'))
            ->getMock();
        $collection->expects($this->any())->method('addFieldToFilter')
            ->will($this->returnValue($collection));
        $collection->expects($this->once())->method('addFieldToSelect')
            ->will($this->returnValue($collection));
        $collection->expects($this->once())->method('addRecurringProfilesFilter')
            ->will($this->returnValue($collection));
        $collection->expects($this->once())->method('setOrder')
            ->will($this->returnValue($collection));
        $collection->expects($this->once())->method('getIterator')
            ->will($this->returnValue(new ArrayIterator(array($collectionElement))));

        $profile = $this->getMockBuilder('Magento_Sales_Model_Recurring_Profile')
            ->disableOriginalConstructor()
            ->setMethods(array('getFieldLabel'))
            ->getMock();

        $registry = $this->getMockBuilder('Magento_Core_Model_Registry')
            ->disableOriginalConstructor()
            ->setMethods(array('registry'))
            ->getMock();
        $registry->expects($this->at(0))
            ->method('registry')
            ->with('current_recurring_profile')
            ->will($this->returnValue($profile));
        $registry->expects($this->at(1))
            ->method('registry')
            ->with('current_customer')
            ->will($this->returnValue($customer));

        $storeManager = $this->getMockBuilder('Magento_Core_Model_StoreManager')
            ->disableOriginalConstructor()
            ->setMethods(array('getStore'))
            ->getMock();
        $storeManager->expects($this->once())->method('getStore')
            ->will($this->returnValue($store));

        $block = $objectManager->getObject(
            'Magento_Sales_Block_Recurring_Profile_Related_Orders_Grid',
            array(
                'profile' => $profile,
                'registry' => $registry,
                'storeManager' => $storeManager,
                'collection' => $collection
            )
        );

        $pagerBlock = $this->getMockBuilder('Magento_Page_Block_Html_Pager')
            ->disableOriginalConstructor()
            ->setMethods(array('setCollection'))
            ->getMock();
        $pagerBlock->expects($this->once())->method('setCollection')
            ->with($collection)
            ->will($this->returnValue($pagerBlock));
        $layout = $this->_getMockLayout();
        $layout->expects($this->once())->method('createBlock')
            ->will($this->returnValue($pagerBlock));

        $block->setLayout($layout);

        $this->assertNotEmpty($block->getGridColumns());
        $expectedResult = array(new Magento_Object(array(
            'increment_id' => 1,
            'increment_id_link_url' => null,
            'created_at'  => '11-11-1999',
            'customer_name' => null,
            'status' => null,
            'base_grand_total' => '10 USD'
        )));
        $this->assertEquals($expectedResult, $block->getGridElements());
    }

    protected function _getMockLayout()
    {
        $helper = $this->getMockBuilder('Magento_Core_Helper_Data')
            ->disableOriginalConstructor()
            ->setMethods(array('formatCurrency', 'formatDate'))
            ->getMock();
        $helper->expects($this->once())->method('formatCurrency')
            ->will($this->returnValue('10 USD'));
        $helper->expects($this->once())->method('formatDate')
            ->will($this->returnValue('11-11-1999'));

        $layout = $this->getMockBuilder('Magento_Core_Model_Layout')
            ->disableOriginalConstructor()
            ->setMethods(array('createBlock', 'getChildName', 'setChild', 'helper'))
            ->getMock();
        $layout->expects($this->any())->method('helper')
            ->will($this->returnValue($helper));
        return $layout;
    }
}
