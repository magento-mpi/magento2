<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\View\Design\FileResolution\Fallback;

use \Magento\Framework\View\Design\Fallback\RulePool;

class StaticFileTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $resolver;

    /**
     * @var StaticFile
     */
    protected $object;

    protected function setUp()
    {
        $this->resolver = $this->getMock('Magento\Framework\View\Design\FileResolution\Fallback\ResolverInterface');
        $this->object = new StaticFile($this->resolver);
    }

    public function testGetFile()
    {
        $theme = $this->getMockForAbstractClass('\Magento\Framework\View\Design\ThemeInterface');
        $expected = 'some/file.ext';
        $this->resolver->expects($this->once())
            ->method('resolve')
            ->with(RulePool::TYPE_STATIC_FILE, 'file.ext', 'frontend', $theme, 'en_US', 'Magento_Module')
            ->will($this->returnValue($expected));
        $actual = $this->object->getFile('frontend', $theme, 'en_US', 'file.ext', 'Magento_Module');
        $this->assertSame($expected, $actual);
    }
}
