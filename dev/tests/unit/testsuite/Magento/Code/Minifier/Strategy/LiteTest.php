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
        $expectedMinifiedFile = __DIR__ . '/minified/some.min.js';
        $content = 'content';
        $minifiedContent = 'minified content';

        $filesystem = $this->getMock('Magento_Filesystem', array(), array(), '', false);
        $filesystem->expects($this->once())
            ->method('read')
            ->with($originalFile)
            ->will($this->returnValue($content));
        $filesystem->expects($this->once())
            ->method('write')
            ->with($this->anything(), $minifiedContent);

        $adapter = $this->getMockForAbstractClass('Magento_Code_Minifier_AdapterInterface', array(), '', false);
        $adapter->expects($this->once())
            ->method('minify')
            ->with($content)
            ->will($this->returnValue($minifiedContent));

        $strategy = new Magento_Code_Minifier_Strategy_Lite($adapter, $filesystem);

        $minifiedFile = $strategy->getMinifiedFile($originalFile, $expectedMinifiedFile);
        $this->assertSame($expectedMinifiedFile, $minifiedFile);
    }

    public function testGetMinifiedFileNoUpdateNeeded()
    {
        $originalFile = __DIR__ . '/original/some.js';
        $expectedMinifiedFile = __DIR__ . '/some.min.js';

        $filesystem = $this->getMock('Magento_Filesystem', array(), array(), '', false);
        $filesystem->expects($this->once())
            ->method('has')
            ->with($expectedMinifiedFile)
            ->will($this->returnValue(true));
        $filesystem->expects($this->never())
            ->method('read');
        $filesystem->expects($this->never())
            ->method('write');

        $adapter = $this->getMockForAbstractClass('Magento_Code_Minifier_AdapterInterface', array(), '', false);
        $adapter->expects($this->never())
            ->method('minify');

        $strategy = new Magento_Code_Minifier_Strategy_Lite($adapter, $filesystem);

        $minifiedFile = $strategy->getMinifiedFile($originalFile, $expectedMinifiedFile);
        $this->assertSame($expectedMinifiedFile, $minifiedFile);
    }
}
