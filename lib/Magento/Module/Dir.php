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

class Dir
{
    /**
     * Directory registry
     *
     * @var \Magento\App\Dir
     */
    protected $_applicationDirs;

    /**
     * @var \Magento\Stdlib\String
     */
    protected $_string;

    /**
     * @param \Magento\App\Dir $applicationDirs
     * @param \Magento\Stdlib\String $string
     */
    public function __construct(\Magento\App\Dir $applicationDirs, \Magento\Stdlib\String $string)
    {
        $this->_applicationDirs = $applicationDirs;
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
        $result = $this->_applicationDirs->getDir(\Magento\App\Dir::MODULES)
            . DIRECTORY_SEPARATOR
            . $this->_string->upperCaseWords($moduleName, '_', DIRECTORY_SEPARATOR);
        if ($type) {
            if (!in_array($type, array('etc', 'sql', 'data', 'i18n', 'view'))) {
                throw new \InvalidArgumentException("Directory type '$type' is not recognized.");
            }
            $result .= DIRECTORY_SEPARATOR . $type;
        }
        return $result;
    }
}
