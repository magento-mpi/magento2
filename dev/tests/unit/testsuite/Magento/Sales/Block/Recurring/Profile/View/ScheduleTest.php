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
namespace Magento\Sales\Block\Recurring\Profile\View;

/**
 * Test class for \Magento\Sales\Block\Recurring\Profile\View\Schedule
 */
class ScheduleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Sales\Block\Recurring\Profile\View\Schedule
     */
    protected $_block;

    /**
     * @var \Magento\Sales\Model\Recurring\Profile
     */
    protected $_profile;

    public function testPrepareLayout()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->_profile = $this->getMockBuilder('Magento\Sales\Model\Recurring\Profile')
            ->disableOriginalConstructor()
            ->setMethods(array('setStore', 'setLocale', 'getFieldLabel', 'renderData'))
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

        $this->_block = $objectManager->getObject(
            'Magento\Sales\Block\Recurring\Profile\View\Schedule',
            array(
                'registry' => $registry,
                'storeManager' => $storeManager,
            )
        );

        $layout = $this->getMockBuilder('Magento\Core\Model\Layout')
            ->disableOriginalConstructor()
            ->setMethods(array('helper'))
            ->getMock();

        $this->_block->setLayout($layout);

        $this->assertNotEmpty($this->_block->getRenderedInfo());
    }
}
