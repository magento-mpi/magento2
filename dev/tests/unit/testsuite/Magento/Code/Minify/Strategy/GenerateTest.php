<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Code_Minify_Strategy_GenerateTest extends PHPUnit_Framework_TestCase
{
    public function testGetMinifiedFile()
    {
        $baseDir = __DIR__ . '/minified';
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

        $adapter = $this->getMockForAbstractClass('Magento_Code_Minify_AdapterInterface', array(), '', false);
        $adapter->expects($this->once())
            ->method('minify')
            ->with($content)
            ->will($this->returnValue($minifiedContent));

        $strategy = new Magento_Code_Minify_Strategy_Generate($adapter, $filesystem, $baseDir);
        $minifier = $this->getMock('Magento_Code_Minifier', array(), array(), '', false);
        $minifier->expects($this->once())
            ->method('isFileMinified')
            ->with($originalFile)
            ->will($this->returnValue(false));

        $minifier->expects($this->once())
            ->method('generateMinifiedFileName')
            ->with($originalFile)
            ->will($this->returnValue('some.min.js'));

        $minifiedFile = $strategy->getMinifiedFile($originalFile, $minifier);
        $this->assertStringEndsWith($expectedMinifiedFile, $minifiedFile);
        $this->assertStringStartsWith($baseDir, $minifiedFile);
    }

    public function testGetMinifiedFileOriginalMinified()
    {
        $baseDir = __DIR__ . '/minified';
        $originalFile = __DIR__ . '/original/some.js';
        $expectedMinifiedFile = __DIR__ . '/original/some.min.js';

        $filesystem = $this->getMock('Magento_Filesystem', array(), array(), '', false);
        $filesystem->expects($this->never())
            ->method('read');
        $filesystem->expects($this->never())
            ->method('write');
        $filesystem->expects($this->any())
            ->method('has')
            ->with($expectedMinifiedFile)
            ->will($this->returnValue(true));

        $adapter = $this->getMockForAbstractClass('Magento_Code_Minify_AdapterInterface', array(), '', false);
        $adapter->expects($this->never())
            ->method('minify');

        $strategy = new Magento_Code_Minify_Strategy_Generate($adapter, $filesystem, $baseDir);
        $minifier = $this->getMock('Magento_Code_Minifier', array(), array(), '', false);

        $minifier->expects($this->never())
            ->method('generateMinifiedFileName');

        $minifiedFile = $strategy->getMinifiedFile($originalFile, $minifier);
        $this->assertStringEndsWith($expectedMinifiedFile, $minifiedFile);
    }

    public function testGetMinifiedFileNoUpdateNeeded()
    {
        $baseDir = __DIR__ . '/minified';
        $originalFile = __DIR__ . '/original/some.js';
        $expectedMinifiedFile = $baseDir . '/some.min.js';

        $filesystem = $this->getMock('Magento_Filesystem', array(), array(), '', false);
        $filesystem->expects($this->at(2))
            ->method('has')
            ->with($expectedMinifiedFile)
            ->will($this->returnValue(true));
        $filesystem->expects($this->never())
            ->method('read');
        $filesystem->expects($this->never())
            ->method('write');

        $adapter = $this->getMockForAbstractClass('Magento_Code_Minify_AdapterInterface', array(), '', false);
        $adapter->expects($this->never())
            ->method('minify');


        $strategy = new Magento_Code_Minify_Strategy_Generate($adapter, $filesystem, $baseDir);
        $minifier = $this->getMock('Magento_Code_Minifier', array(), array(), '', false);
        $minifier->expects($this->once())
            ->method('isFileMinified')
            ->with($originalFile)
            ->will($this->returnValue(false));

        $minifier->expects($this->once())
            ->method('generateMinifiedFileName')
            ->with($originalFile)
            ->will($this->returnValue('some.min.js'));

        $minifiedFile = $strategy->getMinifiedFile($originalFile, $minifier);
        $this->assertStringEndsWith($expectedMinifiedFile, $minifiedFile);
        $this->assertStringStartsWith($baseDir, $minifiedFile);
    }

    public function testGetMinifiedFileMinifiedOriginal()
    {
        $baseDir = __DIR__ . '/minified';
        $originalFile = __DIR__ . '/original/some.js';

        $filesystem = $this->getMock('Magento_Filesystem', array(), array(), '', false);
        $filesystem->expects($this->never())
            ->method('read');
        $filesystem->expects($this->never())
            ->method('write');

        $adapter = $this->getMockForAbstractClass('Magento_Code_Minify_AdapterInterface', array(), '', false);
        $adapter->expects($this->never())
            ->method('minify');

        $strategy = new Magento_Code_Minify_Strategy_Generate($adapter, $filesystem, $baseDir);
        $minifier = $this->getMock('Magento_Code_Minifier', array(), array(), '', false);
        $minifier->expects($this->once())
            ->method('isFileMinified')
            ->with($originalFile)
            ->will($this->returnValue(true));

        $minifier->expects($this->never())
            ->method('generateMinifiedFileName');

        $minifiedFile = $strategy->getMinifiedFile($originalFile, $minifier);
        $this->assertSame($originalFile, $minifiedFile);
    }
}
