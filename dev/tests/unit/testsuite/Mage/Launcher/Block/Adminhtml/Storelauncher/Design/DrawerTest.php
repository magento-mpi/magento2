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
 * Test class for Mage_Launcher_Block_Adminhtml_Storelauncher_Design_Drawer
 */
class Mage_Launcher_Block_Adminhtml_Storelauncher_Design_DrawerTest extends PHPUnit_Framework_TestCase
{
    /**
     * Drawer Block
     *
     * @var Mage_Launcher_Block_Adminhtml_Storelauncher_Design_Drawer
     */
    protected $_drawerBlock;

    /**
     * Config data array, used in configCallback method
     *
     * @var array
     */
    protected $_configData;

    public function setUp()
    {
        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);

        $config = $this->getMock('Mage_Core_Model_Store_Config', array('getConfig'), array(), '', false);
        $config->expects($this->any())
            ->method('getConfig')
            ->will($this->returnCallback(array($this, 'configCallback')));

        $layout = $this->getMock('Mage_Core_Model_Layout', array('getBlock', 'getChildName'), array(), '', false);

        $layout->expects($this->any())
            ->method('getChildName')
            ->with(null, 'theme-preview')
            ->will($this->returnValue('Mage_Launcher_Block_Adminhtml_Storelauncher_Design_Drawer_Theme'));
        $layout->expects($this->any())
            ->method('getBlock')
            ->with('Mage_Launcher_Block_Adminhtml_Storelauncher_Design_Drawer_Theme')
            ->will($this->returnValue(
                $this->getMock('Mage_Launcher_Block_Adminhtml_Storelauncher_Design_Drawer_Theme',
                    array(), array(), '', false)
            )
        );

        $themeService = $this->getMock('Mage_Core_Model_Theme_Service', array('getPhysicalThemes'), array(), '', false);

        $themeService->expects($this->any())
            ->method('getPhysicalThemes')
            ->will($this->returnValue($this->_getThemes()));

        $store = $this->getMock('Mage_Core_Model_Store', array(), array(), '', false);
        $store->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(1));
        $store->expects($this->any())
            ->method('getCode')
            ->will($this->returnValue('default'));

        $helperMock = $this->getMock('Mage_Launcher_Helper_Data', array(), array(), '', false);
        $helperMock->expects($this->any())
            ->method('getCurrentStoreView')
            ->will($this->returnValue($store));

        $helperFactoryMock = $this->getMock('Mage_Core_Model_Factory_Helper',
            array(), array(), '', false, false
        );
        $helperFactoryMock->expects($this->any())->method('get')->with('Mage_Launcher_Helper_Data')
            ->will($this->returnValue($helperMock));

        $arguments = array(
            'layout' => $layout,
            'storeConfig' => $config,
            'helperFactory' => $helperFactoryMock,
            'themeService' => $themeService
        );

        $this->_drawerBlock = $objectManagerHelper->getObject(
            'Mage_Launcher_Block_Adminhtml_Storelauncher_Design_Drawer',
            $arguments
        );

        $this->_configData = array();
    }

    public function tearDown()
    {
        unset($this->_drawerBlock);
    }

    /**
     * @covers Mage_Launcher_Block_Adminhtml_Storelauncher_Design_Drawer::getThemesBlocks
     */
    public function testGetThemesBlocks()
    {
        $themesBlocks = $this->_drawerBlock->getThemesBlocks();
        $this->assertEquals(5, count($themesBlocks));
    }

    /**
     * Get mocked themes
     * @return array
     */
    protected function _getThemes()
    {
        $themes = array();
        for ($iterationIndex = 0; $iterationIndex < 5; $iterationIndex++) {
            $themes[] = $this->getMock('Mage_Launcher_Block_Adminhtml_Storelauncher_Design_Drawer_Theme',
                array(), array(), '', false);
        }
        return $themes;
    }

    /**
     * Callback function for getConfig method
     *
     * @param string $path
     * @param mixed $store
     * @return string
     */
    public function configCallback($path, $store = null)
    {
        return isset($this->_configData[$store][$path]) ? $this->_configData[$store][$path] : '';
    }

    /**
     * Get Config Source data
     *
     * @return array
     */
    protected function _getConfigSource()
    {
        return array(
            1 => array('design/theme/theme_id' => '118'),
            null => array('design/theme/theme_id' => '272'),
        );
    }

    /**
     * @covers Mage_Launcher_Block_Adminhtml_Storelauncher_Design_Drawer::getCurrentThemeId
     */
    public function testGetCurrentThemeId()
    {
        $this->_configData = $this->_getConfigSource();

        $result = $this->_drawerBlock->getCurrentThemeId();

        $this->assertEquals($result, 118);
    }

}
