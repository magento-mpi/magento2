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
     * @var \Magento\View\Design\Theme\FlyweightFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $themeFactory;

    /**
     * @var Service
     */
    private $object;

    protected function setUp()
    {
        $appState = $this->getMock('Magento\App\State', array(), array(), '', false);
        $design = $this->getMockForAbstractClass('Magento\View\DesignInterface');
        $this->themeFactory = $this->getMock(
            'Magento\View\Design\Theme\FlyweightFactory', array(), array(), '', false
        );
        $filesystem = $this->getMock('Magento\App\Filesystem', array(), array(), '', false);
        $this->object = new \Magento\View\Service($appState, $design, $this->themeFactory, $filesystem);
    }

    public function testExtractScope()
    {
        $originalData = array('original' => 'data');
        $params = $originalData;
        $this->assertEquals('file.ext', $this->object->extractScope('file.ext', $params));
        $this->assertSame($originalData, $params);

        $this->assertEquals('file.ext', $this->object->extractScope('Module::file.ext', $params));
        $this->assertArrayHasKey('module', $params);
        $this->assertEquals('Module', $params['module']);
    }

    /**
     * @param string $file
     * @expectedException \Magento\Exception
     * @dataProvider extractScopeExceptionDataProvider
     */
    public function testExtractScopeException($file)
    {
        Service::extractModule($file);
    }

    /**
     * @return array
     */
    public function extractScopeExceptionDataProvider()
    {
        return array(
            array('::no_scope.ext'),
            array('../file.ext'),
        );
    }

    /**
     * @expectedException \UnexpectedValueException
     * @expectedExceptionMessage Could not find theme 'nonexistent_theme' for area 'area'
     */
    public function testUpdateDesignParamsWrongTheme()
    {
        $this->themeFactory->expects($this->once())
            ->method('create')
            ->with('nonexistent_theme', 'area')
            ->will($this->returnValue(null));
        $params = array('area' => 'area', 'theme' => 'nonexistent_theme');
        $this->object->updateDesignParams($params);
    }
} 
