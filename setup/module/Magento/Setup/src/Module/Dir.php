<?php
/**
 * Encapsulates directories structure of a Magento module
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Setup\Module;

use Magento\Framework\Filesystem\Directory\ReadInterface;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Setup\Model\FilesystemFactory;

class Dir
{
    /**
     * Modules root directory
     *
     * @var ReadInterface
     */
    protected $_modulesDirectory;

    /**
     * @param FilesystemFactory $filesystemFactory
     */
    public function __construct(FilesystemFactory $filesystemFactory)
    {
        $this->_modulesDirectory = $filesystemFactory->create()->getDirectoryRead(DirectoryList::MODULES);
    }

    /**
     * Retrieve full path to a directory of certain type within a module
     *
     * @param string $moduleName Fully-qualified module name
     * @param string $type Type of module's directory to retrieve
     * @return string
     * @throws \InvalidArgumentException
     */
    public function getDir($moduleName, $type = '')
    {
        $path = str_replace(' ', '/', ucwords(str_replace('_', ' ', $moduleName)));
        if ($type) {
            if (!in_array($type, array('etc', 'sql', 'data', 'i18n', 'view'))) {
                throw new \InvalidArgumentException("Directory type '{$type}' is not recognized.");
            }
            $path .= '/' . $type;
        }

        $result = $this->_modulesDirectory->getAbsolutePath($path);
        return $result;
    }
}
