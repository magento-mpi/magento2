<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\I18n;

class DictionaryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Tools\I18n\Dictionary
     */
    protected $_dictionary;

    protected function setUp()
    {
        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_dictionary = $objectManagerHelper->getObject('Magento\Tools\I18n\Dictionary');
    }

    public function testPhraseCollecting()
    {
        $phraseFirstMock = $this->getMock('Magento\Tools\I18n\Dictionary\Phrase', [], [], '', false);
        $phraseSecondMock = $this->getMock('Magento\Tools\I18n\Dictionary\Phrase', [], [], '', false);

        $this->_dictionary->addPhrase($phraseFirstMock);
        $this->_dictionary->addPhrase($phraseSecondMock);

        $this->assertEquals([$phraseFirstMock, $phraseSecondMock], $this->_dictionary->getPhrases());
    }

    public function testGetDuplicates()
    {
        $phraseFirstMock = $this->getMock('Magento\Tools\I18n\Dictionary\Phrase', [], [], '', false);
        $phraseFirstMock->expects($this->once())->method('getKey')->will($this->returnValue('key_1'));
        $phraseSecondMock = $this->getMock('Magento\Tools\I18n\Dictionary\Phrase', [], [], '', false);
        $phraseSecondMock->expects($this->once())->method('getKey')->will($this->returnValue('key_1'));
        $phraseThirdMock = $this->getMock('Magento\Tools\I18n\Dictionary\Phrase', [], [], '', false);
        $phraseThirdMock->expects($this->once())->method('getKey')->will($this->returnValue('key_3'));

        $this->_dictionary->addPhrase($phraseFirstMock);
        $this->_dictionary->addPhrase($phraseSecondMock);
        $this->_dictionary->addPhrase($phraseThirdMock);

        $this->assertEquals([[$phraseFirstMock, $phraseSecondMock]], $this->_dictionary->getDuplicates());
    }
}
