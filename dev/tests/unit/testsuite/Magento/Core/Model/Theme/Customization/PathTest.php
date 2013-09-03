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

/**
 * Test of customization path model
 */
class Magento_Core_Model_Theme_Customization_PathTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_Theme_Customization_Path
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_dir;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_theme;

    protected function setUp()
    {
        $this->_theme = $this->getMock('Magento_Core_Model_Theme', null, array(), '', false);
        $this->_dir = $this->getMock('Magento_Core_Model_Dir', array(), array(), '', false);
        $this->_model = new Magento_Core_Model_Theme_Customization_Path($this->_dir);
    }

    protected function tearDown()
    {
        $this->_theme = null;
        $this->_dir = null;
        $this->_model = null;
    }

    /**
     * @covers Magento_Core_Model_Theme_Customization_Path::__construct
     * @covers Magento_Core_Model_Theme_Customization_Path::getCustomizationPath
     */
    public function testGetCustomizationPath()
    {
        $this->_dir->expects($this->once())->method('getDir')->with(Magento_Core_Model_Dir::MEDIA)
            ->will($this->returnValue('/media_dir'));
        $expectedPath = implode(
            DIRECTORY_SEPARATOR,
            array('/media_dir', Magento_Core_Model_Theme_Customization_Path::DIR_NAME, '123')
        );
        $this->assertEquals($expectedPath, $this->_model->getCustomizationPath($this->_theme->setId(123)));
        $this->assertNull($this->_model->getCustomizationPath($this->_theme->setId(null)));
    }

    /**
     * @covers Magento_Core_Model_Theme_Customization_Path::getThemeFilesPath
     */
    public function testGetThemeFilesPath()
    {
        $this->_theme->setArea('area51');
        $this->_dir->expects($this->once())->method('getDir')->with(Magento_Core_Model_Dir::THEMES)
            ->will($this->returnValue('/themes_dir'));
        $expectedPath = implode(
            \Magento\Filesystem::DIRECTORY_SEPARATOR,
            array('/themes_dir', 'area51', 'path')
        );
        $this->assertEquals($expectedPath, $this->_model->getThemeFilesPath($this->_theme->setThemePath('path')));
        $this->assertNull($this->_model->getCustomizationPath($this->_theme->setThemePath(null)));
    }

    /**
     * @covers Magento_Core_Model_Theme_Customization_Path::getCustomViewConfigPath
     */
    public function testGetCustomViewConfigPath()
    {
        $this->_dir->expects($this->once())->method('getDir')->with(Magento_Core_Model_Dir::MEDIA)
            ->will($this->returnValue('/media_dir'));
        $expectedPath = implode(
            DIRECTORY_SEPARATOR,
            array('/media_dir', Magento_Core_Model_Theme_Customization_Path::DIR_NAME, '123',
                Magento_Core_Model_Theme::FILENAME_VIEW_CONFIG)
        );
        $this->assertEquals($expectedPath, $this->_model->getCustomViewConfigPath($this->_theme->setId(123)));
        $this->assertNull($this->_model->getCustomViewConfigPath($this->_theme->setId(null)));
    }
}
