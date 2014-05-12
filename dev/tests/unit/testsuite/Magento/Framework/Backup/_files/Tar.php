<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Mock class to work with tar archives
 */
namespace Magento\Framework\Backup\Archive;

class Tar
{
    /**
     * Mock set files that shouldn't be added to tarball
     *
     * @param array $skipFiles
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setSkipFiles(array $skipFiles)
    {
        return $this;
    }

    /**
     * Mock pack file to TAR (Tape Archiver).
     *
     * @param $source
     * @param $destination
     * @param bool $skipRoot
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function pack($source, $destination, $skipRoot = false)
    {
        return '\unexistingpath';
    }
}
