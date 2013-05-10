<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Log_Model_EntryPoint_Shell extends Mage_Core_Model_EntryPointAbstract
{
    /**
     * Filename of the entry point script
     *
     * @var string
     */
    private $_entryFileName;

    /**
     * @param Mage_Core_Model_Config_Primary $config
     * @param string $entryFileName  filename of the entry point script
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(
        Mage_Core_Model_Config_Primary $config,
        $entryFileName,
        Magento_ObjectManager $objectManager = null
    ) {
        parent::__construct($config, $objectManager);
        $this->_entryFileName = $entryFileName;
    }

    /**
     * Process request to application
     */
    protected function _processRequest()
    {
        /** @var $shell Mage_Log_Model_Shell */
        $shell = $this->_objectManager->create('Mage_Log_Model_Shell', array('entryPoint' => $this->_entryFileName));
        $shell->run();
    }
}
