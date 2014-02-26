<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\RecurringProfile\Block\Profile\View;

/**
 * Test class for \Magento\RecurringProfile\Block\Profile\View\Reference
 */
class ReferenceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\RecurringProfile\Block\Profile\View\Reference
     */
    protected $_block;

    /**
     * @var \Magento\RecurringProfile\Model\Profile
     */
    protected $_profile;

    public function testPrepareLayout()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->_profile = $this->getMockBuilder('Magento\RecurringProfile\Model\Profile')
            ->disableOriginalConstructor()
            ->setMethods(array('setStore', 'getFieldLabel', 'renderData', '__wakeup'))
            ->getMock();
        $this->_profile->expects($this->once())->method('setStore')->will($this->returnValue($this->_profile));

        $registry = $this->getMockBuilder('Magento\Registry')
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
            'Magento\RecurringProfile\Block\Profile\View\Reference',
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
