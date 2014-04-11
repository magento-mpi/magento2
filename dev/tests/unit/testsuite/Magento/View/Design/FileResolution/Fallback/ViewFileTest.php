<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Design\FileResolution\Fallback;

class ViewFileTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\View\Design\FileResolution\Fallback\ResolverInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resolver;

    /**
     * @var ViewFile
     */
    protected $object;

    protected function setUp()
    {
        $this->resolver = $this->getMock('Magento\View\Design\FileResolution\Fallback\ResolverInterface');
        $this->object = new ViewFile($this->resolver);
    }

    public function testGetFile()
    {
        $theme = $this->getMockForAbstractClass('\Magento\View\Design\ThemeInterface');
        $expected = 'some/file.ext';
        $this->resolver->expects($this->once())
            ->method('resolve')
            ->with(ViewFile::TYPE, 'file.ext', 'frontend', $theme, 'en_US', 'Magento_Module')
            ->will($this->returnValue($expected));
        $actual = $this->object->getViewFile('frontend', $theme, 'en_US', 'file.ext', 'Magento_Module');
        $this->assertSame($expected, $actual);
    }
}
