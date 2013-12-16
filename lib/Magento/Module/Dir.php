<?php
/**
 * Encapsulates directories structure of a Magento module
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Module;

use Magento\Filesystem;

class Dir
{
    /**
     * Modules root directory
     *
     * @var \Magento\Filesystem\Directory\ReadInterface
     */
    protected $_modulesDirectory;

    /**
     * @var \Magento\Stdlib\String
     */
    protected $_string;

    /**
     * @param \Magento\Filesystem $filesystem
     * @param \Magento\Stdlib\String $string
     */
    public function __construct(Filesystem $filesystem, \Magento\Stdlib\String $string)
    {
        $this->_modulesDirectory = $filesystem->getDirectoryRead(Filesystem::MODULES);
        $this->_string = $string;
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
        $path = $this->_string->upperCaseWords($moduleName, '_', '/');
        if ($type) {
            if (!in_array($type, array('etc', 'sql', 'data', 'i18n', 'view'))) {
                throw new \InvalidArgumentException("Directory type '$type' is not recognized.");
            }
            $path .= '/' . $type;
        }

        $result = $this->_modulesDirectory->getAbsolutePath($path);

        return $result;
    }
}
