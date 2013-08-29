<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\I18n\Code\Dictionary;

class GeneratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Tools\I18n\Code\Dictionary\ParserInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_parser;

    /**
     * @var \Magento\Tools\I18n\Code\Dictionary\ParserInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_writer;

    /**
     * @var \Magento\Tools\I18n\Code\Dictionary\Generator
     */
    protected $_generator;

    /**
     * @var array
     */
    protected $_phrases;

    protected function setUp()
    {
        $this->_parser = $this->getMock('Magento\Tools\I18n\Code\Dictionary\ParserInterface');
        $this->_writer = $this->getMock('Magento\Tools\I18n\Code\Dictionary\WriterInterface');
        $this->_phrases = array(
            array('phrase' => 'phrase1', 'context_type' => 'theme', 'context' => array('theme1' => 1, 'theme2' => 1)),
            array('phrase' => 'phrase2', 'context_type' => 'module', 'context' => array('module1' => 1,
                'module2' => 1)),
        );

        $objectManagerHelper = new \Magento_Test_Helper_ObjectManager($this);
        $this->_generator = $objectManagerHelper->getObject('Magento\Tools\I18n\Code\Dictionary\Generator', array(
            'parser' => $this->_parser,
            'writer' => $this->_writer,
        ));
    }

    public function testGenerateWithoutContext()
    {
        $this->_parser->expects($this->once())->method('parse');
        $this->_parser->expects($this->once())->method('getPhrases')->will($this->returnValue($this->_phrases));

        $this->_writer->expects($this->at(0))->method('write')->with(array('phrase1', 'phrase1'));
        $this->_writer->expects($this->at(1))->method('write')->with(array('phrase2', 'phrase2'));

        $this->_generator->generate(false);
    }

    public function testGenerateWithContext()
    {
        $this->_parser->expects($this->once())->method('parse');
        $this->_parser->expects($this->once())->method('getPhrases')->will($this->returnValue($this->_phrases));

        $this->_writer->expects($this->at(0))->method('write')
            ->with(array('phrase1', 'phrase1', 'theme', 'theme1,theme2'));
        $this->_writer->expects($this->at(1))->method('write')
            ->with(array('phrase2', 'phrase2', 'module', 'module1,module2'));

        $this->_generator->generate();
    }
}
