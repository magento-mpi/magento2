<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\I18n\Code\Dictionary\Writer;

class CsvTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $_testFile;

    protected function setUp()
    {
        $this->_testFile = str_replace('\\', '/', realpath(dirname(__FILE__) . '/../../..')) . '/_files/test.csv';
    }

    public function tearDown()
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
        $objectManagerHelper = new \Magento_Test_Helper_ObjectManager($this);
        $objectManagerHelper->getObject('Magento\Tools\I18n\Code\Dictionary\Writer\Csv', array(
            'outputFilename' => 'wrong/path',
        ));
    }

    public function testWrite()
    {
        $objectManagerHelper = new \Magento_Test_Helper_ObjectManager($this);
        /** @var \Magento\Tools\I18n\Code\Dictionary\Writer\Csv $writer */
        $writer = $objectManagerHelper->getObject('Magento\Tools\I18n\Code\Dictionary\Writer\Csv', array(
            'outputFilename' => $this->_testFile,
        ));

        $writer->write(array('field1', 'field2', 'field3'));
        $writer->write(array('field4', 'field5', 'field6'));

        $expected = "field1,field2,field3\nfield4,field5,field6\n";

        $this->assertEquals($expected, file_get_contents($this->_testFile));
    }
}
