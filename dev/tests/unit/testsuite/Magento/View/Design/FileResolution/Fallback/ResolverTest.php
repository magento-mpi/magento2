<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Design\FileResolution\Fallback;

class ResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Filesystem\Directory\Read|\PHPUnit_Framework_MockObject_MockObject
     */
    private $directory;

    /**
     * @var \Magento\View\Design\Fallback\Rule\RuleInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $rule;

    /**
     * @var \Magento\View\Design\FileResolution\Fallback\Resolver
     */
    private $object;

    protected function setUp()
    {
        $this->directory = $this->getMock('\Magento\Filesystem\Directory\Read', array(), array(), '', false);
        $this->rule = $this->getMock('\Magento\View\Design\Fallback\Rule\RuleInterface', array(), array(), '', false);
        $this->directory->expects($this->any())
            ->method('getRelativePath')
            ->will($this->returnArgument(0));
        $this->object = new \Magento\View\Design\FileResolution\Fallback\Resolver();
    }

    public function testResolveFile()
    {
        $expectedPath = 'some/dir/file.ext';
        $this->rule->expects($this->once())
            ->method('getPatternDirs')
            ->will($this->returnValue(['some/dir']));
        $this->directory->expects($this->once())
            ->method('isExist')
            ->with($expectedPath)
            ->will($this->returnValue(true));
        $this->assertSame($expectedPath, $this->object->resolveFile($this->directory, $this->rule, 'file.ext'));
    }

    public function testResolveFileNoPatterns()
    {
        $this->rule->expects($this->once())
            ->method('getPatternDirs')
            ->will($this->returnValue([]));
        $this->assertFalse($this->object->resolveFile($this->directory, $this->rule, 'file.ext'));
    }

    public function testResolveFileNonexistentFile()
    {
        $this->rule->expects($this->once())
            ->method('getPatternDirs')
            ->will($this->returnValue(['some/dir']));
        $this->directory->expects($this->once())
            ->method('isExist')
            ->will($this->returnValue(false));
        $this->assertFalse($this->object->resolveFile($this->directory, $this->rule, 'file.ext'));
    }
}
