<?php
/**
 * Test for \Magento\Filesystem\Adapter\Zlib
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Filesystem_Adapter_ZlibTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Filesystem\Adapter\Zlib
     */
    protected $_adapter;

    /**
     * @var array
     */
    protected $_deleteFiles = array();

    protected function setUp()
    {
        $this->_adapter = new \Magento\Filesystem\Adapter\Zlib();
    }

    protected function tearDown()
    {
        foreach ($this->_deleteFiles as $fileName) {
            if (is_dir($fileName)) {
                rmdir($fileName);
            } elseif (is_file($fileName)) {
                unlink($fileName);
            }
        }
    }

    public function testCreateStream()
    {
        $file = $this->_getFixturesPath() . 'data.csv';
        $this->assertInstanceOf('\Magento\Filesystem\Stream\Zlib', $this->_adapter->createStream($file));
    }

    public function testRW()
    {
        $file = $this->_getFixturesPath() . 'compressed.tgz';
        $this->_adapter->write($file, 'Test string');
        $this->assertFileExists($file);
        $this->_deleteFiles[] = $file;
        $this->assertEquals('Test string', $this->_adapter->read($file));
    }

    /**
     * @return string
     */
    protected function _getFixturesPath()
    {
        return __DIR__ . DS . '..' . DS . '_files' . DS;
    }
}
