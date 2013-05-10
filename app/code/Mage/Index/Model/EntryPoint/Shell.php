<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Index_Model_EntryPoint_Shell extends Mage_Core_Model_EntryPointAbstract
{
    /**
     * Filename of the entry point script
     *
     * @var string
     */
    protected $_entryFileName;

    /**
     * @var Mage_Index_Model_EntryPoint_Shell_ErrorHandler
     */
    protected $_errorHandler;

    /**
     * @param string $entryFileName filename of the entry point script
     * @param Mage_Index_Model_EntryPoint_Shell_ErrorHandler $errorHandler
     * @param Mage_Core_Model_Config_Primary $config
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(
        $entryFileName,
        Mage_Index_Model_EntryPoint_Shell_ErrorHandler $errorHandler,
        Mage_Core_Model_Config_Primary $config,
        Magento_ObjectManager $objectManager = null
    ) {
        parent::__construct($config, $objectManager);
        $this->_entryFileName = $entryFileName;
        $this->_errorHandler = $errorHandler;
    }

    /**
     * Process request to application
     */
    protected function _processRequest()
    {
        /** @var $shell Mage_Index_Model_Shell */
        $shell = $this->_objectManager->create('Mage_Index_Model_Shell', array('entryPoint' => $this->_entryFileName));
        $shell->run();
        if ($shell->hasErrors()) {
            $this->_errorHandler->terminate(1);
        }
    }
}
