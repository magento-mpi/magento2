<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for \Magento\Adminhtml\Block\Page\System\Config\Robots\Reset
 */
class Magento_Adminhtml_Block_Page_System_Config_Robots_ResetTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Adminhtml\Block\Page\System\Config\Robots\Reset
     */
    private $_resetRobotsBlock;

    /**
     * @var \Magento\Page\Helper\Robots|PHPUnit_Framework_MockObject_MockObject
     */
    private $_mockRobotsHelper;

    protected function setUp()
    {
        $objectManagerHelper = new Magento_TestFramework_Helper_ObjectManager($this);

        $this->_mockRobotsHelper = $this->getMock('Magento\Page\Helper\Robots',
            array('getRobotsDefaultCustomInstructions'), array(), '', false, false
        );

        $this->_resetRobotsBlock = $objectManagerHelper->getObject(
            'Magento\Adminhtml\Block\Page\System\Config\Robots\Reset',
            array(
                'pageRobots' => $this->_mockRobotsHelper,
                'coreData' => $this->getMock('Magento\Core\Helper\Data', array(), array(), '', false),
                'application' => $this->getMock('Magento\Core\Model\App', array(), array(), '', false),
            )
        );

        $coreRegisterMock = $this->getMock('Magento\Core\Model\Registry');
        $coreRegisterMock->expects($this->any())
            ->method('registry')
            ->with('_helper/Magento_Page_Helper_Robots')
            ->will($this->returnValue($this->_mockRobotsHelper));

        $objectManagerMock = $this->getMockBuilder('Magento\ObjectManager')->getMock();
        $objectManagerMock->expects($this->any())
            ->method('get')
            ->with('Magento_Core_Model_Registry')
            ->will($this->returnValue($coreRegisterMock));

        Mage::reset();
        Mage::setObjectManager($objectManagerMock);
    }

    /**
     * @covers \Magento\Adminhtml\Block\Page\System\Config\Robots\Reset::getRobotsDefaultCustomInstructions
     */
    public function testGetRobotsDefaultCustomInstructions()
    {
        $expectedInstructions = 'User-agent: *';
        $this->_mockRobotsHelper->expects($this->once())
            ->method('getRobotsDefaultCustomInstructions')
            ->will($this->returnValue($expectedInstructions));
        $this->assertEquals($expectedInstructions, $this->_resetRobotsBlock->getRobotsDefaultCustomInstructions());
    }
}
