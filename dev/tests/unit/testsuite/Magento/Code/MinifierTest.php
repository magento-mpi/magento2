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
     * @var Magento_Code_Minify_StrategyInterface|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_strategy;

    /**
     * @var Magento_Code_Minifier
     */
    protected $_minifier;

    protected function setUp()
    {
        $this->_strategy = $this->getMockForAbstractClass('Magento_Code_Minify_StrategyInterface');
        $this->_minifier = new Magento_Code_Minifier($this->_strategy);
    }

    public function testGetMinifiedFile()
    {
        $originalFile = '/original/some.js';
        $expectedFile = '/minified/some.min.jd';

        $this->_strategy->expects($this->once())
            ->method('getMinifiedFile')
            ->with($originalFile, $this->isInstanceOf('Magento_Code_Minifier'))
            ->will($this->returnValue($expectedFile));
        $minifiedFile = $this->_minifier->getMinifiedFile($originalFile);
        $this->assertSame($expectedFile, $minifiedFile);
    }

    /**
     * @param string $originalFile
     * @param bool $expected
     * @dataProvider isFileMinifiedDataProvider
     */
    public function testIsFileMinified($originalFile, $expected)
    {
        $actual = $this->_minifier->isFileMinified($originalFile);
        $this->assertSame($expected, $actual);
    }

    public function isFileMinifiedDataProvider()
    {
        return array(
            'minified' => array('file.min.js', true),
            'not minified' => array('file.js', false),
        );
    }

    public function testGenerateMinifiedFileName()
    {
        $originalFile = '/path/file.js';
        $minifiedFile = $this->_minifier->generateMinifiedFileName($originalFile);
        $this->assertNotSame($originalFile, $minifiedFile);
        $this->assertStringEndsWith('file.min.js', $minifiedFile);
    }
}
