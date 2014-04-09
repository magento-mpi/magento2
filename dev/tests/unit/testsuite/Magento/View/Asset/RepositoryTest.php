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
        $assetSource = $this->getMock('Magento\View\Asset\Source', array(), array(), '', false);
        $this->object = new Repository(
            $this->getMockForAbstractClass('\Magento\UrlInterface'),
            $this->getMockForAbstractClass('Magento\View\DesignInterface'),
            $this->themeProvider,
            $assetSource
        );
    }

    public function testCreate()
    {
        $this->markTestIncomplete('MAGETWO-21654');
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

    /**
     * @param string $file
     * @param string $expectedErrorMessage
     * @dataProvider extractModuleExceptionDataProvider
     */
    public function testExtractModuleException($file, $expectedErrorMessage)
    {
        $this->setExpectedException('\Magento\Exception', $expectedErrorMessage);
        Repository::extractModule($file);
    }

    /**
     * @return array
     */
    public function extractModuleExceptionDataProvider()
    {
        return array(
            array('::no_scope.ext', 'Scope separator "::" cannot be used without scope identifier.'),
            array('../file.ext', 'File name \'../file.ext\' is forbidden for security reasons.'),
        );
    }

    public function testExtractModule()
    {
        $this->assertEquals(array('Module_One', 'File'), Repository::extractModule('Module_One::File'));
        $this->assertEquals(array('', 'File'), Repository::extractModule('File'));
        $this->assertEquals(
            array('Module_One', 'File::SomethingElse'),
            Repository::extractModule('Module_One::File::SomethingElse')
        );
    }
} 
