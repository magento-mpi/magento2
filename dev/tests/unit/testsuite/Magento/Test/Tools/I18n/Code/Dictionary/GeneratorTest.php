<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Test\Tools\I18n\Code\Dictionary;

use Magento\Tools\I18n\Code\Context;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class GeneratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Tools\I18n\Code\Parser\Parser|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_parserMock;

    /**
     * @var \Magento\Tools\I18n\Code\Parser\Contextual|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_contextualParserMock;

    /**
     * @var \Magento\Tools\I18n\Code\Factory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_factoryMock;

    /**
     * @var \Magento\Tools\I18n\Code\Dictionary\WriterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_writerMock;

    /**
     * @var \Magento\Tools\I18n\Code\Dictionary\Generator
     */
    protected $_generator;

    protected function setUp()
    {
        $this->_parserMock = $this->getMock('Magento\Tools\I18n\Code\Parser\Parser', array(), array(), '', false);
        $this->_contextualParserMock = $this->getMock(
            'Magento\Tools\I18n\Code\Parser\Contextual',
            array(),
            array(),
            '',
            false
        );
        $this->_writerMock = $this->getMock('Magento\Tools\I18n\Code\Dictionary\WriterInterface');
        $this->_factoryMock = $this->getMock('Magento\Tools\I18n\Code\Factory', array(), array(), '', false);
        $this->_factoryMock->expects(
            $this->any()
        )->method(
            'createDictionaryWriter'
        )->will(
            $this->returnValue($this->_writerMock)
        );

        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_generator = $objectManagerHelper->getObject(
            'Magento\Tools\I18n\Code\Dictionary\Generator',
            array(
                'parser' => $this->_parserMock,
                'contextualParser' => $this->_contextualParserMock,
                'factory' => $this->_factoryMock
            )
        );
    }

    public function testCreatingDictionaryWriter()
    {
        $outputFilename = 'test';

        $this->_factoryMock->expects($this->once())->method('createDictionaryWriter')->with($outputFilename);
        $this->_parserMock->expects($this->any())->method('getPhrases')->will($this->returnValue(array()));

        $this->_generator->generate(array(), $outputFilename);
    }

    public function testUsingRightParserWhileWithoutContextParsing()
    {
        $filesOptions = array('file1', 'file2');

        $this->_parserMock->expects($this->once())->method('parse')->with($filesOptions);
        $this->_parserMock->expects($this->once())->method('getPhrases')->will($this->returnValue(array()));

        $this->_generator->generate($filesOptions, 'file.csv');
    }

    public function testUsingRightParserWhileWithContextParsing()
    {
        $filesOptions = array('file1', 'file2');

        $this->_contextualParserMock->expects($this->once())->method('parse')->with($filesOptions);
        $this->_contextualParserMock->expects($this->once())->method('getPhrases')->will($this->returnValue(array()));

        $this->_generator->generate($filesOptions, 'file.csv', true);
    }

    public function testWritingPhrases()
    {
        $phrases = array(
            $this->getMock('Magento\Tools\I18n\Code\Dictionary\Phrase', array(), array(), '', false),
            $this->getMock('Magento\Tools\I18n\Code\Dictionary\Phrase', array(), array(), '', false)
        );

        $this->_parserMock->expects($this->once())->method('getPhrases')->will($this->returnValue($phrases));

        $this->_writerMock->expects($this->at(0))->method('write')->with($phrases[0]);
        $this->_writerMock->expects($this->at(1))->method('write')->with($phrases[1]);

        $this->_generator->generate(array('file1', 'file2'), 'file.csv');
    }
}
