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

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage File "FileName" do not exists
     */
    public function testGetDataFileNonExistent()
    {
        $file = 'FileName';
        $this->_model->getData($file);
    }
}

function file_exists($fname)
{
    return ($fname == 'FileExists') ? true : false;
}
