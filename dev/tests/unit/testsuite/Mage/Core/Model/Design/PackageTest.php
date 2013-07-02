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

//@TODO Move test suite cause no more model Mage_Core_Model_Design_Package and it seems this test suite tests class
// Mage_Core_Model_View_Url
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
        $isProductionMode = true;
        $isSigned = false;      //NOTE: If going to test with signature enabled mock Magento_Filesystem::getMTime()
        $expected = 'http://example.com/public_dir/a/t/m/file.js';

        // 1. Get fileSystem model
        /** @var $filesystem Magento_Filesystem|PHPUnit_Framework_MockObject_MockObject */
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

        // 2. Get directories configuration
        /** @var $dirs Mage_Core_Model_Dir|PHPUnit_Framework_MockObject_MockObject */
        $dirs = $this->getMock('Mage_Core_Model_Dir', array(), array(), '', false);
        $dirs->expects($this->any())
            ->method('getDir')
            ->will($this->returnValue('some_dir'));

        // 3. Get store model
        $store = $this->getMock('Mage_Core_Model_Store', array(), array(), '', false);
        $store->expects($this->any())
            ->method('getBaseUrl')
            ->will($this->returnValue('http://example.com/'));
        $store->expects($this->any())
            ->method('getConfig')
            ->will($this->returnValue($isSigned));

        // 4. Get store manager
        /** @var $storeManager Mage_Core_Model_StoreManager|PHPUnit_Framework_MockObject_MockObject */
        $storeManager = $this->getMock('Mage_Core_Model_StoreManager', array(), array(), '', false);
        $storeManager->expects($this->any())
            ->method('getStore')
            ->will($this->returnValue($store));

        // 5. Get viewService model
        /** @var $viewService Mage_Core_Model_View_Service|PHPUnit_Framework_MockObject_MockObject */
        $viewService = $this->getMock('Mage_Core_Model_View_Service',
            array('updateDesignParams', 'extractScope', 'isViewFileOperationAllowed'), array(), '', false
        );
        $viewService->expects($this->any())
            ->method('extractScope')
            ->will($this->returnArgument(0));
        $viewService->expects($this->any())
            ->method('isViewFileOperationAllowed')
            ->will($this->returnValue($isProductionMode));
        $viewService->expects($this->any())
            ->method('updateDesignParams');

        // 6. Get publisher model
        /** @var $publisher Mage_Core_Model_View_Publisher|PHPUnit_Framework_MockObject_MockObject */
        $publisher = $this->getMock('Mage_Core_Model_View_Publisher', array(), array(), '', false);
        $publisher->expects($this->any())
            ->method('getPublishedFilePath')
            ->will($this->returnValue(str_replace('/', DIRECTORY_SEPARATOR, 'some_dir/public_dir/a/t/m/file.js')));

        // 7. Get deployed file manager
        /** @var $deployedFilesManager Mage_Core_Model_View_DeployedFilesManager|PHPUnit_Framework_MockObject_MockObject */
        $deployedFilesManager = $this->getMock('Mage_Core_Model_View_DeployedFilesManager', array(), array(), '',
            false
        );

        // Create model to be tested
        /** @var $model Mage_Core_Model_View_Url|PHPUnit_Framework_MockObject_MockObject */
        $model = new Mage_Core_Model_View_Url(
            $filesystem, $dirs, $storeManager, $viewService, $publisher, $deployedFilesManager
        );

        // Test
        $actual = $model->getViewFileUrl('file.js', array(
            'area'       => 'a',
            'themeModel' => $themeModel,
            'locale'     => 'l',
            'module'     => 'm'
        ));
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
