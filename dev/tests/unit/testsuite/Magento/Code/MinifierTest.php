<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Code;

class MinifierTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Code\Minifier\StrategyInterface|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_strategy;

    /**
     * @var \Magento\Filesystem|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_filesystem;

    /**
     * @var \Magento\Code\Minifier
     */
    protected $_minifier;

    protected function setUp()
    {
        $this->_strategy = $this->getMockForAbstractClass('Magento\Code\Minifier\StrategyInterface');
        $this->_filesystem = $this->getMock('Magento\Filesystem', array(), array(), '', false);
        $this->_minifier = new \Magento\Code\Minifier($this->_strategy, $this->_filesystem, __DIR__);
    }

    public function testGetMinifiedFile()
    {
        $originalFile = '/original/some.js';

        $this->_strategy->expects($this->once())
            ->method('minifyFile')
            ->with($originalFile, $this->matches(__DIR__ . '%ssome.min.js'));
        $minifiedFile = $this->_minifier->getMinifiedFile($originalFile);
        $this->assertStringMatchesFormat(__DIR__ . '%ssome.min.js', $minifiedFile);
    }

    public function testGetMinifiedFileOriginalMinified()
    {
        $originalFile = 'file.min.js';
        $this->_strategy->expects($this->never())
            ->method('minifyFile');
        $minifiedFile = $this->_minifier->getMinifiedFile($originalFile);
        $this->assertSame($originalFile, $minifiedFile);
    }

    public function testGetMinifiedFileExistsMinified()
    {
        $originalFile = __DIR__ . '/original/some.js';
        $expectedMinifiedFile = __DIR__ . '/original/some.min.js';

        $this->_filesystem->expects($this->once())
            ->method('has')
            ->with($expectedMinifiedFile)
            ->will($this->returnValue(true));

        $minifiedFile = $this->_minifier->getMinifiedFile($originalFile, '/minified/some.min.js');
        $this->assertStringEndsWith($expectedMinifiedFile, $minifiedFile);
    }
}
