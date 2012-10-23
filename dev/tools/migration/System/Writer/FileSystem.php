<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    tools
 * @copyright  {copyright}
 * @license    {license_link}
 */

class Tools_Migration_System_Writer_FileSystem implements Tools_Migration_System_WriterInterface
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
}
