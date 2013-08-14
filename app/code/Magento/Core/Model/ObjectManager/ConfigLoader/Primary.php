<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_Core_Model_ObjectManager_ConfigLoader_Primary
{
    /**
     * Directory manager
     *
     * @var Magento_Core_Model_Dir
     */
    protected $_dirs;

    /**
     * Application mode
     *
     * @var string
     */
    protected $_appMode;

    /**
     * @param Magento_Core_Model_Dir $dirs
     * @param string $appMode
     */
    public function __construct(Magento_Core_Model_Dir $dirs, $appMode = Magento_Core_Model_App_State::MODE_DEFAULT)
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
            glob($this->_dirs->getDir(Magento_Core_Model_Dir::CONFIG)
                . DIRECTORY_SEPARATOR . 'di' . DIRECTORY_SEPARATOR . '*'
            ),
            new Magento_ObjectManager_Config_Mapper_Dom(),
            $this->_appMode == Magento_Core_Model_App_State::MODE_DEVELOPER
        );
        return $reader->read();
    }
}
