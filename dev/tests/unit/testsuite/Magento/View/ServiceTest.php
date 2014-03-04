<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View;

class ServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\App\State|\PHPUnit_Framework_MockObject_MockObject
     */
    private $appState;

    /**
     * @var \Magento\View\Design\Theme\FlyweightFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $themeFactory;

    /**
     * @var \Magento\View\Design\Theme\ListInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $themeList;

    /**
     * @var Service
     */
    private $object;

    protected function setUp()
    {
        $this->appState = $this->getMock('Magento\App\State', array(), array(), '', false);
        $design = $this->getMockForAbstractClass('Magento\View\DesignInterface');
        $this->themeFactory = $this->getMock(
            'Magento\View\Design\Theme\FlyweightFactory', array(), array(), '', false
        );
        $this->themeList = $this->getMockForAbstractClass('\Magento\View\Design\Theme\ListInterface');
        $filesystem = $this->getMock('Magento\App\Filesystem', array(), array(), '', false);
        $this->object = new Service(
            $this->appState,
            $design,
            $this->themeFactory,
            $this->themeList,
            $filesystem,
            $this->getMockForAbstractClass('\Magento\UrlInterface'),
            $this->getMock('\Magento\View\Asset\PreProcessor\Factory', array(), array(), '', false),
            $this->getMock('\Magento\View\Design\FileResolution\StrategyPool', array(), array(), '', false),
            new \Magento\View\Asset\PathGenerator
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
        $this->appState->expects($this->once())->method('isInstalled')->will($this->returnValue(true));
        $this->themeFactory->expects($this->once())
            ->method('create')
            ->with('nonexistent_theme', 'area')
            ->will($this->returnValue(null));
        $params = array('area' => 'area', 'theme' => 'nonexistent_theme');
        $this->object->updateDesignParams($params);
    }
} 
