<?php
/**
 * {license_notice}
 *
 * @category     Magento
 * @package      Magento_Backup
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backup\Filesystem;

/**
 * Filesystem helper
 *
 * @category    Magento
 * @package     Magento_Backup
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Helper
{
    /**
     * Constant can be used in getInfo() function as second parameter.
     * Check whether directory and all files/sub directories are writable
     *
     * @const int
     */
    const INFO_WRITABLE = 1;

    /**
     * Constant can be used in getInfo() function as second parameter.
     * Check whether directory and all files/sub directories are readable
     *
     * @const int
     */
    const INFO_READABLE = 2;

    /**
     * Constant can be used in getInfo() function as second parameter.
     * Get directory size
     *
     * @const int
     */
    const INFO_SIZE = 4;

    /**
     * Constant can be used in getInfo() function as second parameter.
     * Combination of INFO_WRITABLE, INFO_READABLE, INFO_SIZE
     *
     * @const int
     */
    const INFO_ALL = 7;

    /**
     * Recursively delete $path
     *
     * @param string $path
     * @param array $skipPaths
     * @param bool $removeRoot
     * @return void
     * @throws \Magento\Exception
     */
    public function rm($path, $skipPaths = array(), $removeRoot = false)
    {
        $filesystemIterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        $iterator = new \Magento\Backup\Filesystem\Iterator\Filter($filesystemIterator, $skipPaths);

        foreach ($iterator as $item) {
            $item->isDir() ? @rmdir($item->__toString()) : @unlink($item->__toString());
        }

        if ($removeRoot && is_dir($path)) {
            @rmdir($path);
        }
    }

    /**
     * Get information (readable, writable, size) about $path
     *
     * @param string $path
     * @param int $infoOptions
     * @param array $skipFiles
     * @return array
     */
    public function getInfo($path, $infoOptions = self::INFO_ALL, $skipFiles = array())
    {
        $info = array();
        if ($infoOptions & self::INFO_READABLE) {
            $info['readable'] = true;
        }

        if ($infoOptions & self::INFO_WRITABLE) {
            $info['writable'] = true;
        }

        if ($infoOptions & self::INFO_SIZE) {
            $info['size'] = 0;
        }

        $filesystemIterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        $iterator = new \Magento\Backup\Filesystem\Iterator\Filter($filesystemIterator, $skipFiles);

        foreach ($iterator as $item) {
            if ($item->isLink()) {
                continue;
            }

            if ($infoOptions & self::INFO_WRITABLE && !$item->isWritable()) {
                $info['writable'] = false;
            }

            if ($infoOptions & self::INFO_READABLE && !$item->isReadable()) {
                $info['readable'] = false;
            }

            if ($infoOptions & self::INFO_SIZE && !$item->isDir()) {
                $info['size'] += $item->getSize();
            }
        }

        return $info;
    }
}
