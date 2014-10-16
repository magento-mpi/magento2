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
     * List of directory codes that require write permissions in pre-install
     *
     * @var array
     */
    protected $preInstallWritableDirectories = array(
        DirectoryList::CONFIG,
        DirectoryList::VAR_DIR,
        DirectoryList::MEDIA,
        DirectoryList::STATIC_VIEW,
    );

    /**
     * List of directory codes that require non-writable permissions in post-install
     *
     * @var array
     */
    protected $postInstallNonWritableDirectories = array(
        DirectoryList::CONFIG,
    );

    /**
     * List of required writable directories in pre-install
     *
     * @var array
     */
    protected $preInstallRequired = [];

    /**
     * List of required non-writable directories in post-install
     *
     * @var
     */
    protected $postInstallRequired = [];

    /**
     * List of current writable directories in pre-install
     *
     * @var array
     */
    protected $preInstallCurrent = [];

    /**
     * List of current non-writable directories in post-install
     *
     * @var array
     */
    protected $postInstallCurrent = [];

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
     * Retrieve list of required writable directories in pre-install
     *
     * @return array
     */
    public function getPreInstallRequired()
    {
        if (!$this->preInstallRequired) {
            foreach ($this->preInstallWritableDirectories as $code) {
                $this->preInstallRequired[$code] = $this->directoryList->getPath($code);
            }
        }
        return array_values($this->preInstallRequired);
    }

    /**
     * Retrieve list of required non-writable directories in post-install
     *
     * @return array
     */
    public function getPostInstallRequired()
    {
        if (!$this->postInstallRequired) {
            foreach ($this->postInstallNonWritableDirectories as $code) {
                $this->postInstallRequired[$code] = $this->directoryList->getPath($code);
            }
        }
        return array_values($this->postInstallRequired);
    }

    /**
     * Retrieve list of currently writable directories in pre-install
     *
     * @param bool
     * @return array
     */
    public function getPreInstallCurrent()
    {
        if (!$this->preInstallCurrent) {
            foreach ($this->preInstallRequired as $code => $path) {
                if (!$this->validate($code, true)) {
                    continue;
                }
                $this->preInstallCurrent[$code] = $path;
            }
        }
        return array_values($this->preInstallCurrent);
    }

    /**
     * Retrieve list of currently non-writable directories in post-install
     *
     * @param bool
     * @return array
     */
    public function getPostInstallCurrent()
    {
        if (!$this->postInstallCurrent) {
            foreach ($this->postInstallRequired as $code => $path) {
                if (!$this->validate($code, false)) {
                    continue;
                }
                $this->postInstallCurrent[$code] = $path;
            }
        }
        return array_values($this->postInstallCurrent);
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
     * Checks writable directories in pre-install
     *
     * @return array
     */
    public function verifyPreInstall()
    {
        $required = $this->getPreInstallRequired();
        $current = $this->getPreInstallCurrent();
        return array_diff($required, $current);
    }

    /**
     * Checks non-writable directories in post-install
     *
     * @return array
     */
    public function verifyPostInstall()
    {
        $required = $this->getPostInstallRequired();
        $current = $this->getPostInstallCurrent();
        return array_diff($required, $current);
    }
}
