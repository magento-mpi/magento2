<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Test\Tools\I18n\Parser\Adapter;

use Magento\TestFramework\Helper\ObjectManager;
use Magento\Tools\I18n\Dictionary\Phrase;

class JsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $_testFile;

    /**
     * @var int
     */
    protected $_stringsCount;

    /**
     * @var \Magento\Tools\I18n\Parser\Adapter\Js
     */
    protected $_adapter;

    protected function setUp()
    {
        // dev/tests/unit/testsuite/tools/I18n/Parser/Adapter/_files/file.js
        $this->_testFile = str_replace('\\', '/', realpath(dirname(__FILE__))) . '/_files/file.js';
        $this->_stringsCount = count(file($this->_testFile));

        $this->_adapter = (new ObjectManager($this))->getObject('Magento\Tools\I18n\Parser\Adapter\Js');
    }

    public function testParse()
    {
        $expectedResult = array(
            array(
                'phrase' => 'Phrase 1',
                'file' => $this->_testFile,
                'line' => $this->_stringsCount - 2,
                'quote' => Phrase::QUOTE_SINGLE
            ),
            array(
                'phrase' => 'Phrase 2 %1',
                'file' => $this->_testFile,
                'line' => $this->_stringsCount - 1,
                'quote' => Phrase::QUOTE_DOUBLE
            )
        );

        $this->_adapter->parse($this->_testFile);

        $this->assertEquals($expectedResult, $this->_adapter->getPhrases());
    }
}
