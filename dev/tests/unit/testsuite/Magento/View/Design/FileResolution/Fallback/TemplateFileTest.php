<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Design\FileResolution\Fallback;

class TemplateFileTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\View\Design\FileResolution\Fallback\ResolverInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resolver;

    /**
     * @var TemplateFile
     */
    protected $object;

    protected function setUp()
    {
        $this->resolver = $this->getMock('Magento\View\Design\FileResolution\Fallback\ResolverInterface');
        $this->object = new TemplateFile($this->resolver);
    }

    public function testGetFile()
    {
        $theme = $this->getMockForAbstractClass('\Magento\View\Design\ThemeInterface');
        $expected = 'some/file.ext';
        $this->resolver->expects($this->once())
            ->method('resolve')
            ->with(TemplateFile::TYPE, 'file.ext', 'frontend', $theme, null, 'Magento_Module')
            ->will($this->returnValue($expected));
        $actual = $this->object->getTemplateFile('frontend', $theme, 'file.ext', 'Magento_Module');
        $this->assertSame($expected, $actual);
    }
}
