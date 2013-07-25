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
     * Application mode
     *
     * @var string
     */
    protected $_appMode;

    /**
     * @param Mage_Core_Model_Dir $dirs
     * @param string $appMode
     */
    public function __construct(Mage_Core_Model_Dir $dirs, $appMode = Mage_Core_Model_App_State::MODE_DEFAULT)
    {
        $this->_dirs = $dirs;
        $this->_appMode = $appMode;
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
            ),
            new Magento_ObjectManager_Config_Mapper_Dom(),
            $this->_appMode == Mage_Core_Model_App_State::MODE_DEVELOPER
        );
        return $reader->read();
    }
}
