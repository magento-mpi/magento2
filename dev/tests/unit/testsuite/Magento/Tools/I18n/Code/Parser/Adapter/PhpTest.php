<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\I18n\Code\Parser\Adapter;

class PhpTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|
     * \Magento\Tools\I18n\Code\Parser\Adapter\Php\Tokenizer\PhraseCollector
     */
    protected $_phraseCollectorMock;

    /**
     * @var \Magento\Tools\I18n\Code\Parser\Adapter\Php
     */
    protected $_adapter;

    protected function setUp()
    {
        $this->_phraseCollectorMock = $this->getMock(
            'Magento\Tools\I18n\Code\Parser\Adapter\Php\Tokenizer\PhraseCollector',
            array(),
            array(),
            '',
            false
        );

        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_adapter = $objectManagerHelper->getObject(
            'Magento\Tools\I18n\Code\Parser\Adapter\Php',
            array('phraseCollector' => $this->_phraseCollectorMock)
        );
    }

    public function testParse()
    {
        $expectedResult = array(array('phrase' => 'phrase1', 'file' => 'file1', 'line' => 15, 'quote' => ''));

        $this->_phraseCollectorMock->expects($this->once())->method('parse')->with('file1');
        $this->_phraseCollectorMock->expects(
            $this->once()
        )->method(
            'getPhrases'
        )->will(
            $this->returnValue(array(array('phrase' => 'phrase1', 'file' => 'file1', 'line' => 15)))
        );

        $this->_adapter->parse('file1');
        $this->assertEquals($expectedResult, $this->_adapter->getPhrases());
    }
}
