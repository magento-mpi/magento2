<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Input/output client interface
 *
 * @category   Varien
 * @package    Varien_Io
 * @author      Magento Core Team <core@magentocommerce.com>
 */
interface Varien_Io_Interface
{
    /**
     * Open a connection
     *
     * @param array $args
     *
     * @return
     */
    public function open(array $args = array());

    /**
     * Close a connection
     *
     */
    public function close();

    /**
     * Create a directory
     *
     * @param $dir
     * @param int $mode
     * @param bool $recursive
     *
     * @return
     */
    public function mkdir($dir, $mode = 0777, $recursive = true);

    /**
     * Delete a directory
     *
     * @param $dir
     * @param bool $recursive
     *
     * @return
     */
    public function rmdir($dir, $recursive = false);

    /**
     * Get current working directory
     *
     */
    public function pwd();

    /**
     * Change current working directory
     *
     * @param $dir
     *
     * @return
     */
    public function cd($dir);

    /**
     * Read a file
     *
     * @param $filename
     * @param null $dest
     *
     * @return
     */
    public function read($filename, $dest = null);

    /**
     * Write a file
     *
     * @param $filename
     * @param $src
     * @param null $mode
     *
     * @return
     */
    public function write($filename, $src, $mode = null);

    /**
     * Delete a file
     *
     * @param $filename
     *
     * @return
     */
    public function rm($filename);

    /**
     * Rename or move a directory or a file
     *
     * @param $src
     * @param $dest
     *
     * @return
     */
    public function mv($src, $dest);

    /**
     * Chamge mode of a directory or a file
     *
     * @param $filename
     * @param $mode
     *
     * @return
     */
    public function chmod($filename, $mode);

    /**
     * Get list of cwd subdirectories and files
     *
     * @param null $grep
     *
     * @return
     */
    public function ls($grep = null);

    /**
     * Retrieve directory separator in context of io resource
     *
     */
    public function dirsep();
}
