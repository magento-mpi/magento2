<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Setup\Model;

use Magento\Framework\App\Filesystem\DirectoryList;

class FilePermissions
{
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
     * List of required directories
     *
     * @var array
     */
    protected $required = [];

    /**
     * List of currently existed directories
     *
     * @var array
     */
    protected $current = [];

    /**
     * @param DirectoryListFactory  $directoryListFactory
     */
    public function __construct(
        DirectoryListFactory  $directoryListFactory
    ) {
        $this->directoryList = $directoryListFactory->create();
    }

    /**
     * Retrieve list of required directories
     *
     * @return array
     */
    public function getRequired()
    {
        if (!$this->required) {
            foreach ($this->writableDirectories as $code) {
                $this->required[$code] = $this->directoryList->getPath($code);
            }
        }
        return array_values($this->required);
    }

    /**
     * Retrieve list of currently existed directories
     *
     * @return array
     */
    public function getCurrent()
    {
        if (!$this->current) {
            foreach ($this->required as $code => $path) {
                if (!$this->validate($path)) {
                    continue;
                }
                $this->current[$code] = $path;
            }
        }
        return array_values($this->current);
    }

    /**
     * Validate directory permissions by given directory code
     *
     * @param string $path
     * @return bool
     */
    protected function validate($path)
    {
        if (!file_exists($path) || !is_dir($path) || !is_readable($path) || !is_writable($path)) {
            return false;
        }
        return true;
    }

    /**
     * Checks if has file permission or not
     *
     * @return array
     */
    public function getDirsWithNoPermission()
    {
        $required = $this->getRequired();
        $current = $this->getCurrent();
        return array_diff($required, $current);
    }
}
