<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Code_Minifier_Strategy_LiteTest extends PHPUnit_Framework_TestCase
{
    public function testGetMinifiedFile()
    {
        $originalFile = __DIR__ . '/original/some.js';
        $minifiedFile = __DIR__ . '/minified/some.min.js';
        $content = 'content';
        $minifiedContent = 'minified content';

        $filesystem = $this->getMock('Magento_Filesystem', array(), array(), '', false);
        $filesystem->expects($this->once())
            ->method('read')
            ->with($originalFile)
            ->will($this->returnValue($content));
        $filesystem->expects($this->once())
            ->method('write')
            ->with($minifiedFile, $minifiedContent);

        $adapter = $this->getMockForAbstractClass('Magento_Code_Minifier_AdapterInterface', array(), '', false);
        $adapter->expects($this->once())
            ->method('minify')
            ->with($content)
            ->will($this->returnValue($minifiedContent));

        $strategy = new Magento_Code_Minifier_Strategy_Lite($adapter, $filesystem);
        $strategy->minifyFile($originalFile, $minifiedFile);
    }

    public function testGetMinifiedFileNoUpdateNeeded()
    {
        $originalFile = __DIR__ . '/original/some.js';
        $minifiedFile = __DIR__ . '/some.min.js';

        $filesystem = $this->getMock('Magento_Filesystem', array(), array(), '', false);
        $filesystem->expects($this->once())
            ->method('has')
            ->with($minifiedFile)
            ->will($this->returnValue(true));
        $filesystem->expects($this->never())
            ->method('read');
        $filesystem->expects($this->never())
            ->method('write');

        $adapter = $this->getMockForAbstractClass('Magento_Code_Minifier_AdapterInterface', array(), '', false);
        $adapter->expects($this->never())
            ->method('minify');

        $strategy = new Magento_Code_Minifier_Strategy_Lite($adapter, $filesystem);

        $strategy->minifyFile($originalFile, $minifiedFile);
    }
}
