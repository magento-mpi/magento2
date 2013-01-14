<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Launcher
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Launcher_Block_Adminhtml_Drawer
 */
class Mage_Launcher_Block_Adminhtml_DrawerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers Mage_Launcher_Block_Adminhtml_Drawer::getTrackerLink
     */
    public function testGetTrackerLink()
    {
        $objectManager = new Magento_Test_Helper_ObjectManager($this);
        $link = $this->getMock(
            'Mage_Launcher_Model_LinkTracker',
            array('save', 'load'), array(), '', false
        );

        $linkTrackerFactory = $this->getMock(
            'Mage_Launcher_Model_LinkTrackerFactory',
            array('create'), array(), '', false
        );

        $linkTrackerFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($link));

        $urlBuilder = $this->getMock('Mage_Backend_Model_Url', array('getUrl'), array(), '', false);

        $arguments = array(
            'urlBuilder' => $urlBuilder,
            'linkTrackerFactory' => $linkTrackerFactory
        );
        $drawer = $objectManager->getBlock('Mage_Launcher_Block_Adminhtml_Drawer', $arguments);
        $route = 'adminhtml/cms_page/edit';
        $params = array('page_id' => 3);
        $link = $drawer->getTrackerLink($route, $params);

        $this->assertEquals($link->getCode(), md5($route . serialize($params)));
        $this->assertEquals($link->getUrl(), $route);
        $this->assertEquals($link->getParams(), serialize($params));
    }
}
