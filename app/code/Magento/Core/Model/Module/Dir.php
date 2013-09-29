<?php
/**
 * Encapsulates directories structure of a Magento module
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Module;

class Dir
{
    /**
     * Directory registry
     *
     * @var \Magento\Core\Model\Dir
     */
    protected $_applicationDirs;

    /**
     * @param \Magento\Core\Model\Dir $applicationDirs
     */
    public function __construct(\Magento\Core\Model\Dir $applicationDirs)
    {
        $this->_applicationDirs = $applicationDirs;
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
        $result = $this->_applicationDirs->getDir(\Magento\Core\Model\Dir::MODULES)
            . DIRECTORY_SEPARATOR
            . uc_words($moduleName, DIRECTORY_SEPARATOR);
        if ($type) {
            if (!in_array($type, array('etc', 'sql', 'data', 'i18n', 'view'))) {
                throw new \InvalidArgumentException("Directory type '$type' is not recognized.");
            }
            $result .= '/' . $type;
        }
        return $result;
    }
}
