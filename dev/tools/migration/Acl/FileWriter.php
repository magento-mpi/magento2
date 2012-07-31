<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    tools
 * @copyright  {copyright}
 * @license    {license_link}
 */

class Tools_Migration_Acl_FileWriter
{
    /**
     * @param string $fileName
     * @param string $contents
     */
    public function write($fileName, $contents)
    {
        file_put_contents($fileName, $contents);
    }
}
