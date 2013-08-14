<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Saas_Launcher
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Saas_Launcher_Block_Adminhtml_Drawer
 */
class Saas_Launcher_Block_Adminhtml_DrawerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Saas_Launcher_Block_Adminhtml_Drawer
     */
    protected $_drawer;

    public function setUp()
    {
        $objectManager = new Magento_Test_Helper_ObjectManager($this);
        $linkTracker = $this->getMock(
            'Saas_Launcher_Model_LinkTracker',
            array('save', 'load'), array(), '', false
        );

        $urlBuilder = $this->getMock('Magento_Backend_Model_Url', array('getUrl'), array(), '', false);

        $arguments = array(
            'urlBuilder' => $urlBuilder,
            'linkTracker' => $linkTracker
        );
        $this->_drawer = $objectManager->getObject('Saas_Launcher_Block_Adminhtml_Drawer', $arguments);
    }

    /**
     * @covers Saas_Launcher_Block_Adminhtml_Drawer::getTrackerLink
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
     * @covers Saas_Launcher_Block_Adminhtml_Drawer::getTrackerLink
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

    /**
     * @dataProvider getConfigValueDataProvider
     * @param array $configMap
     * @param string $configPath
     * @param string $expectedValue
     */
    public function testGetConfigValue(array $configMap, $configPath, $expectedValue)
    {
        $result = $this->_getDrawerInstanceForGetConfigValueTest($configMap)->getConfigValue($configPath);

        $this->assertEquals($expectedValue, $result);
    }

    /**
     * Retrieve drawer instance for getConfigValue method test
     *
     * @param array $configMap
     * @return Saas_Launcher_Block_Adminhtml_Drawer
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    protected function _getDrawerInstanceForGetConfigValueTest(array $configMap)
    {
        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);

        // mock system configuration instance
        $config = $this->getMock('Magento_Core_Model_Store_Config', array('getConfig'), array(), '', false);
        $config->expects($this->any())
            ->method('getConfig')
            ->will($this->returnCallback(
                function ($configPath, $store) use ($configMap) {
                    return isset($configMap[$configPath]) ? $configMap[$configPath] : null;
                }
            ));

        $arguments = array(
            'storeConfig' => $config,
        );

        return $objectManagerHelper->getObject('Saas_Launcher_Block_Adminhtml_Drawer', $arguments);
    }

    /**
     * @return array
     */
    public function getConfigValueDataProvider()
    {
        return array(
            array(
                array('valid/config/path' => 'config_value'),
                'valid/config/path',
                'config_value'
            ),
            array(
                array(),
                'valid/config/path',
                null
            ),
        );
    }

    /**
     * @dataProvider getObscuredConfigValueDataProvider
     * @param array $configMap
     * @param string $configPath
     * @param string $expectedValue
     */
    public function testGetObscuredConfigValue(array $configMap, $configPath, $expectedValue)
    {
        $result = $this->_getDrawerInstanceForGetConfigValueTest($configMap)->getObscuredConfigValue($configPath);

        $this->assertEquals($expectedValue, $result);
    }

    /**
     * @return array
     */
    public function getObscuredConfigValueDataProvider()
    {
        return array(
            array(
                array('valid/config/path' => 'config_value'),
                'valid/config/path',
                Saas_Launcher_Block_Adminhtml_Drawer::SECRET_DATA_DISPLAY_VALUE
            ),
            array(
                array(),
                'valid/config/path',
                null
            ),
        );
    }
}
