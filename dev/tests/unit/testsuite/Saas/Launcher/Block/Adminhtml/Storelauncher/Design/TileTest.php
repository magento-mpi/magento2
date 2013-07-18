<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Saas_Launcher
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Saas_Launcher_Block_Adminhtml_Storelauncher_Design_Tile
 */
class Saas_Launcher_Block_Adminhtml_Storelauncher_Design_TileTest extends PHPUnit_Framework_TestCase
{
    /**
     * Tile Block
     *
     * @var Saas_Launcher_Block_Adminhtml_Storelauncher_Design_Tile
     */
    protected $_tileBlock;

    /**
     * Theme Service mock
     *
     * @var Mage_Core_Model_ThemeFactory|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_themeFactoryMock;

    /**
     * Store mock
     *
     * @var Mage_Core_Model_Store|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeMock;

    /**
     * Launcher helper mock
     *
     * @var Saas_Launcher_Helper_Data|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_launcherHelperMock;

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

        $this->_themeFactoryMock = $this->getMock('Mage_Core_Model_ThemeFactory', array('create'), array(), '', false);

        $this->_storeMock = $this->getMock('Mage_Core_Model_Store', array(), array(), '', false);

        $this->_launcherHelperMock = $this->getMock('Saas_Launcher_Helper_Data', array(), array(), '', false);

        $arguments = array(
            'storeConfig' => $config,
            'launcherHelper' => $this->_launcherHelperMock,
            'themeFactory' => $this->_themeFactoryMock,
        );

        $this->_tileBlock = $objectManagerHelper->getObject(
            'Saas_Launcher_Block_Adminhtml_Storelauncher_Design_Tile',
            $arguments
        );

        $this->_configData = $this->_getConfigSource();
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
        if (is_object($store)) {
            $store = $store->getId();
        }
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
            1 => array(
                Mage_Core_Model_View_Design::XML_PATH_THEME_ID => '118',
                Saas_Launcher_Model_Storelauncher_Design_SaveHandler::XML_PATH_LOGO => 'stores/1/dragons.png'
            ),
            null => array(
                Mage_Core_Model_View_Design::XML_PATH_THEME_ID => '272',
                Saas_Launcher_Model_Storelauncher_Design_SaveHandler::XML_PATH_LOGO => 'default/magento.png'
            ),
        );
    }

    /**
     * @dataProvider isLogoUploadedDataProvider
     */
    public function testIsLogoUploaded($store, $expected)
    {
        $this->_storeMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($store));
        $this->_launcherHelperMock->expects($this->any())
            ->method('getCurrentStoreView')
            ->will($this->returnValue($this->_storeMock));

        $result = $this->_tileBlock->isLogoUploaded();

        $this->assertEquals($expected, $result);
    }

    /**
     * Data Provider for testGetIsLogoUploaded
     *
     * @return array
     */
    public function isLogoUploadedDataProvider()
    {
        return array(
            array(1, true),
            array(2, false)
        );
    }

    /**
     * @covers Saas_Launcher_Block_Adminhtml_Storelauncher_Design_Tile::getThemeName
     * @dataProvider getThemeNameDataProvider
     */
    public function testGetThemeName($store, $themeId, $themeExists, $expected)
    {
        $theme = $this->getMock('Mage_Core_Model_Theme', array('getThemeTitle', 'load'), array(), '', false);

        if ($themeExists) {
            $theme->expects($this->once())
                ->method('load')
                ->with($this->equalTo($themeId))
                ->will($this->returnSelf());


            $theme->expects($this->once())
                ->method('getThemeTitle')
                ->will($this->returnValue('Magento Demo'));

            $this->_themeFactoryMock->expects($this->once())
                ->method('create')
                ->will($this->returnValue($theme));
        }

        $this->_storeMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue($store));
        $this->_launcherHelperMock->expects($this->once())
            ->method('getCurrentStoreView')
            ->will($this->returnValue($this->_storeMock));

        $result = $this->_tileBlock->getThemeName();
        $this->assertEquals($expected, $result);
    }

    public function getThemeNameDataProvider()
    {
        return array(
            array(1, 118, true, 'Magento Demo'),
            array(2, 0, false, ''),
        );
    }
}
