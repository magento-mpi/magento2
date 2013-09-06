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
 * Test class for Magento_Sales_Block_Recurring_Profile_View_Address
 */
class Magento_Sales_Block_Recurring_Profile_View_AddressTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Sales_Block_Recurring_Profile_View_Address
     */
    protected $_block;

    /**
     * @var Magento_Sales_Model_Recurring_Profile
     */
    protected $_profile;

    /**
     * @var Magento_Sales_Model_Order_AddressFactory
     */
    protected $_addressFactory;

    public function setUp()
    {
        $objectManager = new Magento_Test_Helper_ObjectManager($this);

        $this->_profile = $this->getMockBuilder('Magento_Sales_Model_Recurring_Profile')
            ->disableOriginalConstructor()
            ->setMethods(array('setStore', 'setLocale', 'getData', 'getInfoValue'))
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

        $this->_addressFactory = $this->getMockBuilder('Magento_Sales_Model_Order_AddressFactory')
            ->disableOriginalConstructor()
            ->setMethods(array('create'))
            ->getMock();

        $this->_block = $objectManager->getObject(
            'Magento_Sales_Block_Recurring_Profile_View_Address',
            array(
                'registry' => $registry,
                'storeManager' => $storeManager,
                'addressFactory' => $this->_addressFactory,
            )
        );
    }

    public function testPrepareLayoutInfoEmpty()
    {
        $this->_profile->expects($this->once())->method('getInfoValue')->will($this->returnValue('1'));
        $this->_block->setAddressType('shipping');

        $parentBlock = $this->getMockBuilder('Magento_Core_Block_Template')
            ->disableOriginalConstructor()
            ->setMethods(array('unsetChild'))
            ->getMock();
        $parentBlock->expects($this->once())->method('unsetChild');

        $layout = $this->getMockBuilder('Magento_Core_Model_Layout')
            ->disableOriginalConstructor()
            ->setMethods(array('getParentName', 'getBlock'))
            ->getMock();
        $layout->expects($this->once())->method('getParentName')
            ->will($this->returnValue('name'));
        $layout->expects($this->once())->method('getBlock')
            ->will($this->returnValue($parentBlock));

        $this->_block->setLayout($layout);

        $this->assertEmpty($this->_block->getRenderedInfo());
    }

    public function testPrepareLayoutInfoAdded()
    {
        $address = $this->getMockBuilder('Magento_Sales_Model_Order_Address')
            ->disableOriginalConstructor()
            ->setMethods(array('format'))
            ->getMock();
        $this->_addressFactory->expects($this->once())->method('create')->will($this->returnValue($address));

        $layout = $this->getMockBuilder('Magento_Core_Model_Layout')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_block->setLayout($layout);

        $this->assertNotEmpty($this->_block->getRenderedInfo());
    }
}
