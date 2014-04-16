<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Design\FileResolution\Fallback;

use \Magento\View\Design\Fallback\RulePool;

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
            ->with(RulePool::TYPE_TEMPLATE_FILE, 'file.ext', 'frontend', $theme, null, 'Magento_Module')
            ->will($this->returnValue($expected));
        $actual = $this->object->getFile('frontend', $theme, 'file.ext', 'Magento_Module');
        $this->assertSame($expected, $actual);
    }
}
