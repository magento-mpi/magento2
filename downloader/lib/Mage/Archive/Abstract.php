<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Archive
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Class to work with archives
 *
 * @category    Mage
 * @package     Mage_Archive
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Archive_Abstract
{
    /**
     * Write data to file. If file can't be opened,
     *
     * @param string $destination
     * @param string $data
     * @return boolean
     */
    protected function _writeFile($destination, $data)
    {
        if(false === file_put_contents($destination, $data)) {
            throw new Mage_Exception("Can't write to file: " . $destination);
        }
        return true;
    }

    /**
     * Read data from file. If file can't be opened, throw to exception.
     *
     * @param string $source
     * @return string
     */
    protected function _readFile($source)
    {
        $data = '';
        if (is_file($source) && is_readable($source)) {
            $data = @file_get_contents($source);
            if ($data === false) {
                throw new Mage_Exception("Can't get contents from: " . $source);
            }
        }
        return $data;
    }

    /**
     * Get file name from source (URI) without last extension.
     *
     * @param string $source
     * @return string
     */
    public function getFilename($source, $withExtension=false)
    {
        $file = str_replace(dirname($source) . DS, '', $source);
        if (!$withExtension) {
            $file = substr($file, 0, strrpos($file, '.'));
        }
        return $file;
    }

}
