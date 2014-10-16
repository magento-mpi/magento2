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
     * List of directory codes that require write permissions for installation
     *
     * @var array
     */
    protected $installationWritableDirectories = array(
        DirectoryList::CONFIG,
        DirectoryList::VAR_DIR,
        DirectoryList::MEDIA,
        DirectoryList::STATIC_VIEW,
    );

    /**
     * List of directory codes that require non-writable permissions for application
     *
     * @var array
     */
    protected $applicationNonWritableDirectories = array(
        DirectoryList::CONFIG,
    );

    /**
     * List of required writable directories for installation
     *
     * @var array
     */
    protected $installationRequired = [];

    /**
     * List of required non-writable directories for application
     *
     * @var
     */
    protected $applicationRequired = [];

    /**
     * List of current writable directories for installation
     *
     * @var array
     */
    protected $installationCurrent = [];

    /**
     * List of current non-writable directories for application
     *
     * @var array
     */
    protected $applicationCurrent = [];

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
    public function getInstallationRequired()
    {
        if (!$this->installationRequired) {
            foreach ($this->installationWritableDirectories as $code) {
                $this->installationRequired[$code] = $this->directoryList->getPath($code);
            }
        }
        return array_values($this->installationRequired);
    }

    /**
     * Retrieve list of required non-writable directories for application
     *
     * @return array
     */
    public function getApplicationRequired()
    {
        if (!$this->applicationRequired) {
            foreach ($this->applicationNonWritableDirectories as $code) {
                $this->applicationRequired[$code] = $this->directoryList->getPath($code);
            }
        }
        return array_values($this->applicationRequired);
    }

    /**
     * Retrieve list of currently writable directories for installation
     *
     * @param bool
     * @return array
     */
    public function getInstallationCurrent()
    {
        if (!$this->installationCurrent) {
            foreach ($this->installationRequired as $code => $path) {
                if (!$this->validate($code, true)) {
                    continue;
                }
                $this->installationCurrent[$code] = $path;
            }
        }
        return array_values($this->installationCurrent);
    }

    /**
     * Retrieve list of currently non-writable directories for application
     *
     * @param bool
     * @return array
     */
    public function getApplicationCurrent()
    {
        if (!$this->applicationCurrent) {
            foreach ($this->applicationRequired as $code => $path) {
                if (!$this->validate($code, false)) {
                    continue;
                }
                $this->applicationCurrent[$code] = $path;
            }
        }
        return array_values($this->applicationCurrent);
    }

    /**
     * Validate directory is writable/non-writable by given directory code
     *
     * @param string $code
     * @param bool $writable Flag indicating to check for writable/non-writable
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
     * Checks writable directories for installation
     *
     * @return array
     */
    public function verifyInstallation()
    {
        $required = $this->getInstallationRequired();
        $current = $this->getInstallationCurrent();
        return array_diff($required, $current);
    }

    /**
     * Checks non-writable directories for application
     *
     * @return array
     */
    public function verifyApplication()
    {
        $required = $this->getApplicationRequired();
        $current = $this->getApplicationCurrent();
        return array_diff($required, $current);
    }
}
