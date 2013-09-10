<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Tools
 * @copyright  {copyright}
 * @license    {license_link}
 */

class Magento_Tools_Migration_Acl_FileManager
{
    /**
     * @param string $fileName
     * @param string $contents
     */
    public function write($fileName, $contents)
    {
        if (false == is_dir(dirname($fileName))) {
            mkdir(dirname($fileName), 0777, true);
        }
        file_put_contents($fileName, $contents);
    }

    /**
     * Remove file
     *
     * @param $fileName
     */
    public function remove($fileName)
    {
        unlink($fileName);
    }

    /**
     * Retrieve contents of a file
     *
     * @param string $fileName
     * @return string
     */
    public function getContents($fileName)
    {
        return file_get_contents($fileName);
    }
}
