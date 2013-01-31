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
     * @var Mage_Launcher_Block_Adminhtml_Drawer
     */
    protected $_drawer;

    public function setUp()
    {
        $objectManager = new Magento_Test_Helper_ObjectManager($this);
        $linkTracker = $this->getMock(
            'Mage_Launcher_Model_LinkTracker',
            array('save', 'load'), array(), '', false
        );

        $urlBuilder = $this->getMock('Mage_Backend_Model_Url', array('getUrl'), array(), '', false);

        $arguments = array(
            'urlBuilder' => $urlBuilder,
            'linkTracker' => $linkTracker
        );
        $this->_drawer = $objectManager->getBlock('Mage_Launcher_Block_Adminhtml_Drawer', $arguments);
    }

    /**
     * @covers Mage_Launcher_Block_Adminhtml_Drawer::getTrackerLink
     */
    public function testGetTrackerLink()
    {
        $route = 'adminhtml/cms_page/edit';
        $params = array('page_id' => 3);
        $linkTracker = $this->_drawer->getTrackerLink($route, $params);

        $this->assertEquals($linkTracker->getCode(), md5($route . serialize($params)));
        $this->assertEquals($linkTracker->getUrl(), $route);
        $this->assertEquals($linkTracker->getParams(), serialize($params));
    }

    /**
     * Test covers the case when getTrackerLink method called consequently some times with different args
     *
     * @param array $firstLinkParams
     * @param array $firstLinkExpected
     * @param array $secondLinkParams
     * @param array $secondLinkExpected
     * @dataProvider getTrackerLinkConsequentDataProvider
     * @covers Mage_Launcher_Block_Adminhtml_Drawer::getTrackerLink
     */
    public function testGetTrackerLinkConsequent($firstLinkParams, $firstLinkExpected,
        $secondLinkParams, $secondLinkExpected
    ) {
        $linkTracker = $this->_drawer->getTrackerLink($firstLinkParams['route'], $firstLinkParams['params']);

        $this->assertEquals($linkTracker->getCode(), $firstLinkExpected['code']);
        $this->assertEquals($linkTracker->getUrl(), $firstLinkExpected['url']);
        $this->assertEquals($linkTracker->getParams(), $firstLinkExpected['params']);

        // Emulate insertion to DB
        $linkTracker->setId(1);

        $linkTracker = $this->_drawer->getTrackerLink($secondLinkParams['route'], $secondLinkParams['params']);

        $this->assertEquals($linkTracker->getCode(), $secondLinkExpected['code']);
        $this->assertEquals($linkTracker->getUrl(), $secondLinkExpected['url']);
        $this->assertEquals($linkTracker->getParams(), $secondLinkExpected['params']);
    }

    public function getTrackerLinkConsequentDataProvider()
    {
        $route1 = 'adminhtml/cms_page/edit';
        $params1 = array('page_id' => 3);

        $route2 = 'adminhtml/test_page/load';
        $params2 = array('page_id' => 7);

        return array(
            array(
                array(
                    'route' => $route1,
                    'params' => $params1
                ),
                array(
                    'code' => md5($route1 . serialize($params1)),
                    'url' => $route1,
                    'params' => serialize($params1)
                ),
                array(
                    'route' => $route2,
                    'params' => $params2
                ),
                array(
                    'code' => md5($route2 . serialize($params2)),
                    'url' => $route2,
                    'params' => serialize($params2)
                )
            )
        );
    }
}
