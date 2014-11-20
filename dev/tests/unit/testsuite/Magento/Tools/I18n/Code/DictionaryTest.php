<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\I18n\Code;

class DictionaryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Tools\I18n\Code\Dictionary
     */
    protected $_dictionary;

    protected function setUp()
    {
        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_dictionary = $objectManagerHelper->getObject('Magento\Tools\I18n\Code\Dictionary');
    }

    public function testPhraseCollecting()
    {
        $phraseFirstMock = $this->getMock('Magento\Tools\I18n\Code\Dictionary\Phrase', array(), array(), '', false);
        $phraseSecondMock = $this->getMock('Magento\Tools\I18n\Code\Dictionary\Phrase', array(), array(), '', false);

        $this->_dictionary->addPhrase($phraseFirstMock);
        $this->_dictionary->addPhrase($phraseSecondMock);

        $this->assertEquals(array($phraseFirstMock, $phraseSecondMock), $this->_dictionary->getPhrases());
    }

    public function testGetDuplicates()
    {
        $phraseFirstMock = $this->getMock('Magento\Tools\I18n\Code\Dictionary\Phrase', array(), array(), '', false);
        $phraseFirstMock->expects($this->once())->method('getKey')->will($this->returnValue('key_1'));
        $phraseSecondMock = $this->getMock('Magento\Tools\I18n\Code\Dictionary\Phrase', array(), array(), '', false);
        $phraseSecondMock->expects($this->once())->method('getKey')->will($this->returnValue('key_1'));
        $phraseThirdMock = $this->getMock('Magento\Tools\I18n\Code\Dictionary\Phrase', array(), array(), '', false);
        $phraseThirdMock->expects($this->once())->method('getKey')->will($this->returnValue('key_3'));

        $this->_dictionary->addPhrase($phraseFirstMock);
        $this->_dictionary->addPhrase($phraseSecondMock);
        $this->_dictionary->addPhrase($phraseThirdMock);

        $this->assertEquals(array(array($phraseFirstMock, $phraseSecondMock)), $this->_dictionary->getDuplicates());
    }
}
