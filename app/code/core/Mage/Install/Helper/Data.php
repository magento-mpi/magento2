<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Install
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Install data helper
 */
class Mage_Install_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * The list of var children directories that have to be cleaned before the install
     *
     * @var array
     */
    protected $_varSubFolders;

    /**
     * @var Magento_Filesystem
     */
    protected $_filesystem;

    public function __construct(Magento_Filesystem $filesystem)
    {
        $this->_filesystem = $filesystem;
    }

    /**
     * Delete all service folders from var directory
     */
    public function cleanVarFolder()
    {
        foreach ($this->getVarSubFolders() as $folder) {
            $this->_filesystem->delete($folder);
        }
    }

    /**
     * Retrieve the list of service directories located in var folder
     *
     * @return array
     */
    public function getVarSubFolders()
    {
        if ($this->_varSubFolders == null) {
            $this->_varSubFolders = array(
                Mage::getConfig()->getTempVarDir() . DS . 'session',
                Mage::getConfig()->getTempVarDir() . DS . 'cache',
                Mage::getConfig()->getTempVarDir() . DS . 'locks',
                Mage::getConfig()->getTempVarDir() . DS . 'log',
                Mage::getConfig()->getTempVarDir() . DS . 'report',
                Mage::getConfig()->getTempVarDir() . DS . 'maps'
            );
        }
        return $this->_varSubFolders;
    }

    /**
     * Set the list of service directories located in var folder
     *
     * @param array $varSubFolders
     * @return Mage_Install_Helper_Data
     */
    public function setVarSubFolders(array $varSubFolders)
    {
        $this->_varSubFolders = $varSubFolders;
        return $this;
    }
}
