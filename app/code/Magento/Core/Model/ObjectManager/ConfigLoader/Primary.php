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
     * Application mode
     *
     * @var string
     */
    protected $_appMode;

    /**
     * @var Magento_Core_Model_Dir
     */
    protected $_dirs;

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
            new Magento_Core_Model_Config_FileResolver_Primary($this->_dirs),
            new Magento_ObjectManager_Config_Mapper_Dom(),
            new Magento_ObjectManager_Config_SchemaLocator(),
            new Magento_Core_Model_Config_ValidationState(new Magento_Core_Model_App_State($this->_appMode))
        );

        return $reader->read('primary');
    }
}
