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
 * Test class for Saas_Launcher_Block_Adminhtml_Storelauncher_Design_Drawer
 */
class Saas_Launcher_Block_Adminhtml_Storelauncher_Design_DrawerTest extends PHPUnit_Framework_TestCase
{
    /**
     * Drawer Block
     *
     * @var Saas_Launcher_Block_Adminhtml_Storelauncher_Design_Drawer
     */
    protected $_drawerBlock;

    /**
     * Object Manager Mock
     *
     * @var Magento_ObjectManager|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManagerMock;

    /**
     * Launcher helper mock
     *
     * @var Saas_Launcher_Helper_Data|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_launcherHelperMock;

    /**
     * DB file storage mock
     *
     * @var Mage_Core_Helper_File_Storage_Database|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_fileDbHelperMock;

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
            ->will($this->returnValue('Saas_Launcher_Block_Adminhtml_Storelauncher_Design_Drawer_Theme'));
        $layout->expects($this->any())
            ->method('getBlock')
            ->with('Saas_Launcher_Block_Adminhtml_Storelauncher_Design_Drawer_Theme')
            ->will($this->returnValue(
                $this->getMock('Saas_Launcher_Block_Adminhtml_Storelauncher_Design_Drawer_Theme',
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

        $this->_launcherHelperMock = $this->getMock('Saas_Launcher_Helper_Data', array(), array(), '', false);
        $this->_launcherHelperMock->expects($this->any())
            ->method('getCurrentStoreView')
            ->will($this->returnValue($store));

        $this->_fileDbHelperMock = $this->getMock('Mage_Core_Helper_File_Storage_Database',
            array(), array(), '', false);
        $this->_fileDbHelperMock->expects($this->any())
            ->method('checkDbUsage')
            ->will($this->returnValue(false));

        $helperFactoryMock = $this->getMock('Mage_Core_Model_Factory_Helper',
            array(), array(), '', false, false
        );
        $helperFactoryMock->expects($this->any())
            ->method('get')
            ->will($this->returnCallback(array($this, 'helperCallback')));

        $this->_objectManagerMock = $this->getMock('Magento_ObjectManager', array(), array(), '', false);

        $arguments = array(
            'layout' => $layout,
            'storeConfig' => $config,
            'helperFactory' => $helperFactoryMock,
            'themeService' => $themeService,
            'objectManager' => $this->_objectManagerMock
        );

        $this->_drawerBlock = $objectManagerHelper->getObject(
            'Saas_Launcher_Block_Adminhtml_Storelauncher_Design_Drawer',
            $arguments
        );

        $this->_configData = array();
    }

    public function tearDown()
    {
        unset($this->_drawerBlock);
    }

    /**
     * Get mocked themes
     * @return array
     */
    protected function _getThemes()
    {
        $themes = array();
        for ($iterationIndex = 0; $iterationIndex < 5; $iterationIndex++) {
            $themes[] = $this->getMock('Saas_Launcher_Block_Adminhtml_Storelauncher_Design_Drawer_Theme',
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
     * Callback function for Helper Factory
     *
     * @param string $name
     * @return mixed
     */
    public function helperCallback($name)
    {
        switch ($name) {
            case 'Saas_Launcher_Helper_Data':
                return $this->_launcherHelperMock;
            case 'Mage_Core_Helper_File_Storage_Database':
                return $this->_fileDbHelperMock;
        }
        return null;
    }

    /**
     * Get Config Source data
     *
     * @return array
     */
    protected function _getConfigSource()
    {
        return array(
            1 => array(Mage_Core_Model_View_Design::XML_PATH_THEME_ID => '118'),
            null => array(Mage_Core_Model_View_Design::XML_PATH_THEME_ID => '272'),
        );
    }

    /**
     * Get Config Source data
     *
     * @return array
     */
    protected function _getConfigSourceLogo()
    {
        return array(
            1 => array(Saas_Launcher_Model_Storelauncher_Design_SaveHandler::XML_PATH_LOGO => 'dragons.png'),
            null => array(Saas_Launcher_Model_Storelauncher_Design_SaveHandler::XML_PATH_LOGO => 'magento.png'),
        );
    }

    /**
     * @covers Saas_Launcher_Block_Adminhtml_Storelauncher_Design_Drawer::getThemesBlocks
     */
    public function testGetThemesBlocks()
    {
        $themesBlocks = $this->_drawerBlock->getThemesBlocks();
        $this->assertEquals(5, count($themesBlocks));
    }

    /**
     * @covers Saas_Launcher_Block_Adminhtml_Storelauncher_Design_Drawer::getCurrentThemeId
     */
    public function testGetCurrentThemeId()
    {
        $this->_configData = $this->_getConfigSource();

        $result = $this->_drawerBlock->getCurrentThemeId();

        $this->assertEquals($result, 118);
    }

    public function testGetLogoUrl()
    {
        $this->_configData = $this->_getConfigSourceLogo();

        $fileSystemMock = $this->getMockBuilder('Magento_Filesystem')->disableOriginalConstructor()->getMock();
        $fileSystemMock->expects($this->any())
            ->method('isFile')
            ->with($this->stringContains('logo' . DIRECTORY_SEPARATOR . 'dragons.png'), $this->isNull())
            ->will($this->returnValue(true));

        $this->_objectManagerMock
            ->expects($this->any())
            ->method('get')
            ->with('Magento_Filesystem')
            ->will($this->returnValue($fileSystemMock));

        $result = $this->_drawerBlock->getLogoUrl();
        $this->assertEquals('logo/dragons.png', $result);
    }
}
