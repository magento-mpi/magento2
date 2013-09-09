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
 * Test class for Magento_Sales_Block_Recurring_Profile_View_Schedule
 */
class Magento_Sales_Block_Recurring_Profile_View_ScheduleTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Sales_Block_Recurring_Profile_View_Schedule
     */
    protected $_block;

    /**
     * @var Magento_Sales_Model_Recurring_Profile
     */
    protected $_profile;

    public function testPrepareLayout()
    {
        $objectManager = new Magento_Test_Helper_ObjectManager($this);

        $this->_profile = $this->getMockBuilder('Magento_Sales_Model_Recurring_Profile')
            ->disableOriginalConstructor()
            ->setMethods(array('setStore', 'setLocale', 'getFieldLabel', 'renderData'))
            ->getMock();
        $this->_profile->expects($this->once())->method('setStore')->will($this->returnValue($this->_profile));
        $this->_profile->expects($this->once())->method('setLocale')->will($this->returnValue($this->_profile));

        $registry = $this->getMockBuilder('Magento_Core_Model_Registry')
            ->disableOriginalConstructor()
            ->setMethods(array('registry'))
            ->getMock();
        $registry->expects($this->once())
            ->method('registry')
            ->with('current_recurring_profile')
            ->will($this->returnValue($this->_profile));

        $store = $this->getMockBuilder('Magento_Core_Model_Store')
            ->disableOriginalConstructor()
            ->getMock();

        $storeManager = $this->getMockBuilder('Magento_Core_Model_StoreManager')
            ->disableOriginalConstructor()
            ->setMethods(array('getStore'))
            ->getMock();
        $storeManager->expects($this->once())->method('getStore')
            ->will($this->returnValue($store));

        $this->_block = $objectManager->getObject(
            'Magento_Sales_Block_Recurring_Profile_View_Schedule',
            array(
                'registry' => $registry,
                'storeManager' => $storeManager,
            )
        );

        $layout = $this->getMockBuilder('Magento_Core_Model_Layout')
            ->disableOriginalConstructor()
            ->setMethods(array('helper'))
            ->getMock();

        $this->_block->setLayout($layout);

        $this->assertNotEmpty($this->_block->getRenderedInfo());
    }
}
