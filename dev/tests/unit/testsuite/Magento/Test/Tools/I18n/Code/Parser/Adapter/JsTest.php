<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Test\Tools\I18n\Code\Parser\Adapter;

class JsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $_testFile;

    /**
     * @var \Magento\Tools\I18n\Code\Parser\Adapter\Js
     */
    protected $_adapter;

    protected function setUp()
    {
        // dev/tests/unit/testsuite/tools/I18n/Parser/Adapter/_files/file.js
        $this->_testFile = str_replace('\\', '/', realpath(dirname(__FILE__))) . '/_files/file.js';

        $objectManagerHelper = new \Magento_TestFramework_Helper_ObjectManager($this);
        $this->_adapter = $objectManagerHelper->getObject('Magento\Tools\I18n\Code\Parser\Adapter\Js');
    }

    public function testParse()
    {
        $expectedResult = array(
            array(
                'phrase' => 'Phrase 1',
                'file' => $this->_testFile,
                'line' => 10,
            ),
            array(
                'phrase' => 'Phrase 2 %1',
                'file' => $this->_testFile,
                'line' => 11,
            ),
        );

        $this->_adapter->parse($this->_testFile);

        $this->assertEquals($expectedResult, $this->_adapter->getPhrases());
    }
}
