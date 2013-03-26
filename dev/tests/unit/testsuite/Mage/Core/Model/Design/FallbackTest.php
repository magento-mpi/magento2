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

/**
 * Test that Design Package delegates fallback resolution to a Fallback model
 */
class Mage_Core_Model_Design_FallbackTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getFileDataProvider
     * @param Mage_Core_Model_Theme|PHPUnit_Framework_MockObject_MockObject $theme
     * @param string $file
     * @param string $targetFile
     * @param string $expectedFileName
     * @cover Mage_Core_Model_Design_Fallback::_fallback()
     */
    public function testGetFile($theme, $file, $targetFile, $expectedFileName)
    {
        $moduleDir = 'module_view_dir';
        $module = 'Mage_Core11';

        $filesystem = $this->_getFileSystemMock($targetFile);
        $objectManager = $this->_getObjectManagerMock();
        $dirs = $this->_getDirsMock();

        $configModel = $this->getMock('Mage_Core_Model_Config', array('getModuleDir'), array(), '', false);

        $configModel->expects($this->any())
            ->method('getModuleDir')
            ->will($this->returnValue($moduleDir));

        $objectManager->expects($this->any())
            ->method('get')
            ->with('Mage_Core_Model_Config')
            ->will($this->returnValue($configModel));

        $data = array(
            'area'       => 'area51',
            'locale'     => 'en_EN',
            'themeModel' => $theme,
        );

        $fallback = new Mage_Core_Model_Design_Fallback($dirs, $objectManager, $filesystem, $data);
        $filename = $fallback->getFile($file, $module);

        $this->assertEquals(str_replace('/', DIRECTORY_SEPARATOR, $expectedFileName), $filename);
    }

    /**
     * @return array
     */
    public function getFileDataProvider()
    {
        $file = 'test.txt';

        $themeFilesPath = '%%%';
        $parentThemeFilesPath = '$$$';

        /** @var $parentTheme Mage_Core_Model_Theme|PHPUnit_Framework_MockObject_MockObject */
        $parentTheme = $this->getMock('Mage_Core_Model_Theme', array('getThemeFilesPath'), array(), '', false);
        $parentTheme->expects($this->any())
            ->method('getThemeFilesPath')
            ->will($this->returnValue($parentThemeFilesPath));

        /** @var $themeSimple Mage_Core_Model_Theme|PHPUnit_Framework_MockObject_MockObject */
        $themeSimple = $this->getMock('Mage_Core_Model_Theme', array('getThemeFilesPath'), array(), '', false);
        $themeSimple->expects($this->any())
            ->method('getThemeFilesPath')
            ->will($this->returnValue($themeFilesPath));

        /** @var $themeInherited Mage_Core_Model_Theme|PHPUnit_Framework_MockObject_MockObject */
        $themeInherited = $this->getMock('Mage_Core_Model_Theme',
            array('getParentTheme', 'getThemeFilesPath'), array(), '', false);
        $themeInherited->expects($this->any())
            ->method('getParentTheme')
            ->will($this->returnValue($parentTheme));
        $themeInherited->expects($this->any())
            ->method('getThemeFilesPath')
            ->will($this->returnValue($themeFilesPath));

        return array(
            0 => array($themeSimple, $file, '%%%/test.txt', '%%%/test.txt'),
            1 => array($themeSimple, $file, '%%%/Mage_Core11/test.txt', '%%%/Mage_Core11/test.txt'),
            2 => array($themeSimple, $file, 'module_view_dir/area51/test.txt', 'module_view_dir/area51/test.txt'),
            3 => array($themeSimple, $file, null, 'module_view_dir/area51/test.txt'),
            4 => array($themeInherited, $file, '%%%/test.txt', '%%%/test.txt'),
            5 => array($themeInherited, $file, '$$$/test.txt', '$$$/test.txt'),
            6 => array($themeInherited, $file, '%%%/Mage_Core11/test.txt', '%%%/Mage_Core11/test.txt'),
            7 => array($themeInherited, $file, '$$$/Mage_Core11/test.txt', '$$$/Mage_Core11/test.txt'),
            8 => array($themeInherited, $file, 'module_view_dir/area51/test.txt', 'module_view_dir/area51/test.txt'),
            9 => array($themeInherited, $file, null, 'module_view_dir/area51/test.txt'),
        );
    }

    /**
     * @dataProvider getLocaleFileDataProvider
     * @param Mage_Core_Model_Theme|PHPUnit_Framework_MockObject_MockObject $theme
     * @param string $file
     * @param string $targetFile
     * @param string $expectedFileName
     * @cover Mage_Core_Model_Design_Fallback::_fallback()
     */
    public function testGetLocaleFile($theme, $file, $targetFile, $expectedFileName)
    {
        $filesystem = $this->_getFileSystemMock($targetFile);
        $objectManager = $this->_getObjectManagerMock();
        $dirs = $this->_getDirsMock();

        $data = array(
            'area'       => 'area51',
            'locale'     => 'en_EN',
            'themeModel' => $theme,
        );

        $fallback = new Mage_Core_Model_Design_Fallback($dirs, $objectManager, $filesystem, $data);
        $filename = $fallback->getLocaleFile($file);

        $this->assertEquals(str_replace('/', DIRECTORY_SEPARATOR, $expectedFileName), $filename);
    }

    /**
     * @return array
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function getLocaleFileDataProvider()
    {
        $file = 'test.txt';

        $themeFilesPath = '%%%';
        $parentThemeFilesPath = '$$$';
        $parentThemeFP = '###';
        $grandParentThemeFP = '@@@';

        // 0. Parent and grand parent themes
        /** @var $parentTheme Mage_Core_Model_Theme|PHPUnit_Framework_MockObject_MockObject */
        $parentTheme = $this->getMock('Mage_Core_Model_Theme', array('getThemeFilesPath'), array(), '', false);
        $parentTheme->expects($this->any())
            ->method('getThemeFilesPath')
            ->will($this->returnValue($parentThemeFilesPath));

        /** @var $grandParentTheme Mage_Core_Model_Theme|PHPUnit_Framework_MockObject_MockObject */
        $grandParentTheme = $this->getMock('Mage_Core_Model_Theme', array(
            'getThemePath', 'getThemeFilesPath'
        ), array(), '', false);
        $grandParentTheme->expects($this->any())
            ->method('getThemeFilesPath')
            ->will($this->returnValue($grandParentThemeFP));

        /** @var $parentThemeInherited Mage_Core_Model_Theme|PHPUnit_Framework_MockObject_MockObject */
        $parentThemeInherited = $this->getMock('Mage_Core_Model_Theme',
            array('getParentTheme', 'getThemeFilesPath'), array(), '', false);
        $parentThemeInherited->expects($this->any())->method('getParentTheme')
            ->will($this->returnValue($grandParentTheme));
        $parentThemeInherited->expects($this->any())->method('getThemeFilesPath')
            ->will($this->returnValue($parentThemeFP));

        // 1.
        /** @var $themeSimple Mage_Core_Model_Theme|PHPUnit_Framework_MockObject_MockObject */
        $themeSimple = $this->getMock('Mage_Core_Model_Theme', array('getThemeFilesPath'), array(), '', false);
        $themeSimple->expects($this->any())->method('getThemeFilesPath')->will($this->returnValue($themeFilesPath));

        // 2.
        /** @var $themeInherited Mage_Core_Model_Theme|PHPUnit_Framework_MockObject_MockObject */
        $themeInherited = $this->getMock('Mage_Core_Model_Theme', array(
            'getParentTheme', 'getThemeFilesPath'
        ), array(), '', false);
        $themeInherited->expects($this->any())->method('getParentTheme')->will($this->returnValue($parentTheme));
        $themeInherited->expects($this->any())->method('getThemeFilesPath')->will($this->returnValue($themeFilesPath));

        // 3.
        /** @var $themeInheritedTwice Mage_Core_Model_Theme|PHPUnit_Framework_MockObject_MockObject */
        $themeInheritedTwice = $this->getMock('Mage_Core_Model_Theme', array(
            'getParentTheme', 'getThemeFilesPath'
        ), array(), '', false);
        $themeInheritedTwice->expects($this->any())
            ->method('getParentTheme')
            ->will($this->returnValue($parentThemeInherited));
        $themeInheritedTwice->expects($this->any())
            ->method('getThemeFilesPath')
            ->will($this->returnValue($themeFilesPath));

        return array(
            0 => array($themeSimple, $file, null, '%%%/locale/en_EN/test.txt'),
            1 => array($themeSimple, $file, '%%%/locale/en_EN/test.txt', '%%%/locale/en_EN/test.txt'),
            2 => array($themeInherited, $file, '%%%/locale/en_EN/test.txt', '%%%/locale/en_EN/test.txt'),
            3 => array($themeInherited, $file, '$$$/locale/en_EN/test.txt', '$$$/locale/en_EN/test.txt'),
            4 => array($themeInherited, $file, null, '$$$/locale/en_EN/test.txt'),
            5 => array($themeInheritedTwice, $file, '%%%/locale/en_EN/test.txt', '%%%/locale/en_EN/test.txt'),
            6 => array($themeInheritedTwice, $file, '###/locale/en_EN/test.txt', '###/locale/en_EN/test.txt'),
            7 => array($themeInheritedTwice, $file, '@@@/locale/en_EN/test.txt', '@@@/locale/en_EN/test.txt'),
            8 => array($themeInheritedTwice, $file, null, '@@@/locale/en_EN/test.txt'),
        );
    }

    /**
     * @dataProvider getViewFileDataProvider
     * @param Mage_Core_Model_Theme|PHPUnit_Framework_MockObject_MockObject $theme
     * @param string $file
     * @param string $targetFile
     * @param string $expectedFileName
     * @cover Mage_Core_Model_Design_Fallback::_fallback()
     */
    public function testGetViewFile($theme, $file, $targetFile, $expectedFileName)
    {
        $moduleDir = 'module_view_dir';
        $jsDir = 'js_dir';
        $module = 'Mage_Core11';

        $filesystem = $this->_getFileSystemMock($targetFile);
        $objectManager = $this->_getObjectManagerMock();
        $dirs = $this->_getDirsMock();

        $configModel = $this->getMock('Mage_Core_Model_Config', array('getModuleDir'), array(), '', false);

        $configModel->expects($this->any())
            ->method('getModuleDir')
            ->with($this->equalTo('view'), $this->equalTo($module))
            ->will($this->returnValue($moduleDir));

        $objectManager->expects($this->any())
            ->method('get')
            ->with('Mage_Core_Model_Config')
            ->will($this->returnValue($configModel));

        $dirs->expects($this->at(0))
            ->method('getDir')
            ->with(Mage_Core_Model_Dir::PUB_LIB)
            ->will($this->returnValue($jsDir));

        $data = array(
            'area'       => 'area51',
            'locale'     => 'en_EN',
            'themeModel' => $theme,
        );

        $fallback = new Mage_Core_Model_Design_Fallback($dirs, $objectManager, $filesystem, $data);
        $filename = $fallback->getViewFile($file, $module);

        $this->assertEquals(str_replace('/', DIRECTORY_SEPARATOR, $expectedFileName), $filename);
    }

    /**
     * @return array
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function getViewFileDataProvider()
    {
        $file = 'test.txt';

        $themeFilesPath = '%%%';
        $parentThemeFilesPath = '$$$';
        $parentThemeFP = '###';
        $grandParentThemeFP = '@@@';


        // 0. Parent and grand parent themes
        /** @var $parentTheme Mage_Core_Model_Theme */
        $parentTheme = $this->getMock('Mage_Core_Model_Theme', array('getThemeFilesPath'), array(), '', false);
        $parentTheme->expects($this->any())
            ->method('getThemeFilesPath')
            ->will($this->returnValue($parentThemeFilesPath));

        /** @var $grandParentTheme Mage_Core_Model_Theme */
        $grandParentTheme = $this->getMock('Mage_Core_Model_Theme', array('getThemeFilesPath'), array(), '', false);
        $grandParentTheme->expects($this->any())
            ->method('getThemeFilesPath')
            ->will($this->returnValue($grandParentThemeFP));

        /** @var $parentThemeInherited Mage_Core_Model_Theme */
        $parentThemeInherited = $this->getMock('Mage_Core_Model_Theme',
            array('getThemeFilesPath', 'getParentTheme'), array(), '', false);
        $parentThemeInherited->expects($this->any())
            ->method('getThemeFilesPath')
            ->will($this->returnValue($parentThemeFP));
        $parentThemeInherited->expects($this->any())
            ->method('getParentTheme')
            ->will($this->returnValue($grandParentTheme));

        // 1.
        /** @var $themeSimple Mage_Core_Model_Theme */
        $themeSimple = $this->getMock('Mage_Core_Model_Theme', array('getThemeFilesPath'), array(), '', false);
        $themeSimple->expects($this->any())->method('getThemeFilesPath')->will($this->returnValue($themeFilesPath));

        // 2.
        // 3.

        // 4.
        /** @var $themeInherited Mage_Core_Model_Theme */
        $themeInherited = $this->getMock('Mage_Core_Model_Theme', array(
            'getParentTheme', 'getThemeFilesPath'
        ), array(), '', false);
        $themeInherited->expects($this->any())
            ->method('getParentTheme')
            ->will($this->returnValue($parentTheme));
        $themeInherited->expects($this->any())->method('getThemeFilesPath')->will($this->returnValue($themeFilesPath));

        // 5.

        // 6.
        /** @var $themeInheritedTwice Mage_Core_Model_Theme */
        $themeInheritedTwice = $this->getMock('Mage_Core_Model_Theme', array(
            'getParentTheme', 'getThemeFilesPath'
        ), array(), '', false);
        $themeInheritedTwice->expects($this->any())
            ->method('getParentTheme')
            ->will($this->returnValue($parentThemeInherited));
        $themeInheritedTwice->expects($this->any())
            ->method('getThemeFilesPath')
            ->will($this->returnValue($themeFilesPath));

        return array(
            0 => array($themeSimple, $file, '%%%/locale/en_EN/test.txt', '%%%/locale/en_EN/test.txt'),
            1 => array($themeSimple, $file, '%%%/test.txt', '%%%/test.txt'),
            2 => array($themeSimple, $file,
                '%%%/locale/en_EN/Mage_Core11/test.txt',
                '%%%/locale/en_EN/Mage_Core11/test.txt'
            ),
            3 => array($themeSimple, $file, '%%%/Mage_Core11/test.txt', '%%%/Mage_Core11/test.txt'),
            4 => array($themeSimple, $file,
                'module_view_dir/area51/locale/en_EN/test.txt',
                'module_view_dir/area51/locale/en_EN/test.txt'
            ),
            5 => array($themeSimple, $file, 'module_view_dir/area51/test.txt', 'module_view_dir/area51/test.txt'),
            6 => array($themeSimple, $file, 'js_dir/test.txt', 'js_dir/test.txt'),
            7 => array($themeSimple, $file, null, 'js_dir/test.txt'),

            8 => array($themeInherited, $file, '%%%/locale/en_EN/test.txt', '%%%/locale/en_EN/test.txt'),
            9 => array($themeInherited, $file, '%%%/test.txt', '%%%/test.txt'),
            10 => array($themeInherited, $file, '$$$/locale/en_EN/test.txt', '$$$/locale/en_EN/test.txt'),
            11 => array($themeInherited, $file, '$$$/test.txt', '$$$/test.txt'),
            12 => array($themeInherited, $file,
                '%%%/locale/en_EN/Mage_Core11/test.txt',
                '%%%/locale/en_EN/Mage_Core11/test.txt'
            ),
            13 => array($themeInherited, $file, '%%%/Mage_Core11/test.txt', '%%%/Mage_Core11/test.txt'),
            14 => array($themeInherited, $file,
                '$$$/locale/en_EN/Mage_Core11/test.txt',
                '$$$/locale/en_EN/Mage_Core11/test.txt'
            ),
            15 => array($themeInherited, $file, '$$$/Mage_Core11/test.txt', '$$$/Mage_Core11/test.txt'),
            16 => array($themeInherited, $file,
                'module_view_dir/area51/locale/en_EN/test.txt',
                'module_view_dir/area51/locale/en_EN/test.txt'
            ),
            17 => array($themeInherited, $file, 'module_view_dir/area51/test.txt', 'module_view_dir/area51/test.txt'),
            18 => array($themeInherited, $file, 'js_dir/test.txt', 'js_dir/test.txt'),
            19 => array($themeInherited, $file, null, 'js_dir/test.txt'),

            //////////////
            20 => array($themeInheritedTwice, $file, '%%%/locale/en_EN/test.txt', '%%%/locale/en_EN/test.txt'),
            21 => array($themeInheritedTwice, $file, '%%%/test.txt', '%%%/test.txt'),
            22 => array($themeInheritedTwice, $file, '###/locale/en_EN/test.txt', '###/locale/en_EN/test.txt'),
            23 => array($themeInheritedTwice, $file, '###/test.txt', '###/test.txt'),
            24 => array($themeInheritedTwice, $file, '@@@/locale/en_EN/test.txt', '@@@/locale/en_EN/test.txt'),
            25 => array($themeInheritedTwice, $file, '@@@/test.txt', '@@@/test.txt'),
            26 => array($themeInheritedTwice, $file,
                '%%%/locale/en_EN/Mage_Core11/test.txt',
                '%%%/locale/en_EN/Mage_Core11/test.txt'
            ),
            27 => array($themeInheritedTwice, $file, '%%%/Mage_Core11/test.txt', '%%%/Mage_Core11/test.txt'),
            28 => array($themeInheritedTwice, $file,
                '###/locale/en_EN/Mage_Core11/test.txt',
                '###/locale/en_EN/Mage_Core11/test.txt'
            ),
            29 => array($themeInheritedTwice, $file, '###/Mage_Core11/test.txt', '###/Mage_Core11/test.txt'),
            30 => array($themeInheritedTwice, $file,
                '@@@/locale/en_EN/Mage_Core11/test.txt',
                '@@@/locale/en_EN/Mage_Core11/test.txt'
            ),
            31 => array($themeInheritedTwice, $file, '@@@/Mage_Core11/test.txt', '@@@/Mage_Core11/test.txt'),
            32 => array($themeInheritedTwice, $file,
                'module_view_dir/area51/locale/en_EN/test.txt',
                'module_view_dir/area51/locale/en_EN/test.txt'
            ),
            33 => array($themeInheritedTwice, $file,
                'module_view_dir/area51/test.txt',
                'module_view_dir/area51/test.txt'
            ),
            34 => array($themeInheritedTwice, $file, 'js_dir/test.txt', 'js_dir/test.txt'),
            35 => array($themeInheritedTwice, $file, null, 'js_dir/test.txt')

        );
    }

    /**
     * @param string $targetFile
     * @return Magento_Filesystem|PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getFileSystemMock($targetFile)
    {
        $targetFile = str_replace('/', DIRECTORY_SEPARATOR, $targetFile);
            /** @var $filesystem Magento_Filesystem */
        $filesystem = $this->getMock('Magento_Filesystem', array('has'), array(), '', false);
        $filesystem->expects($this->any())
            ->method('has')
            ->will($this->returnCallback(
                function ($tryFile) use ($targetFile) {
                    return ($tryFile == $targetFile);
                }
        ));

        return $filesystem;
    }

    /**
     * @return Magento_ObjectManager|PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getObjectManagerMock()
    {
        /** @var $objectManager Magento_ObjectManager */
        $objectManager = $this->getMock('Magento_ObjectManager');
        return $objectManager;
    }

    /**
     * @return Mage_Core_Model_Dir|PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getDirsMock()
    {
        /** @var $dirs Mage_Core_Model_Dir */
        $dirs = $this->getMock('Mage_Core_Model_Dir', array('getDir'), array(), '', false);
        return $dirs;
    }
}
