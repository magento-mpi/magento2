<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Model_View_UrlTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param Magento_Core_Model_Theme $themeModel
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
        /** @var $dirs Magento_Core_Model_Dir|PHPUnit_Framework_MockObject_MockObject */
        $dirs = $this->getMock('Magento_Core_Model_Dir', array(), array(), '', false);
        $dirs->expects($this->any())
            ->method('getDir')
            ->will($this->returnValue('some_dir'));

        // 3. Get store model
        $store = $this->getMock('Magento_Core_Model_Store', array(), array(), '', false);
        $store->expects($this->any())
            ->method('getBaseUrl')
            ->will($this->returnValue('http://example.com/'));
        $store->expects($this->any())
            ->method('getConfig')
            ->will($this->returnValue($isSigned));

        // 4. Get store manager
        /** @var $storeManager Magento_Core_Model_StoreManager|PHPUnit_Framework_MockObject_MockObject */
        $storeManager = $this->getMock('Magento_Core_Model_StoreManager', array(), array(), '', false);
        $storeManager->expects($this->any())
            ->method('getStore')
            ->will($this->returnValue($store));

        // 5. Get viewService model
        /** @var $viewService Magento_Core_Model_View_Service|PHPUnit_Framework_MockObject_MockObject */
        $viewService = $this->getMock('Magento_Core_Model_View_Service',
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
        /** @var $publisher Magento_Core_Model_View_Publisher|PHPUnit_Framework_MockObject_MockObject */
        $publisher = $this->getMock('Magento_Core_Model_View_Publisher', array(), array(), '', false);
        $publisher->expects($this->any())
            ->method('getPublicFilePath')
            ->will($this->returnValue(str_replace('/', DIRECTORY_SEPARATOR, 'some_dir/public_dir/a/t/m/file.js')));

        // 7. Get deployed file manager
        /** @var $dFManager Magento_Core_Model_View_DeployedFilesManager|PHPUnit_Framework_MockObject_MockObject */
        $dFManager = $this->getMock('Magento_Core_Model_View_DeployedFilesManager', array(), array(), '',
            false
        );

        // Create model to be tested
        /** @var $model Magento_Core_Model_View_Url|PHPUnit_Framework_MockObject_MockObject */
        $model = new Magento_Core_Model_View_Url(
            $filesystem, $dirs, $storeManager, $viewService, $publisher, $dFManager
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
            'Magento_Core_Model_Theme',
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
