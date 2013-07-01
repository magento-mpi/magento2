<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_Design_PackageTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param string $area
     * @param string $themePath
     * @param string $locale
     * @param string $file
     * @param string $module
     * @param string $expected
     * @dataProvider buildDeployedFilePathDataProvider
     */
    public function testBuildDeployedFilePath($area, $themePath, $locale, $file, $module, $expected)
    {
        $this->markTestIncomplete('It should be checked and may be fixed after task MAGETWO-10693');
        $actual = Mage_Core_Model_View_DeployedFilesManager::buildDeployedFilePath($area, $themePath, $locale, $file,
            $module, $expected);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @return array
     */
    public static function buildDeployedFilePathDataProvider()
    {
        return array(
            'no module' => array('a', 't', 'l', 'f', null, str_replace('/', DIRECTORY_SEPARATOR, 'a/t/f')),
            'with module' => array('a', 't', 'l', 'f', 'm', str_replace('/', DIRECTORY_SEPARATOR, 'a/t/m/f')),
        );
    }

    /**
     * @param Mage_Core_Model_Theme $themeModel
     * @dataProvider getViewFileUrlProductionModeDataProvider
     */
    public function testGetViewFileUrlProductionMode($themeModel)
    {
        $dirs = $this->getMock('Mage_Core_Model_Dir', array(), array(), '', false);
        $moduleReader = $this->getMock('Mage_Core_Model_Config_Modules_Reader', array(), array(), '', false);

        $filesystem = $this->getMock('Magento_Filesystem', array(), array(), '', false);
        $filesystem->expects($this->never())
            ->method('isFile');
        $filesystem->expects($this->never())
            ->method('isDirectory');
        $filesystem->expects($this->never())
            ->method('read');
        $filesystem->expects($this->never())
            ->method('write');
        $filesystem->expects($this->never())
            ->method('copy');

        $resolutionPool = $this->getMock('Mage_Core_Model_Design_FileResolution_StrategyPool', array(), array(), '',
            false);
        $appState = new Mage_Core_Model_App_State(Mage_Core_Model_App_State::MODE_PRODUCTION);
        $storeManager = $this->getMock('Mage_Core_Model_StoreManagerInterface');
        $cssHelper = $this->getMock('Mage_Core_Helper_Css', array(), array(), '', false);

        // Create model to be tested
        $expected = 'http://example.com/public_dir/a/t/m/file.js';
        $model = $this->getMock('Mage_Core_Model_View_Design', array('getPublicDir', 'getPublicFileUrl'),
            array($dirs, $moduleReader, $filesystem, $resolutionPool, $appState, $storeManager, $cssHelper));
        $model->expects($this->once())
            ->method('getPublicDir')
            ->will($this->returnValue('public_dir'));
        $model->expects($this->once())
            ->method('getPublicFileUrl')
            ->with(str_replace('/', DIRECTORY_SEPARATOR, 'public_dir/a/t/m/file.js'))
            ->will($this->returnValue($expected));

        // Test
        $actual = $model->getViewFileUrl('file.js', array('area' => 'a', 'themeModel' => $themeModel, 'locale' => 'l',
            'module' => 'm'));
        $this->assertEquals($expected, $actual);
    }

    /**
     * @return array
     */
    public static function getViewFileUrlProductionModeDataProvider()
    {
        $usualTheme = PHPUnit_Framework_MockObject_Generator::getMock(
            'Mage_Core_Model_Theme',
            array(),
            array(),
            '',
            false,
            false
        );
        $virtualTheme = clone $usualTheme;
        $parentOfVirtualTheme = clone $usualTheme;

        $usualTheme->expects(new PHPUnit_Framework_MockObject_Matcher_InvokedCount(1))
            ->method('getThemePath')
            ->will(new PHPUnit_Framework_MockObject_Stub_Return('t'));

        $parentOfVirtualTheme->expects(new PHPUnit_Framework_MockObject_Matcher_InvokedCount(1))
            ->method('getThemePath')
            ->will(new PHPUnit_Framework_MockObject_Stub_Return('t'));

        $virtualTheme->expects(new PHPUnit_Framework_MockObject_Matcher_InvokedCount(1))
            ->method('getParentTheme')
            ->will(new PHPUnit_Framework_MockObject_Stub_Return($parentOfVirtualTheme));

        return array(
            'usual theme' => array(
                $usualTheme
            ),
            'virtual theme' => array(
                $virtualTheme
            ),
        );
    }
}
