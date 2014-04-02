<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Asset;

class RepositoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\View\Design\Theme\Provider|\PHPUnit_Framework_MockObject_MockObject
     */
    private $themeProvider;

    /**
     * @var Repository
     */
    private $object;

    protected function setUp()
    {
        $this->themeProvider = $this->getMock('\Magento\View\Design\Theme\Provider', array(), array(), '', false);
        $assetSource = $this->getMock('Magento\View\Asset\File\Source', array(), array(), '', false);
        $this->object = new Repository(
            $this->getMockForAbstractClass('\Magento\UrlInterface'),
            $this->getMockForAbstractClass('Magento\View\DesignInterface'),
            $this->themeProvider,
            $assetSource
        );
    }

    public function testExtractScope()
    {
        $originalData = array('original' => 'data');
        $params = $originalData;
        $this->assertEquals('file.ext', $this->object->extractScope('file.ext', $params));
        $this->assertSame($originalData, $params);

        $this->assertEquals('file.ext', $this->object->extractScope('Module_One::file.ext', $params));
        $this->assertArrayHasKey('module', $params);
        $this->assertEquals('Module_One', $params['module']);
    }

    /**
     * @expectedException \UnexpectedValueException
     * @expectedExceptionMessage Could not find theme 'nonexistent_theme' for area 'area'
     */
    public function testUpdateDesignParamsWrongTheme()
    {
        $params = array('area' => 'area', 'theme' => 'nonexistent_theme');
        $this->themeProvider->expects($this->once())
            ->method('getThemeModel')
            ->with('nonexistent_theme', 'area')
            ->will($this->returnValue(null));
        $this->object->updateDesignParams($params);
    }
} 
