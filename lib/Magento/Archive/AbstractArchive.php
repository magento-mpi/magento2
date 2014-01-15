<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Archive
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Class to work with archives
 *
 * @category    Magento
 * @package     Magento_Archive
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Archive;

class AbstractArchive
{
    /**
     * Write data to file. If file can't be opened - throw exception
     *
     * @param string $destination
     * @param string $data
     * @return boolean
     * @throws \Magento\Exception
     */
    protected function _writeFile($destination, $data)
    {
        $destination = trim($destination);
        if(false === file_put_contents($destination, $data)) {
            throw new \Exception("Can't write to file: " . $destination);
        }
        return true;
    }

    /**
     * Read data from file. If file can't be opened, throw to exception.
     *
     * @param string $source
     * @return string
     * @throws \Magento\Exception
     */
    protected function _readFile($source)
    {
        $data = '';
        if (is_file($source) && is_readable($source)) {
            $data = @file_get_contents($source);
            if ($data === false) {
                throw new \Magento\Exception("Can't get contents from: " . $source);
            }
        }
        return $data;
    }

    /**
     * Get file name from source (URI) without last extension.
     *
     * @param string $source
     * @param bool $withExtension
     * @return mixed|string
     */
    public function getFilename($source, $withExtension=false)
    {
        $file = str_replace(dirname($source) . '/', '', $source);
        if (!$withExtension) {
            $file = substr($file, 0, strrpos($file, '.'));
        }
        return $file;
    }
}
