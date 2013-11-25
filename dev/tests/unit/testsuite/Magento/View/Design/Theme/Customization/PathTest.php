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
namespace Magento\View\Design\Theme\Customization;

class PathTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\View\Design\Theme\Customization\Path
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_dir;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_theme;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_appState;

    protected function setUp()
    {
        $this->_theme = $this->getMock('Magento\Core\Model\Theme', array('__wakeup'), array(), '', false);
        $this->_appState = $this->getMock('Magento\App\State', array('getAreaCode'), array(), '', false);
        $appStateProperty = new \ReflectionProperty('\Magento\Core\Model\Theme', '_appState');
        $appStateProperty->setAccessible(true);
        $appStateProperty->setValue($this->_theme, $this->_appState);
        $this->_dir = $this->getMock('Magento\App\Dir', array(), array(), '', false);
        $this->_model = new \Magento\View\Design\Theme\Customization\Path($this->_dir);
    }

    protected function tearDown()
    {
        $this->_theme = null;
        $this->_dir = null;
        $this->_model = null;
    }

    /**
     * @covers \Magento\View\Design\Theme\Customization\Path::__construct
     * @covers \Magento\View\Design\Theme\Customization\Path::getCustomizationPath
     */
    public function testGetCustomizationPath()
    {
        $this->_dir->expects($this->once())->method('getDir')->with(\Magento\App\Dir::MEDIA)
            ->will($this->returnValue('/media_dir'));
        $expectedPath = implode(
            '/',
            array('/media_dir', \Magento\View\Design\Theme\Customization\Path::DIR_NAME, '123')
        );
        $this->assertEquals($expectedPath, $this->_model->getCustomizationPath($this->_theme->setId(123)));
        $this->assertNull($this->_model->getCustomizationPath($this->_theme->setId(null)));
    }

    /**
     * @covers \Magento\View\Design\Theme\Customization\Path::getThemeFilesPath
     */
    public function testGetThemeFilesPath()
    {
        $this->_appState->expects($this->any())->method('getAreaCode')->will($this->returnValue('area51'));
        $this->_dir->expects($this->once())->method('getDir')->with(\Magento\App\Dir::THEMES)
            ->will($this->returnValue('/themes_dir'));
        $expectedPath = implode('/', array('/themes_dir', 'area51', 'path'));
        $this->assertEquals(
            $expectedPath,
            $this->_model->getThemeFilesPath($this->_theme->setThemePath('path'))
        );
        $this->assertNull($this->_model->getCustomizationPath($this->_theme->setThemePath(null)));
    }

    /**
     * @covers \Magento\View\Design\Theme\Customization\Path::getCustomViewConfigPath
     */
    public function testGetCustomViewConfigPath()
    {
        $this->_dir->expects($this->once())->method('getDir')->with(\Magento\App\Dir::MEDIA)
            ->will($this->returnValue('/media_dir'));
        $expectedPath = implode(
            '/',
            array(
                '/media_dir',
                \Magento\View\Design\Theme\Customization\Path::DIR_NAME,
                '123',
                \Magento\Core\Model\Theme::FILENAME_VIEW_CONFIG
            )
        );
        $this->assertEquals($expectedPath, $this->_model->getCustomViewConfigPath($this->_theme->setId(123)));
        $this->assertNull($this->_model->getCustomViewConfigPath($this->_theme->setId(null)));
    }
}
