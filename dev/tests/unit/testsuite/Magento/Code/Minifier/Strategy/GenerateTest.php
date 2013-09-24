<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Code\Minifier\Strategy;

class GenerateTest extends \PHPUnit_Framework_TestCase
{
    public function testGetMinifiedFile()
    {
        $originalFile = __DIR__ . '/original/some.js';
        $minifiedFile = __DIR__ . '/minified/some.min.js';
        $content = 'content';
        $minifiedContent = 'minified content';

        $filesystem = $this->getMock('Magento\Filesystem', array(), array(), '', false);
        $filesystem->expects($this->once())
            ->method('read')
            ->with($originalFile)
            ->will($this->returnValue($content));
        $filesystem->expects($this->once())
            ->method('write')
            ->with($minifiedFile, $minifiedContent);

        $adapter = $this->getMockForAbstractClass('Magento\Code\Minifier\AdapterInterface', array(), '', false);
        $adapter->expects($this->once())
            ->method('minify')
            ->with($content)
            ->will($this->returnValue($minifiedContent));

        $strategy = new \Magento\Code\Minifier\Strategy\Generate($adapter, $filesystem);
        $strategy->minifyFile($originalFile, $minifiedFile);
    }

    public function testGetMinifiedFileNoUpdateNeeded()
    {
        $originalFile = __DIR__ . '/original/some.js';
        $minifiedFile = __DIR__ . '/some.min.js';

        $filesystem = $this->getMock('Magento\Filesystem', array(), array(), '', false);
        $filesystem->expects($this->once())
            ->method('has')
            ->with($minifiedFile)
            ->will($this->returnValue(true));
        $mTimeMap = array(
            array($originalFile, null, 1),
            array($minifiedFile, null, 1),
        );
        $filesystem->expects($this->exactly(2))
            ->method('getMTime')
            ->will($this->returnValueMap($mTimeMap));
        $filesystem->expects($this->never())
            ->method('read');
        $filesystem->expects($this->never())
            ->method('write');

        $adapter = $this->getMockForAbstractClass('Magento\Code\Minifier\AdapterInterface', array(), '', false);
        $adapter->expects($this->never())
            ->method('minify');

        $strategy = new \Magento\Code\Minifier\Strategy\Generate($adapter, $filesystem);
        $strategy->minifyFile($originalFile, $minifiedFile);
    }
}
