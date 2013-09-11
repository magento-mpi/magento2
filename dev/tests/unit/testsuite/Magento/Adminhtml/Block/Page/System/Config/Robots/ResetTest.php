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
        $this->_resetRobotsBlock = $objectManagerHelper->getObject(
            '\Magento\Adminhtml\Block\Page\System\Config\Robots\Reset',
            array(
                'application' => $this->getMock('Magento\Core\Model\App', array(), array(), '', false),
                'urlBuilder' => $this->getMock('Magento\Backend\Model\Url', array(), array(), '', false)
            )
        );
        $this->_mockRobotsHelper = $this->getMock('Magento\Page\Helper\Robots',
            array('getRobotsDefaultCustomInstructions'), array(), '', false, false
        );
        Mage::register('_helper/Magento\Page\Helper\Robots', $this->_mockRobotsHelper);
    }

    protected function tearDown()
    {
        Mage::unregister('_helper/Magento\Page\Helper\Robots');
    }

    /**
     * @covers \Magento\Adminhtml\Block\Page\System\Config\Robots\Reset::getRobotsDefaultCustomInstructions
     */
    public function testGetRobotsDefaultCustomInstructions()
    {
        $expectedInstructions = 'User-agent: *';
        $this->_mockRobotsHelper
            ->expects($this->once())
            ->method('getRobotsDefaultCustomInstructions')
            ->will($this->returnValue($expectedInstructions));
        $this->assertEquals($expectedInstructions, $this->_resetRobotsBlock->getRobotsDefaultCustomInstructions());
    }
}
