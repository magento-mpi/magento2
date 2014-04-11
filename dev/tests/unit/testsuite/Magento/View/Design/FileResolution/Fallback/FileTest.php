<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Design\FileResolution\Fallback;

class FileTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\View\Design\FileResolution\Fallback\ResolverInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resolver;

    /**
     * @var File
     */
    protected $object;

    protected function setUp()
    {
        $this->resolver = $this->getMock('Magento\View\Design\FileResolution\Fallback\ResolverInterface');
        $this->object = new File($this->resolver);
    }

    public function testGetFile()
    {
        $theme = $this->getMockForAbstractClass('\Magento\View\Design\ThemeInterface');
        $expected = 'some/file.ext';
        $this->resolver->expects($this->once())
            ->method('resolve')
            ->with(File::TYPE, 'file.ext', 'frontend', $theme, null, 'Magento_Module')
            ->will($this->returnValue($expected));
        $actual = $this->object->getFile('frontend', $theme, 'file.ext', 'Magento_Module');
        $this->assertSame($expected, $actual);
    }
}
