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
class Mage_Core_Model_Design_FileResolution_Strategy_FallbackTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getFileDataProvider
     * @param Mage_Core_Model_Theme|PHPUnit_Framework_MockObject_MockObject $theme
     * @param string $file
     * @param string $targetFile
     * @param string $expectedFileName
     * @cover Mage_Core_Model_Design_FileResolution_Strategy_Fallback::_fallback()
     */
    public function testGetFile($theme, $file, $targetFile, $expectedFileName)
    {
        $designDir = 'design_dir';
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

        $dirs->expects($this->any())
            ->method('getDir')
            ->with(Mage_Core_Model_Dir::THEMES)
            ->will($this->returnValue($designDir));

        $fallback = new Mage_Core_Model_Design_FileResolution_Strategy_Fallback($objectManager, $filesystem, $dirs);
        $filename = $fallback->getFile('area51', $theme, $file, $module);

        $this->assertEquals(str_replace('/', DIRECTORY_SEPARATOR, $expectedFileName), $filename);
    }

    /**
     * @return array
     */
    public function getFileDataProvider()
    {
        $file = 'test.txt';
        $customizationPath = 'custom';
        $themePath = 'theme_path';
        $parentThemePath = 'parent_theme_path';

        /** @var $parentTheme Mage_Core_Model_Theme */
        $parentTheme = $this->getMock('Mage_Core_Model_Theme', array('getThemePath'), array(), '', false);
        $parentTheme->expects($this->any())
            ->method('getThemePath')
            ->will($this->returnValue($parentThemePath));

        /** @var $themeSimple Mage_Core_Model_Theme */
        $themeSimple = $this->getMock('Mage_Core_Model_Theme', null, array(), '', false);

        /** @var $themeCustomized Mage_Core_Model_Theme */
        $themeCustomized = $this->getMock('Mage_Core_Model_Theme', array('getCustomizationPath'), array(), '', false);
        $themeCustomized->expects($this->any())
            ->method('getCustomizationPath')
            ->will($this->returnValue($customizationPath));

        /** @var $customizedPhysical Mage_Core_Model_Theme */
        $customizedPhysical = $this->getMock('Mage_Core_Model_Theme',
            array('getCustomizationPath', 'getThemePath'), array(), '', false);
        $customizedPhysical->expects($this->any())
            ->method('getCustomizationPath')
            ->will($this->returnValue($customizationPath));
        $customizedPhysical->expects($this->any())
            ->method('getThemePath')
            ->will($this->returnValue($themePath));

        /** @var $themeInherited Mage_Core_Model_Theme */
        $themeInherited = $this->getMock('Mage_Core_Model_Theme', array('getParentTheme'), array(), '', false);
        $themeInherited->expects($this->any())
            ->method('getParentTheme')
            ->will($this->returnValue($parentTheme));

        /** @var $themeComplicated Mage_Core_Model_Theme */
        $themeComplicated = $this->getMock('Mage_Core_Model_Theme',
            array('getCustomizationPath', 'getThemePath', 'getParentTheme'), array(), '', false);
        $themeComplicated->expects($this->any())
            ->method('getCustomizationPath')
            ->will($this->returnValue($customizationPath));
        $themeComplicated->expects($this->any())
            ->method('getThemePath')
            ->will($this->returnValue($themePath));
        $themeComplicated->expects($this->any())
            ->method('getParentTheme')
            ->will($this->returnValue($parentTheme));

        return array(
            'no theme' => array(
                $themeSimple, $file, 'module_view_dir/area51/test.txt', 'module_view_dir/area51/test.txt'
            ),
            'no theme and non-existent module file' => array(
                $themeSimple, $file, null, 'module_view_dir/area51/test.txt'
            ),
            'theme with non-existent file' => array($themeCustomized, $file, null, 'module_view_dir/area51/test.txt'),
            'theme file exists' => array(
                $themeCustomized, $file, 'module_view_dir/area51/test.txt', 'module_view_dir/area51/test.txt'
            ),
            'custom theme' => array($customizedPhysical, $file, null, 'module_view_dir/area51/test.txt'),
            'theme inherited' => array($themeInherited, $file, 'design_dir/area51/parent_theme_path/test.txt',
                'design_dir/area51/parent_theme_path/test.txt'
            ),
            'theme inherited with module file in the theme' => array(
                $themeInherited, $file, 'design_dir/area51/parent_theme_path/Mage_Core11/test.txt',
                'design_dir/area51/parent_theme_path/Mage_Core11/test.txt'
            ),
            'theme inherited, file not found in theme' => array(
                $themeInherited, $file, 'module_view_dir/area51/test.txt', 'module_view_dir/area51/test.txt'
            ),
            'theme inherited with non-existent file' => array(
                $themeInherited, $file, null, 'module_view_dir/area51/test.txt'
            ),
            'custom inherited theme with custom file' => array(
                $themeComplicated, $file, null, 'module_view_dir/area51/test.txt'
            ),
            'custom inherited theme with theme file' => array(
                $themeComplicated, $file, 'design_dir/area51/theme_path/test.txt',
                'design_dir/area51/theme_path/test.txt'
            ),
            'custom inherited theme with parent theme file' => array(
                $themeComplicated, $file, 'design_dir/area51/parent_theme_path/test.txt',
                'design_dir/area51/parent_theme_path/test.txt'
            ),
            'custom inherited theme with module file in theme' => array(
                $themeComplicated, $file, 'design_dir/area51/theme_path/Mage_Core11/test.txt',
                'design_dir/area51/theme_path/Mage_Core11/test.txt'
            ),
            'custom inherited theme with module file in parent theme' => array(
                $themeComplicated, $file, 'design_dir/area51/parent_theme_path/Mage_Core11/test.txt',
                'design_dir/area51/parent_theme_path/Mage_Core11/test.txt'
            ),
            'custom inherited theme with file existing in module' => array(
                $themeComplicated, $file, 'module_view_dir/area51/test.txt', 'module_view_dir/area51/test.txt'
            ),
            'custom inherited theme with non-existent file' => array(
                $themeComplicated, $file, null, 'module_view_dir/area51/test.txt'
            ),
        );
    }

    /**
     * @dataProvider getLocaleFileDataProvider
     * @param Mage_Core_Model_Theme|PHPUnit_Framework_MockObject_MockObject $theme
     * @param string $file
     * @param string $targetFile
     * @param string $expectedFileName
     * @cover Mage_Core_Model_File_Resolution_Fallback::_fallback()
     */
    public function testGetLocaleFile($theme, $file, $targetFile, $expectedFileName)
    {
        $designDir = 'design_dir';

        $filesystem = $this->_getFileSystemMock($targetFile);
        $objectManager = $this->_getObjectManagerMock();
        $dirs = $this->_getDirsMock();

        $dirs->expects($this->any())
            ->method('getDir')
            ->with(Mage_Core_Model_Dir::THEMES)
            ->will($this->returnValue($designDir));

        $fallback = new Mage_Core_Model_Design_FileResolution_Strategy_Fallback($objectManager, $filesystem, $dirs);
        $filename = $fallback->getLocaleFile('area51', $theme, 'en_EN', $file);

        $this->assertEquals(str_replace('/', DIRECTORY_SEPARATOR, $expectedFileName), $filename);
    }

    /**
     * @return array
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function getLocaleFileDataProvider()
    {
        $customizationPath = 'custom';
        $themePath = 'theme_path';
        $parentThemePath = 'parent_theme_path';
        $grandParentPath = 'grand_parent_theme_path';
        $file = 'test.txt';

        // 0. Parent and grand parent themes
        /** @var $parentTheme Mage_Core_Model_Theme */
        $parentTheme = $this->getMock('Mage_Core_Model_Theme', array('getThemePath'), array(), '', false);
        $parentTheme->expects($this->any())->method('getThemePath')->will($this->returnValue($parentThemePath));

        /** @var $grandParentTheme Mage_Core_Model_Theme */
        $grandParentTheme = $this->getMock('Mage_Core_Model_Theme', array('getThemePath'), array(), '', false);
        $grandParentTheme->expects($this->any())->method('getThemePath')->will($this->returnValue($grandParentPath));

        /** @var $parentThemeInherited Mage_Core_Model_Theme */
        $parentThemeInherited = $this->getMock('Mage_Core_Model_Theme',
            array('getThemePath', 'getParentTheme'), array(), '', false);
        $parentThemeInherited->expects($this->any())->method('getThemePath')
            ->will($this->returnValue($parentThemePath));
        $parentThemeInherited->expects($this->any())->method('getParentTheme')
            ->will($this->returnValue($grandParentTheme));

        // 1.
        /** @var $themeSimple Mage_Core_Model_Theme */
        $themeSimple = $this->getMock('Mage_Core_Model_Theme', null, array(), '', false);

        // 2.
        /** @var $themeCustomized Mage_Core_Model_Theme */
        $themeCustomized = $this->getMock('Mage_Core_Model_Theme', array('getCustomizationPath'), array(), '', false);
        $themeCustomized->expects($this->any())->method('getCustomizationPath')
            ->will($this->returnValue($customizationPath));

        // 3.
        /** @var $customizedPhysical Mage_Core_Model_Theme */
        $customizedPhysical = $this->getMock('Mage_Core_Model_Theme',
            array('getCustomizationPath', 'getThemePath'), array(), '', false);
        $customizedPhysical->expects($this->any())
            ->method('getCustomizationPath')
            ->will($this->returnValue($customizationPath));
        $customizedPhysical->expects($this->any())
            ->method('getThemePath')
            ->will($this->returnValue($themePath));

        // 4.
        /** @var $themeInherited Mage_Core_Model_Theme */
        $themeInherited = $this->getMock('Mage_Core_Model_Theme', array('getParentTheme'), array(), '', false);
        $themeInherited->expects($this->any())->method('getParentTheme')->will($this->returnValue($parentTheme));

        // 5.
        /** @var $themeComplicated Mage_Core_Model_Theme */
        $themeComplicated = $this->getMock('Mage_Core_Model_Theme',
            array('getCustomizationPath', 'getThemePath', 'getParentTheme'), array(), '', false);
        $themeComplicated->expects($this->any())
            ->method('getCustomizationPath')
            ->will($this->returnValue($customizationPath));
        $themeComplicated->expects($this->any())
            ->method('getThemePath')
            ->will($this->returnValue($themePath));
        $themeComplicated->expects($this->any())
            ->method('getParentTheme')
            ->will($this->returnValue($parentTheme));

        // 6.
        /** @var $themeInheritedTwice Mage_Core_Model_Theme */
        $themeInheritedTwice = $this->getMock('Mage_Core_Model_Theme', array('getParentTheme'), array(), '', false);
        $themeInheritedTwice->expects($this->any())
            ->method('getParentTheme')
            ->will($this->returnValue($parentThemeInherited));

        return array(
            'no theme' => array($themeSimple, $file, null, ''),
            'custom virtual theme' => array($themeCustomized, $file, null, ''),
            'custom physical theme, no file found' => array($customizedPhysical, $file, null,
                'design_dir/area51/theme_path/locale/en_EN/test.txt'),
            'inherited theme' => array($themeInherited, $file,
                'design_dir/area51/parent_theme_path/locale/en_EN/test.txt',
                'design_dir/area51/parent_theme_path/locale/en_EN/test.txt'
            ),
            'inherited theme, no file found' => array(
                $themeInherited, $file, null, 'design_dir/area51/parent_theme_path/locale/en_EN/test.txt'
            ),
            'custom inherited theme with theme file' => array($themeComplicated, $file,
                'design_dir/area51/theme_path/locale/en_EN/test.txt',
                'design_dir/area51/theme_path/locale/en_EN/test.txt'
            ),
            'custom inherited theme with file in parent theme' => array($themeComplicated, $file,
                'design_dir/area51/parent_theme_path/locale/en_EN/test.txt',
                'design_dir/area51/parent_theme_path/locale/en_EN/test.txt'
            ),
            'custom inherited theme, no file found' => array($themeComplicated, $file, null,
                'design_dir/area51/parent_theme_path/locale/en_EN/test.txt'),
            'twice inherited theme with file in parent theme' => array($themeInheritedTwice, $file,
                'design_dir/area51/parent_theme_path/locale/en_EN/test.txt',
                'design_dir/area51/parent_theme_path/locale/en_EN/test.txt'
            ),
            'twice inherited theme with file in grandparent theme' => array($themeInheritedTwice, $file,
                'design_dir/area51/grand_parent_theme_path/locale/en_EN/test.txt',
                'design_dir/area51/grand_parent_theme_path/locale/en_EN/test.txt'
            ),
            'twice inherited theme, no file found' => array($themeInheritedTwice, $file, null,
                'design_dir/area51/grand_parent_theme_path/locale/en_EN/test.txt'),
        );
    }

    /**
     * @dataProvider getViewFileDataProvider
     * @param Mage_Core_Model_Theme|PHPUnit_Framework_MockObject_MockObject $theme
     * @param string $file
     * @param string $targetFile
     * @param string $expectedFileName
     * @cover Mage_Core_Model_File_Resolution_Fallback::_fallback()
     */
    public function testGetViewFile($theme, $file, $targetFile, $expectedFileName)
    {
        $designDir = 'design_dir';
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
            ->with(Mage_Core_Model_Dir::THEMES)
            ->will($this->returnValue($designDir));

        $dirs->expects($this->at(1))
            ->method('getDir')
            ->with(Mage_Core_Model_Dir::PUB_LIB)
            ->will($this->returnValue($jsDir));

        $fallback = new Mage_Core_Model_Design_FileResolution_Strategy_Fallback($objectManager, $filesystem, $dirs);
        $filename = $fallback->getViewFile('area51', $theme, 'en_EN', $file, $module);

        $this->assertEquals(str_replace('/', DIRECTORY_SEPARATOR, $expectedFileName), $filename);
    }

    /**
     * @return array
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function getViewFileDataProvider()
    {
        $customizationPath = 'custom';
        $themePath = 'theme_path';
        $parentThemePath = 'parent_theme_path';
        $grandParentThemePath = 'grand_parent_theme_path';
        $file = 'test.txt';

        // 0. Parent and grand parent themes
        /** @var $parentTheme Mage_Core_Model_Theme */
        $parentTheme = $this->getMock('Mage_Core_Model_Theme', array('getThemePath'), array(), '', false);
        $parentTheme->expects($this->any())
            ->method('getThemePath')
            ->will($this->returnValue($parentThemePath));

        /** @var $grandParentTheme Mage_Core_Model_Theme */
        $grandParentTheme = $this->getMock('Mage_Core_Model_Theme', array('getThemePath'), array(), '', false);
        $grandParentTheme->expects($this->any())
            ->method('getThemePath')
            ->will($this->returnValue($grandParentThemePath));

        /** @var $parentThemeInherited Mage_Core_Model_Theme */
        $parentThemeInherited = $this->getMock('Mage_Core_Model_Theme',
            array('getThemePath', 'getParentTheme'), array(), '', false);
        $parentThemeInherited->expects($this->any())
            ->method('getThemePath')
            ->will($this->returnValue($parentThemePath));
        $parentThemeInherited->expects($this->any())
            ->method('getParentTheme')
            ->will($this->returnValue($grandParentTheme));

        // 1.
        /** @var $themeSimple Mage_Core_Model_Theme */
        $themeSimple = $this->getMock('Mage_Core_Model_Theme', null, array(), '', false);

        // 2.
        /** @var $themeCustomized Mage_Core_Model_Theme */
        $themeCustomized = $this->getMock('Mage_Core_Model_Theme', array('getCustomizationPath'), array(), '', false);
        $themeCustomized->expects($this->any())
            ->method('getCustomizationPath')
            ->will($this->returnValue($customizationPath));

        // 3.
        /** @var $customizedPhysical Mage_Core_Model_Theme */
        $customizedPhysical = $this->getMock('Mage_Core_Model_Theme',
            array('getCustomizationPath', 'getThemePath'), array(), '', false);
        $customizedPhysical->expects($this->any())
            ->method('getCustomizationPath')
            ->will($this->returnValue($customizationPath));
        $customizedPhysical->expects($this->any())
            ->method('getThemePath')
            ->will($this->returnValue($themePath));

        // 4.
        /** @var $themeInherited Mage_Core_Model_Theme */
        $themeInherited = $this->getMock('Mage_Core_Model_Theme', array('getParentTheme'), array(), '', false);
        $themeInherited->expects($this->any())
            ->method('getParentTheme')
            ->will($this->returnValue($parentTheme));

        // 5.
        /** @var $themeComplicated Mage_Core_Model_Theme */
        $themeComplicated = $this->getMock('Mage_Core_Model_Theme',
            array('getCustomizationPath', 'getThemePath', 'getParentTheme'), array(), '', false);
        $themeComplicated->expects($this->any())
            ->method('getCustomizationPath')
            ->will($this->returnValue($customizationPath));
        $themeComplicated->expects($this->any())
            ->method('getThemePath')
            ->will($this->returnValue($themePath));
        $themeComplicated->expects($this->any())
            ->method('getParentTheme')
            ->will($this->returnValue($parentTheme));

        // 6.
        /** @var $themeInheritedTwice Mage_Core_Model_Theme */
        $themeInheritedTwice = $this->getMock('Mage_Core_Model_Theme', array('getParentTheme'), array(), '', false);
        $themeInheritedTwice->expects($this->any())
            ->method('getParentTheme')
            ->will($this->returnValue($parentThemeInherited));

        return array(
            'no theme, module localized file exists' => array($themeSimple, $file,
                'module_view_dir/area51/locale/en_EN/test.txt',
                'module_view_dir/area51/locale/en_EN/test.txt'
            ),
            'no theme, module file exists' => array($themeSimple, $file, 'module_view_dir/area51/test.txt',
                'module_view_dir/area51/test.txt'),
            'no theme, file exists in pub lib dir' => array($themeSimple, $file, 'js_dir/test.txt', 'js_dir/test.txt'),
            'no theme, no file found' => array($themeSimple, $file, null, 'js_dir/test.txt'),
            'custom virtual theme' => array($themeCustomized, $file, null, 'js_dir/test.txt'),
            'custom virtual theme, module localized file exists' => array($themeCustomized, $file,
                'module_view_dir/area51/locale/en_EN/test.txt',
                'module_view_dir/area51/locale/en_EN/test.txt'
            ),
            'custom virtual theme, module file exists' => array($themeCustomized, $file,
                'module_view_dir/area51/test.txt', 'module_view_dir/area51/test.txt'),
            'custom virtual theme, file exists in pub lib dir' => array($themeCustomized, $file, 'js_dir/test.txt',
                'js_dir/test.txt'),
            'custom virtual theme, no file found' => array($themeCustomized, $file, null, 'js_dir/test.txt'),
            'custom physical theme, no file found' => array($customizedPhysical, $file, null, 'js_dir/test.txt'),
            'custom physical theme with localized theme file' => array($customizedPhysical, $file,
                'design_dir/area51/theme_path/locale/en_EN/test.txt',
                'design_dir/area51/theme_path/locale/en_EN/test.txt'
            ),
            'custom physical theme with theme file' => array($customizedPhysical, $file,
                'design_dir/area51/theme_path/test.txt', 'design_dir/area51/theme_path/test.txt'),
            'custom physical theme with localized module file in theme' => array($customizedPhysical, $file,
                'design_dir/area51/theme_path/locale/en_EN/Mage_Core11/test.txt',
                'design_dir/area51/theme_path/locale/en_EN/Mage_Core11/test.txt'
            ),
            'custom physical theme with module file in theme' => array($customizedPhysical, $file,
                'design_dir/area51/theme_path/Mage_Core11/test.txt',
                'design_dir/area51/theme_path/Mage_Core11/test.txt'
            ),
            'custom physical theme with localized module file' => array($customizedPhysical, $file,
                'module_view_dir/area51/locale/en_EN/test.txt',
                'module_view_dir/area51/locale/en_EN/test.txt'
            ),
            'custom physical theme with module file' => array($customizedPhysical, $file,
                'module_view_dir/area51/test.txt', 'module_view_dir/area51/test.txt'),
            'custom physical theme with file in pub lib dir' => array($customizedPhysical, $file, 'js_dir/test.txt',
                'js_dir/test.txt'),
            'inherited theme with localized file in parent theme' => array($themeInherited, $file,
                'design_dir/area51/parent_theme_path/locale/en_EN/test.txt',
                'design_dir/area51/parent_theme_path/locale/en_EN/test.txt'
            ),
            'inherited theme with file in parent theme' => array($themeInherited, $file,
                'design_dir/area51/parent_theme_path/test.txt',
                'design_dir/area51/parent_theme_path/test.txt'
            ),
            'inherited theme with localized module file in parent theme' => array($themeInherited, $file,
                'design_dir/area51/parent_theme_path/locale/en_EN/Mage_Core11/test.txt',
                'design_dir/area51/parent_theme_path/locale/en_EN/Mage_Core11/test.txt'
            ),
            'inherited theme' => array($themeInherited, $file,
                'design_dir/area51/parent_theme_path/Mage_Core11/test.txt',
                'design_dir/area51/parent_theme_path/Mage_Core11/test.txt'
            ),
            'inherited theme with localized module file' => array($themeInherited, $file,
                'module_view_dir/area51/locale/en_EN/test.txt',
                'module_view_dir/area51/locale/en_EN/test.txt'
            ),
            'inherited theme with module file' => array($themeInherited, $file, 'module_view_dir/area51/test.txt',
                'module_view_dir/area51/test.txt'),
            'inherited theme with file in pub lib dir' => array($themeInherited, $file, 'js_dir/test.txt',
                'js_dir/test.txt'),
            'inherited theme, no file found' => array($themeInherited, $file, null, 'js_dir/test.txt'),
            'custom inherited theme' => array($themeComplicated, $file,
                'design_dir/area51/theme_path/locale/en_EN/test.txt',
                'design_dir/area51/theme_path/locale/en_EN/test.txt'
            ),
            'custom inherited theme with theme file' => array($themeComplicated, $file,
                'design_dir/area51/theme_path/test.txt', 'design_dir/area51/theme_path/test.txt'),
            'custom inherited theme with localized file in parent theme' => array($themeComplicated, $file,
                'design_dir/area51/parent_theme_path/locale/en_EN/test.txt',
                'design_dir/area51/parent_theme_path/locale/en_EN/test.txt'
            ),
            'custom inherited theme with file in parent theme' => array($themeComplicated, $file,
                'design_dir/area51/parent_theme_path/test.txt',
                'design_dir/area51/parent_theme_path/test.txt'
            ),
            'custom inherited theme with localized module file in theme' => array($themeComplicated, $file,
                'design_dir/area51/theme_path/locale/en_EN/Mage_Core11/test.txt',
                'design_dir/area51/theme_path/locale/en_EN/Mage_Core11/test.txt'
            ),
            'custom inherited theme with module file in theme' => array($themeComplicated, $file,
                'design_dir/area51/theme_path/Mage_Core11/test.txt',
                'design_dir/area51/theme_path/Mage_Core11/test.txt'
            ),
            'custom inherited theme with localized module file in parent theme' => array($themeComplicated, $file,
                'design_dir/area51/parent_theme_path/locale/en_EN/Mage_Core11/test.txt',
                'design_dir/area51/parent_theme_path/locale/en_EN/Mage_Core11/test.txt'
            ),
            'custom inherited theme with module file in parent theme' => array($themeComplicated, $file,
                'design_dir/area51/parent_theme_path/Mage_Core11/test.txt',
                'design_dir/area51/parent_theme_path/Mage_Core11/test.txt'
            ),
            'custom inherited theme with localized module file' => array($themeComplicated, $file,
                'module_view_dir/area51/locale/en_EN/test.txt',
                'module_view_dir/area51/locale/en_EN/test.txt'
            ),
            'custom inherited theme with module file' => array($themeComplicated, $file,
                'module_view_dir/area51/test.txt', 'module_view_dir/area51/test.txt'),
            'custom inherited theme with file in pub lib dir' => array($themeComplicated, $file, 'js_dir/test.txt',
                'js_dir/test.txt'),
            'custom inherited theme, no file found' => array($themeComplicated, $file, null, 'js_dir/test.txt'),
            'theme inherited twice with localized file in parent theme' => array($themeInheritedTwice, $file,
                'design_dir/area51/parent_theme_path/locale/en_EN/test.txt',
                'design_dir/area51/parent_theme_path/locale/en_EN/test.txt'
            ),
            'theme inherited twice with file in parent theme' => array($themeInheritedTwice, $file,
                'design_dir/area51/parent_theme_path/test.txt',
                'design_dir/area51/parent_theme_path/test.txt'
            ),
            'theme inherited twice with localized file in grandparent theme' => array($themeInheritedTwice, $file,
                'design_dir/area51/grand_parent_theme_path/locale/en_EN/test.txt',
                'design_dir/area51/grand_parent_theme_path/locale/en_EN/test.txt'
            ),
            'theme inherited twice with file in grandparent theme' => array($themeInheritedTwice, $file,
                'design_dir/area51/grand_parent_theme_path/test.txt',
                'design_dir/area51/grand_parent_theme_path/test.txt'
            ),
            'theme inherited twice with localized module file in parent theme' => array($themeInheritedTwice, $file,
                'design_dir/area51/parent_theme_path/locale/en_EN/Mage_Core11/test.txt',
                'design_dir/area51/parent_theme_path/locale/en_EN/Mage_Core11/test.txt'
            ),
            'theme inherited twice with module file in pareent theme' => array($themeInheritedTwice, $file,
                'design_dir/area51/parent_theme_path/Mage_Core11/test.txt',
                'design_dir/area51/parent_theme_path/Mage_Core11/test.txt'
            ),
            'theme inherited twice with localized module file in grandparent theme' => array($themeInheritedTwice,
                $file,
                'design_dir/area51/grand_parent_theme_path/locale/en_EN/Mage_Core11/test.txt',
                'design_dir/area51/grand_parent_theme_path/locale/en_EN/Mage_Core11/test.txt'
            ),
            'theme inherited twice with module file in grandparent theme' => array($themeInheritedTwice, $file,
                'design_dir/area51/grand_parent_theme_path/Mage_Core11/test.txt',
                'design_dir/area51/grand_parent_theme_path/Mage_Core11/test.txt'
            ),
            'theme inherited twice with localized module file' => array($themeInheritedTwice, $file,
                'module_view_dir/area51/locale/en_EN/test.txt',
                'module_view_dir/area51/locale/en_EN/test.txt'
            ),
            'theme inherited twice with module file' => array($themeInheritedTwice, $file,
                'module_view_dir/area51/test.txt', 'module_view_dir/area51/test.txt'),
            'theme inherited twice with file in pub lib dir' => array($themeInheritedTwice, $file, 'js_dir/test.txt',
                'js_dir/test.txt'),
            'theme inherited twice, no file found' => array($themeInheritedTwice, $file, null, 'js_dir/test.txt'),
        );
    }

    /**
     * @param array $data
     * @return Mage_Core_Model_Config_Options|PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getOptionsMock(array $data)
    {
        /** @var $options Mage_Core_Model_Config_Options */
        $options = $this->getMock('Mage_Core_Model_Config_Options',
            array('getDesignDir', 'getJsDir'), array(), '', false);
        if (isset($data['designDir'])) {
            $options->expects($this->any())
                ->method('getDesignDir')
                ->will($this->returnValue($data['designDir']));
        }
        if (isset($data['jsDir'])) {
            $options->expects($this->any())
                ->method('getJsDir')
                ->will($this->returnValue($data['jsDir']));
        }

        return $options;
    }

    /**
     * @param array $data
     * @param array $methods
     * @return Mage_Core_Model_Config|PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getAppConfigMock(array $data, $methods = array('getOptions'))
    {
        $options = $this->_getOptionsMock($data);

        /** @var $appConfig Mage_Core_Model_Config */
        $appConfig = $this->getMock('Mage_Core_Model_Config', $methods, array(), '', false);
        $appConfig->expects($this->any())
            ->method('getOptions')
            ->will($this->returnValue($options));

        return $appConfig;
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
     * @return Magento_ObjectManager_Zend
     */
    protected function _getObjectManagerMock()
    {
        /** @var $objectManager Magento_ObjectManager_Zend */
        $objectManager = $this->getMock('Magento_ObjectManager_Zend', array('get'), array(), '', false);
        return $objectManager;
    }

    /**
     * @return Mage_Core_Model_Dir
     */
    protected function _getDirsMock()
    {
        /** @var $dirs Mage_Core_Model_Dir */
        $dirs = $this->getMock('Mage_Core_Model_Dir', array('getDir'), array(), '', false);
        return $dirs;
    }
}
