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
 * Test class for \Magento\Sales\Block\Recurring\Profile\View\Address
 */
class Magento_Sales_Block_Recurring_Profile_View_AddressTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Sales\Block\Recurring\Profile\View\Address
     */
    protected $_block;

    /**
     * @var \Magento\Sales\Model\Recurring\Profile
     */
    protected $_profile;

    /**
     * @var \Magento\Sales\Model\Order\AddressFactory
     */
    protected $_addressFactory;

    protected function setUp()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->_profile = $this->getMockBuilder('Magento\Sales\Model\Recurring\Profile')
            ->disableOriginalConstructor()
            ->setMethods(array('setStore', 'setLocale', 'getData', 'getInfoValue'))
            ->getMock();
        $this->_profile->expects($this->once())->method('setStore')->will($this->returnValue($this->_profile));
        $this->_profile->expects($this->once())->method('setLocale')->will($this->returnValue($this->_profile));

        $registry = $this->getMockBuilder('Magento\Core\Model\Registry')
            ->disableOriginalConstructor()
            ->setMethods(array('registry'))
            ->getMock();
        $registry->expects($this->once())
            ->method('registry')
            ->with('current_recurring_profile')
            ->will($this->returnValue($this->_profile));

        $store = $this->getMockBuilder('Magento\Core\Model\Store')
            ->disableOriginalConstructor()
            ->getMock();

        $storeManager = $this->getMockBuilder('Magento\Core\Model\StoreManager')
            ->disableOriginalConstructor()
            ->setMethods(array('getStore'))
            ->getMock();
        $storeManager->expects($this->once())->method('getStore')
            ->will($this->returnValue($store));

        $this->_addressFactory = $this->getMockBuilder('Magento\Sales\Model\Order\AddressFactory')
            ->disableOriginalConstructor()
            ->setMethods(array('create'))
            ->getMock();

        $this->_block = $objectManager->getObject(
            'Magento\Sales\Block\Recurring\Profile\View\Address',
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

        $parentBlock = $this->getMockBuilder('Magento\Core\Block\Template')
            ->disableOriginalConstructor()
            ->setMethods(array('unsetChild'))
            ->getMock();
        $parentBlock->expects($this->once())->method('unsetChild');

        $layout = $this->getMockBuilder('Magento\Core\Model\Layout')
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
        $address = $this->getMockBuilder('Magento\Sales\Model\Order\Address')
            ->disableOriginalConstructor()
            ->setMethods(array('format'))
            ->getMock();
        $this->_addressFactory->expects($this->once())->method('create')->will($this->returnValue($address));

        $layout = $this->getMockBuilder('Magento\Core\Model\Layout')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_block->setLayout($layout);

        $this->assertNotEmpty($this->_block->getRenderedInfo());
    }
}
