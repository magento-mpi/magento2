<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\I18n\Code\Dictionary\Parser;

class AbstractParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Tools\I18n\Code\Dictionary\ContextDetector|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_contextDetector;

    /**
     * @var \Magento\Tools\I18n\Code\Dictionary\Parser\AbstractParser|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_parser;

    protected function setUp()
    {
        $this->_contextDetector = $this->getMock('Magento\Tools\I18n\Code\Dictionary\ContextDetector', array(), array(),
            '', false);

        $this->_parser = $this->getMockForAbstractClass(
            'Magento\Tools\I18n\Code\Dictionary\Parser\AbstractParser',
            array(array('file1', 'file2'), $this->_contextDetector)
        );
    }

    public function testParse()
    {
        $this->_parser->expects($this->at(0))->method('_parse')->with('file1');
        $this->_parser->expects($this->at(1))->method('_parse')->with('file2');

        $this->_parser->parse();
    }

    public function getPhrases()
    {
        $this->assertInternalType('array', $this->_parser->getPhrases());
    }
}
