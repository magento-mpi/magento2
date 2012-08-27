<?php
/**
 * {license_notice}
 *
 * @category    Varien
 * @package     Varien_Io
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Varien_Io_File test case
 */
class Varien_Io_FileTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var string $_dir
     */
    protected $_dir;

    /**
     * @var string $_file
     */
    protected $_file;
    
    public function testChmodRecursive()
    {
        try {
            $this->_prepare();
            if (substr(PHP_OS, 0, 3) == 'WIN') {
                $permsBefore = 0600;
                $expected = 0666;
            } else {
                $permsBefore = 0700;
                $expected = 0777;
            }
            $this->assertEquals($permsBefore, fileperms($this->_dir) & $permsBefore,
                "Wrong permissions set for " . $this->_dir);
            $this->assertEquals($permsBefore, fileperms($this->_file) & $permsBefore,
                "Wrong permissions set for " . $this->_file);
            Varien_Io_File::chmodRecursive($this->_dir, $expected);
            $this->assertEquals($expected, fileperms($this->_dir) & $expected,
                "Directory permissions were changed incorrectly.");
            $this->assertEquals($expected, fileperms($this->_file) & $expected,
                "File permissions were changed incorrectly.");
        } catch (Exception $e) {
        }

        $this->_cleanup();
        if (isset($e)) {
            throw $e;
        }
    }

    public function testRmdirRecursive()
    {
        try {
            $this->_prepare();
            $this->assertFileExists($this->_file);
            Varien_Io_File::rmdirRecursive($this->_dir);
            $this->assertFileNotExists($this->_dir);
        } catch (Exception $e) {
        }

        $this->_cleanup();
        if (isset($e)) {
            throw $e;
        }
    }

    /**
     * Create files for tests
     */
    protected function _prepare()
    {
        $this->_dir = __DIR__ . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'directory';
        $this->_file = $this->_dir . DIRECTORY_SEPARATOR . 'file.txt';
        @mkdir($this->_dir, 0700, true);
        if (@touch($this->_file)) {
            chmod($this->_file, 0700);
        }
    }

    /**
     * Remove fixture files
     */
    protected function _cleanup()
    {
        if (file_exists($this->_file)) {
            @unlink($this->_file);
        }
        if (file_exists($this->_dir)) {
            @rmdir($this->_dir);
        }
    }
}
