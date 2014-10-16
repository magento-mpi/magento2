<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Setup\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;

class FilePermissions
{

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var DirectoryList
     */
    protected $directoryList;

    /**
     * List of directories that require write permissions
     *
     * @var array
     */
    protected $writableDirectories = array(
        DirectoryList::CONFIG,
        DirectoryList::VAR_DIR,
        DirectoryList::MEDIA,
        DirectoryList::STATIC_VIEW,
    );

    /**
     * List of directories that require non-writable permissions after installation
     */
    protected $nonWritableDirectories = array(
        DirectoryList::CONFIG
    );

    /**
     * List of required directories
     *
     * @var array
     */
    protected $required = [];

    /**
     * @param FilesystemFactory  $filesystemFactory
     * @param DirectoryListFactory  $directoryListFactory
     */
    public function __construct(
        FilesystemFactory  $filesystemFactory,
        DirectoryListFactory  $directoryListFactory
    ) {
        $this->filesystem = $filesystemFactory->create();
        $this->directoryList = $directoryListFactory->create();
    }

    /**
     * Retrieve list of required directories
     *
     * @param bool
     * @return array
     */
    public function getRequired($writableDirectories = true)
    {
        $directories = $writableDirectories ? $this->writableDirectories : $this->nonWritableDirectories;
        $this->required = array();
        foreach ($directories as $code) {
            $this->required[$code] = $this->directoryList->getPath($code);
        }
        return array_values($this->required);
    }

    /**
     * Retrieve list of currently existed directories
     *
     * @param bool
     * @return array
     */
    public function getCurrent($writableDirectories = true)
    {
        $current = array();
        foreach ($this->required as $code => $path) {
            if (!$this->validate($code, $writableDirectories)) {
                continue;
            }
            $current[$code] = $path;
        }
        return array_values($current);
    }

    /**
     * Validate directory permissions by given directory code
     *
     * @param string $code
     * @param bool $writable
     * @return bool
     */
    protected function validate($code, $writable = true)
    {
        $directory = $this->filesystem->getDirectoryWrite($code);
        if ($writable) {
            if (!$directory->isExist() || !$directory->isDirectory() || !$directory->isReadable()
                || !$directory->isWritable()) {
                return false;
            }
        } else {
            if (!$directory->isExist() || !$directory->isDirectory() || !$directory->isReadable()
                || $directory->isWritable()) {
                return false;
            }
        }
        return true;
    }

    /**
     * Checks if has file permission or not
     *
     * @return array
     */
    public function getNonWritableDirs()
    {
        $required = $this->getRequired(true);
        $current = $this->getCurrent(true);
        return array_diff($required, $current);
    }

    /**
     * Checks for directories that are writable
     *
     * @return array
     */
    public function getWritableDirs()
    {
        $required = $this->getRequired(false);
        $current = $this->getCurrent(false);
        return array_diff($required, $current);
    }
}
