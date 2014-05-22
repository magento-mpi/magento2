<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for \Magento\Framework\File\Csv.
 */
namespace Magento\Framework\File;

class CsvTest extends \PHPUnit_Framework_TestCase
{

    public static $_fileExists;
    /**
     * Csv model
     *
     * @var \Magento\Framework\File\Csv
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = new \Magento\Framework\File\Csv;
    }

    protected function tearDown()
    {
        unset($this->_model);
    }

    public function testSetLineLength()
    {
        $this->assertInstanceOf('\Magento\Framework\File\Csv', $this->_model->setLineLength(4));
    }

    public function testSetDelimiter()
    {
        $this->assertInstanceOf('\Magento\Framework\File\Csv', $this->_model->setDelimiter(','));
    }

    public function testSetEnclosure()
    {
        $this->assertInstanceOf('\Magento\Framework\File\Csv', $this->_model->setEnclosure('"'));
    }

    public function testGetDataFileNotExists()
    {
        $file = 'FileName';
        $this->setExpectedException('\Exception', 'File "FileName" do not exists');
        $this->_model->getData($file);
    }

    public function testGetDataFileExists()
    {
        $file = 'FileExists';
        $this->assertTrue($this->_model->getData($file));
    }

    public function testGetDataPairs()
    {
        $keyIndex = 0;
        $valueIndex= 1;
        $file = 'fileName';

    }

    public function testSaveData()
    {

    }

    public function testFputcsv()
    {

    }

}

    function file_exists($fname)
    {
        return ($fname == 'FileExists') ? true : false;
    }

    function fopen()
    {
        return true;
    }

    function fgetcsv()
    {

    }

    function fclose()
    {
        return true;
    }