<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Tools
 * @copyright  {copyright}
 * @license    {license_link}
 */

class Magento_Tools_Migration_System_FileReader
{
    /**
     * Retrieve contents of a file
     *
     * @param string $fileName
     * @return string
     * @throws InvalidArgumentException
     */
    public function getContents($fileName)
    {
        if (false === file_exists($fileName)) {
            throw new InvalidArgumentException($fileName . ' does not exist');
        }
        return file_get_contents($fileName);
    }

    /**
     * Get file list
     *
     * @param string $pattern
     * @return array
     */
    public function getFileList($pattern)
    {
        return glob($pattern);
    }
}
