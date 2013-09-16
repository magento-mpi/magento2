<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Test\Tools\I18n\Code\Dictionary\Writer;

class CsvTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $_testFile;

    /**
     * @var \Magento\Tools\I18n\Code\Dictionary\Phrase|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_phraseFirstMock;

    /**
     * @var \Magento\Tools\I18n\Code\Dictionary\Phrase|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_phraseSecondMock;

    protected function setUp()
    {
        $this->_testFile = str_replace('\\', '/', realpath(dirname(__FILE__))) . '/_files/test.csv';

        $this->_phraseFirstMock = $this->getMock('Magento\Tools\I18n\Code\Dictionary\Phrase', array(), array(), '',
            false);
        $this->_phraseSecondMock = $this->getMock('Magento\Tools\I18n\Code\Dictionary\Phrase', array(), array(), '',
            false);
    }

    protected function tearDown()
    {
        if (file_exists($this->_testFile)) {
            unlink($this->_testFile);
        }
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Cannot open file for write dictionary: "wrong/path"
     */
    public function testWrongOutputFile()
    {
        $objectManagerHelper = new \Magento_TestFramework_Helper_ObjectManager($this);
        $objectManagerHelper->getObject('Magento\Tools\I18n\Code\Dictionary\Writer\Csv', array(
            'outputFilename' => 'wrong/path',
        ));
    }

    public function testWrite()
    {
        $this->_phraseFirstMock->expects($this->once())->method('getPhrase')
            ->will($this->returnValue('phrase1'));
        $this->_phraseFirstMock->expects($this->once())->method('getTranslation')
            ->will($this->returnValue('translation1'));
        $this->_phraseFirstMock->expects($this->once())->method('getContextType')
            ->will($this->returnValue('context_type1'));
        $this->_phraseFirstMock->expects($this->once())->method('getContextValueAsString')
            ->will($this->returnValue('content_value1'));

        $this->_phraseSecondMock->expects($this->once())->method('getPhrase')
            ->will($this->returnValue('phrase2'));
        $this->_phraseSecondMock->expects($this->once())->method('getTranslation')
            ->will($this->returnValue('translation2'));
        $this->_phraseSecondMock->expects($this->once())->method('getContextType')
            ->will($this->returnValue('context_type2'));
        $this->_phraseSecondMock->expects($this->once())->method('getContextValueAsString')
            ->will($this->returnValue('content_value2'));

        $objectManagerHelper = new \Magento_TestFramework_Helper_ObjectManager($this);
        /** @var \Magento\Tools\I18n\Code\Dictionary\Writer\Csv $writer */
        $writer = $objectManagerHelper->getObject('Magento\Tools\I18n\Code\Dictionary\Writer\Csv', array(
            'outputFilename' => $this->_testFile,
        ));
        $writer->write($this->_phraseFirstMock);
        $writer->write($this->_phraseSecondMock);

        $expected = "phrase1,translation1,context_type1,content_value1\nphrase2,translation2,context_type2,"
            . "content_value2\n";
        $this->assertEquals($expected, file_get_contents($this->_testFile));
    }

    public function testWriteWithoutContext()
    {
        $this->_phraseFirstMock->expects($this->once())->method('getPhrase')
            ->will($this->returnValue('phrase1'));
        $this->_phraseFirstMock->expects($this->once())->method('getTranslation')
            ->will($this->returnValue('translation1'));
        $this->_phraseFirstMock->expects($this->once())->method('getContextType')
            ->will($this->returnValue(''));

        $this->_phraseSecondMock->expects($this->once())->method('getPhrase')
            ->will($this->returnValue('phrase2'));
        $this->_phraseSecondMock->expects($this->once())->method('getTranslation')
            ->will($this->returnValue('translation2'));
        $this->_phraseSecondMock->expects($this->once())->method('getContextType')
            ->will($this->returnValue('context_type2'));
        $this->_phraseSecondMock->expects($this->once())->method('getContextValueAsString')
            ->will($this->returnValue(''));

        $objectManagerHelper = new \Magento_TestFramework_Helper_ObjectManager($this);
        /** @var \Magento\Tools\I18n\Code\Dictionary\Writer\Csv $writer */
        $writer = $objectManagerHelper->getObject('Magento\Tools\I18n\Code\Dictionary\Writer\Csv', array(
            'outputFilename' => $this->_testFile,
        ));
        $writer->write($this->_phraseFirstMock);
        $writer->write($this->_phraseSecondMock);

        $expected = "phrase1,translation1\nphrase2,translation2\n";
        $this->assertEquals($expected, file_get_contents($this->_testFile));
    }
}
