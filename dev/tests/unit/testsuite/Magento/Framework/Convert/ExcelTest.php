<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Magento_Convert Test Case for \Magento\Framework\Convert\Excel Export
 */
namespace Magento\Framework\Convert;

class ExcelTest extends \PHPUnit_Framework_TestCase
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

    protected $_testHeader = array(
        'HeaderID', 'HeaderName', 'HeaderEmail', 'HeaderGroup', 'HeaderPhone', 'HeaderZIP',
        'HeaderCountry', 'HeaderRegion',
    );

    protected $_testFooter = array(
        'FooterID', 'FooterName', 'FooterEmail', 'FooterGroup', 'FooterPhone', 'FooterZIP',
        'FooterCountry', 'FooterRegion',
    );

    /**
     * Path for Sample File
     *
     * @return string
     */
    protected function _getSampleOutputFile()
    {
        return __DIR__ . '/_files/output.txt';
    }

    /**
     * Callback method
     *
     * @param array $row
     * @return array
     */
    public function callbackMethod($row)
    {
        $data = array();
        foreach ($row as $value) {
            $data[] = $value . '_TRUE_';
        }
        return $data;
    }

    /**
     * Test \Magento\Framework\Convert\Excel->convert()
     * \Magento\Framework\Convert\Excel($iterator)
     *
     * @return void
     */
    public function testConvert()
    {
        $convert = new \Magento\Framework\Convert\Excel(new \ArrayIterator($this->_testData));
        $convert->setDataHeader($this->_testHeader);
        $convert->setDataFooter($this->_testFooter);
        $isEqual = (file_get_contents($this->_getSampleOutputFile()) == $convert->convert());
        $this->assertTrue($isEqual, 'Failed asserting that data is the same.');
    }

    /**
     * Test \Magento\Framework\Convert\Excel->convert()
     * \Magento\Framework\Convert\Excel($iterator, $callbackMethod)
     *
     * @return void
     */
    public function testConvertCallback()
    {
        $convert = new \Magento\Framework\Convert\Excel(
            new \ArrayIterator($this->_testData),
            array($this, 'callbackMethod')
        );
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
        $name = md5(microtime());
        $file = TESTS_TEMP_DIR . '/' . $name . '.xml';

        $stream = new \Magento\Framework\Filesystem\File\Write(
            $file,
            new \Magento\Framework\Filesystem\Driver\File(),
            'w+'
        );
        $stream->lock();

        if (!$callback) {
            $convert = new \Magento\Framework\Convert\Excel(new \ArrayIterator($this->_testData));
            $convert->setDataHeader($this->_testHeader);
            $convert->setDataFooter($this->_testFooter);
        } else {
            $convert = new \Magento\Framework\Convert\Excel(
                new \ArrayIterator($this->_testData),
                array($this, 'callbackMethod')
            );
        }

        $convert->write($stream);
        $stream->unlock();
        $stream->close();

        return $file;
    }

    /**
     * Test \Magento\Framework\Convert\Excel->write()
     * \Magento\Framework\Convert\Excel($iterator)
     *
     * @return void
     */
    public function testWrite()
    {
        $file = $this->_writeFile();
        $isEqual = file_get_contents($file) == file_get_contents($this->_getSampleOutputFile());
        $this->assertTrue($isEqual, 'Failed asserting that data from files is the same.');
    }

    /**
     * Test \Magento\Framework\Convert\Excel->write()
     * \Magento\Framework\Convert\Excel($iterator, $callbackMethod)
     *
     * @return void
     */
    public function testWriteCallback()
    {
        $file = $this->_writeFile(true);
        $this->assertContains('_TRUE_', file_get_contents($file), 'Failed asserting that callback method is called.');
    }
}
