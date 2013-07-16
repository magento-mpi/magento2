<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
class Mage_Core_Model_ObjectManager_ConfigLoader_Primary
{
    /**
     * Directory manager
     *
     * @var Mage_Core_Model_Dir
     */
    protected $_dirs;

    /**
     * @param Mage_Core_Model_Dir $dirs
     */
    public function __construct(Mage_Core_Model_Dir $dirs)
    {
        $this->_dirs = $dirs;
    }

    /**
     * Retrieve merged configuration from primary config files
     *
     * @return array
     */
    public function load()
    {
        $reader = new Magento_ObjectManager_Config_Reader_Dom(
            glob($this->_dirs->getDir(Mage_Core_Model_Dir::CONFIG)
                . DIRECTORY_SEPARATOR . 'di' . DIRECTORY_SEPARATOR . '*'
        ));
        return $reader->read();
    }
}
