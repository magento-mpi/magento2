<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    tools
 * @copyright  {copyright}
 * @license    {license_link}
 */

interface Tools_Migration_System_WriterInterface
{
    /**
     * @param string $fileName
     * @param string $contents
     */
    public function write($fileName, $contents);

    /**
     * Remove file
     *
     * @param $fileName
     */
    public function remove($fileName);
}
