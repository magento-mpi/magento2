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
     * List of required writable directories for installation
     *
     * @var array
     */
    protected $installationWritableDirectories = [];

    /**
     * List of recommended non-writable directories for application
     *
     * @var array
     */
    protected $applicationNonWritableDirectories = [];

    /**
     * List of current writable directories for installation
     *
     * @var array
     */
    protected $installationCurrentWritableDirectories = [];

    /**
     * List of current non-writable directories for application
     *
     * @var array
     */
    protected $applicationCurrentNonWritableDirectories = [];

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
     * Retrieve list of required writable directories for installation
     *
     * @return array
     */
    public function getInstallationWritableDirectories()
    {
        if (!$this->installationWritableDirectories) {
            $data = array(
                DirectoryList::CONFIG,
                DirectoryList::VAR_DIR,
                DirectoryList::MEDIA,
                DirectoryList::STATIC_VIEW
            );
            foreach ($data as $code) {
                $this->installationWritableDirectories[$code] = $this->directoryList->getPath($code);
            }
        }
        return array_values($this->installationWritableDirectories);
    }

    /**
     * Retrieve list of recommended non-writable directories for application
     *
     * @return array
     */
    public function getApplicationNonWritableDirectories()
    {
        if (!$this->applicationNonWritableDirectories) {
            $data = array(
                DirectoryList::CONFIG
            );
            foreach ($data as $code) {
                $this->applicationNonWritableDirectories[$code] = $this->directoryList->getPath($code);
            }
        }
        return array_values($this->applicationNonWritableDirectories);
    }

    /**
     * Retrieve list of currently writable directories for installation
     *
     * @param bool
     * @return array
     */
    public function getInstallationCurrentWritableDirectories()
    {
        if (!$this->installationCurrentWritableDirectories) {
            foreach ($this->installationWritableDirectories as $code => $path) {
                if ($this->isWritable($code)) {
                    $this->installationCurrentWritableDirectories[] = $path;
                }
            }
        }
        return $this->installationCurrentWritableDirectories;
    }

    /**
     * Retrieve list of currently non-writable directories for application
     *
     * @param bool
     * @return array
     */
    public function getApplicationCurrentNonWritableDirectories()
    {
        if (!$this->applicationCurrentNonWritableDirectories) {
            foreach ($this->applicationNonWritableDirectories as $code => $path) {
                if ($this->isNonWritable($code)) {
                    $this->applicationCurrentNonWritableDirectories[] = $path;
                }
            }
        }
        return $this->applicationCurrentNonWritableDirectories;
    }

    /**
     * Checks if directory is writable by given directory code
     *
     * @param string $code
     * @return bool
     */
    protected function isWritable($code)
    {
        $directory = $this->filesystem->getDirectoryWrite($code);
        return $this->isReadableDirectory($directory) && $directory->isWritable();
    }

    /**
     * Checks if directory is non-writable by given directory code
     *
     * @param string $code
     * @return bool
     */
    protected function isNonWritable($code)
    {
        $directory = $this->filesystem->getDirectoryWrite($code);
        return $this->isReadableDirectory($directory) && !$directory->isWritable();
    }

    /**
     * Checks if directory exists and is readable
     *
     * @param \Magento\Framework\Filesystem\Directory\WriteInterface $directory
     * @return bool
     */
    protected function isReadableDirectory($directory)
    {
        if (!$directory->isExist() || !$directory->isDirectory() || !$directory->isReadable()) {
            return false;
        }
        return true;
    }

    /**
     * Checks writable directories for installation
     *
     * @return array
     */
    public function getMissingWritableDirectoriesForInstallation()
    {
        $required = $this->getInstallationWritableDirectories();
        $current = $this->getInstallationCurrentWritableDirectories();
        return array_diff($required, $current);
    }

    /**
     * Checks non-writable directories for application
     *
     * @return array
     */
    public function getUnnecessaryWritableDirectoriesForApplication()
    {
        $required = $this->getApplicationNonWritableDirectories();
        $current = $this->getApplicationCurrentNonWritableDirectories();
        return array_diff($required, $current);
    }
}
