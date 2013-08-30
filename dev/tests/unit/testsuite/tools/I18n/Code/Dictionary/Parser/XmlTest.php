<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\I18n\Code\Dictionary\Parser;

class XmlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $_testFile;

    /**
     * @var \Magento\Tools\I18n\Code\Dictionary\ContextDetector|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_contextDetector;

    protected function setUp()
    {
        // dev/tests/unit/testsuite/tools/I18n/_files/layout.xml
        $this->_testFile = str_replace('\\', '/', realpath(dirname(__FILE__) . '/../../../') . '/_files/')
            . 'layout.xml';

        $this->_contextDetector = $this->getMock('Magento\Tools\I18n\Code\Dictionary\ContextDetector', array(), array(),
            '', false);
        $this->_contextDetector->expects($this->any())->method('getContext')
            ->will($this->returnValue(array('contextType', 'contextValue')));
    }

    public function testParse()
    {
        $objectManagerHelper = new \Magento_Test_Helper_ObjectManager($this);
        /** @var \Magento\Tools\I18n\Code\Dictionary\Parser\Js $parser */
        $parser = $objectManagerHelper->getObject('Magento\Tools\I18n\Code\Dictionary\Parser\Xml', array(
            'files' => array($this->_testFile),
            'contextDetector' => $this->_contextDetector,
        ));

        $expectedResult = array(
            'contextType::Phrase 2' => array(
                'phrase' => 'Phrase 2',
                'file' => $this->_testFile,
                'line' => '',
                'context' => array(
                    'contextValue' => 1
                ),
                'context_type' => 'contextType',
            ),
            'contextType::Phrase 3' => array(
                'phrase' => 'Phrase 3',
                'file' => $this->_testFile,
                'line' => '',
                'context' => array(
                    'contextValue' => 1
                ),
                'context_type' => 'contextType',
            ),
            'contextType::Phrase 1' => array(
                'phrase' => 'Phrase 1',
                'file' => $this->_testFile,
                'line' => '',
                'context' => array(
                    'contextValue' => 1
                ),
                'context_type' => 'contextType',
            ),
        );

        $parser->parse();
        $this->assertEquals($expectedResult, $parser->getPhrases());
    }
}
