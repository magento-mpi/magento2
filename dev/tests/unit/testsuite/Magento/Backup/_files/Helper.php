<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backup
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Mock Filesystem helper
 */
namespace Magento\Backup\Filesystem;

class Helper
{
    /**
     * Constant can be used in getInfo() function as second parameter.
     * Check whether directory and all files/sub directories are readable
     *
     * @const int
     */
    const INFO_READABLE  = 2;

    /**
     * Constant can be used in getInfo() function as second parameter.
     * Get directory size
     *
     * @const int
     */
    const INFO_SIZE      = 4;

    /**
     * Mock Get information (readable, writable, size) about $path
     *
     * @param $path
     * @param int $infoOptions
     * @param array $skipFiles
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getInfo($path, $infoOptions = self::INFO_ALL, $skipFiles = array())
    {
        return array('readable' => true, 'size' => 1);
    }
}