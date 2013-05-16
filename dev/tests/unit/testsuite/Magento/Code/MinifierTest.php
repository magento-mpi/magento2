<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Code_MinifierTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Code_Minifier_StrategyInterface|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_strategy;

    /**
     * @var Magento_Code_Minifier
     */
    protected $_minifier;

    protected function setUp()
    {
        $this->_strategy = $this->getMockForAbstractClass('Magento_Code_Minifier_StrategyInterface');
        $this->_minifier = new Magento_Code_Minifier($this->_strategy, __DIR__);
    }

    public function testGetMinifiedFile()
    {
        $originalFile = '/original/some.js';

        $this->_strategy->expects($this->once())
            ->method('getMinifiedFile')
            ->with($originalFile, $this->matches(__DIR__ . '%ssome.min.js'))
            ->will($this->returnArgument(1));
        $minifiedFile = $this->_minifier->getMinifiedFile($originalFile);
        $this->assertStringMatchesFormat(__DIR__ . '%ssome.min.js', $minifiedFile);
    }

    public function testGetMinifiedFileOriginalMinified()
    {
        $originalFile = 'file.min.js';
        $this->_strategy->expects($this->never())
            ->method('getMinifiedFile');
        $minifiedFile = $this->_minifier->getMinifiedFile($originalFile);
        $this->assertSame($originalFile, $minifiedFile);
    }
}
