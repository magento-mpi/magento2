<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Magento_Convert Test Case for Magento_Convert_Excel Export
 */
class Magento_Convert_ExcelTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test data
     *
     * @var array
     */
    private $_testData = array(
        array('ID', 'Name', 'Email', 'Group', 'Telephone', 'ZIP', 'Country', 'State/Province'),
        array(1, 'Jon Doe', 'jon.doe@magento.com', 'General', '310-111-1111', 90232, 'United States', 'California')
    );

    /**
     * Path for Sample File
     *
     * @return string
     */
    protected function _getSampleOutputFile()
    {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'output.txt';
    }

    /**
     * Callback method
     *
     * @param  array $row
     * @return array
     */
    public function callbackMethod($row)
    {
        $data = array();
        foreach ($row as $value) {
            $data[] =  $value.'_TRUE_';
        }
        return $data;
    }

    /**
     * Test Magento_Convert_Excel->convert()
     * Magento_Convert_Excel($iterator)
     *
     * @return void
     */
    public function testConvert()
    {
        $convert = new Magento_Convert_Excel(new ArrayIterator($this->_testData));
        $isEqual = (file_get_contents($this->_getSampleOutputFile()) == $convert->convert());
        $this->assertTrue($isEqual, 'Failed asserting that data is the same.');
    }

    /**
     * Test Magento_Convert_Excel->convert()
     * Magento_Convert_Excel($iterator, $callbackMethod)
     *
     * @return void
     */
    public function testConvertCallback()
    {
        $convert = new Magento_Convert_Excel(new ArrayIterator($this->_testData), array($this, 'callbackMethod'));
        $this->assertContains('_TRUE_', $convert->convert(), 'Failed asserting that callback method is called.');
    }

    /**
     * Write Data into File
     *
     * @param bool $callback
     * @return string
     */
    protected function _writeFile($callback = false)
    {
        $ioFile = new Varien_Io_File();

        $path = Magento_Test_Environment::getInstance()->getTmpDir();
        $name = md5(microtime());
        $file = $path . DIRECTORY_SEPARATOR . $name . '.xml';

        $ioFile->open(array('path' => $path));
        $ioFile->streamOpen($file, 'w+');
        $ioFile->streamLock(true);

        if (!$callback) {
            $convert = new Magento_Convert_Excel(new ArrayIterator($this->_testData));
        } else {
            $convert = new Magento_Convert_Excel(new ArrayIterator($this->_testData), array($this, 'callbackMethod'));
        }

        $convert->write($ioFile);
        $ioFile->streamUnlock();
        $ioFile->streamClose();

        return $file;
    }

    /**
     * Test Magento_Convert_Excel->write()
     * Magento_Convert_Excel($iterator)
     *
     * @return void
     */
    public function testWrite()
    {
        $file = $this->_writeFile();
        $isEqual = (file_get_contents($file) == file_get_contents($this->_getSampleOutputFile()));
        $this->assertTrue($isEqual, 'Failed asserting that data from files is the same.');
    }

    /**
     * Test Magento_Convert_Excel->write()
     * Magento_Convert_Excel($iterator, $callbackMethod)
     *
     * @return void
     */
    public function testWriteCallback()
    {
        $file = $this->_writeFile(true);
        $this->assertContains('_TRUE_', file_get_contents($file), 'Failed asserting that callback method is called.');
    }
}
