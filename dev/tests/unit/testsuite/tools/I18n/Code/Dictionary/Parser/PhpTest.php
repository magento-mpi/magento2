<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\I18n\Code\Dictionary\Parser;

class PhpTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento_Tokenizer_PhraseCollector|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_phraseCollector;

    /**
     * @var \Magento\Tools\I18n\Code\Dictionary\Parser\Php
     */
    protected $_parser;

    /**
     * @var \Magento\Tools\I18n\Code\Dictionary\ContextDetector|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_contextDetector;

    protected function setUp()
    {
        $this->_contextDetector = $this->getMock('Magento\Tools\I18n\Code\Dictionary\ContextDetector', array(), array(),
            '', false);
        $this->_contextDetector->expects($this->any())->method('getContext')
            ->will($this->returnValue(array('contextType', 'contextValue')));

        $this->_phraseCollector = $this->getMock('Magento_Tokenizer_PhraseCollector', array(), array(), '', false);

        $objectManagerHelper = new \Magento_Test_Helper_ObjectManager($this);
        $this->_parser = $objectManagerHelper->getObject('Magento\Tools\I18n\Code\Dictionary\Parser\Php', array(
            'files' => array('file1'),
            'contextDetector' => $this->_contextDetector,
            'phraseCollector' => $this->_phraseCollector,
        ));
    }

    public function testParse()
    {
        $this->_phraseCollector->expects($this->once())->method('parse')->with('file1');
        $this->_phraseCollector->expects($this->once())->method('getPhrases')->will($this->returnValue(array(
            array('phrase' => 'phrase1', 'file' => 'file1', 'line' => 15),
        )));

        $this->_parser->parse();

        $expectedResult = array(
            'contextType::phrase1' => array(
                'phrase' => 'phrase1',
                'file' => 'file1',
                'line' => 15,
                'context' => array(
                    'contextValue' => 1,
                ),
                'context_type' => 'contextType',
            ),
        );

        $this->assertEquals($expectedResult, $this->_parser->getPhrases());
    }
}
