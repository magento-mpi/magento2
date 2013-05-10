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
    private $_entryPoint;

    /**
     * @var Mage_Index_Model_EntryPoint_Shell_ErrorHandler
     */
    private $_errorHandler;

    /**
     * @param string $entryPoint filename of the entry point script
     * @param Mage_Core_Model_Config_Primary $config
     * @param Magento_ObjectManager $objectManager
     * @param Mage_Index_Model_EntryPoint_Shell_ErrorHandler $errorHandler
     */
    public function __construct(
        $entryPoint,
        Mage_Core_Model_Config_Primary $config,
        Magento_ObjectManager $objectManager = null,
        Mage_Index_Model_EntryPoint_Shell_ErrorHandler $errorHandler = null
    ) {
        parent::__construct($config, $objectManager);
        $this->_entryPoint = $entryPoint;
        $this->_errorHandler = !is_null($errorHandler)
            ? $errorHandler
            : new Mage_Index_Model_EntryPoint_Shell_ErrorHandler();
    }

    /**
     * Init object manager, configuring it with additional parameters
     */
    protected function _initObjectManager()
    {
        parent::_initObjectManager();

        $this->_objectManager->configure(array(
            'Mage_Index_Model_Shell' => array(
                'parameters' => array(
                    'entryPoint' => $this->_entryPoint,
                )
            )
        ));
    }

    /**
     * Process request to application
     *
     * @SuppressWarnings(PHPMD.ExitExpression)
     */
    protected function _processRequest()
    {
        /** @var $shell Mage_Index_Model_Shell */
        $shell = $this->_objectManager->create('Mage_Index_Model_Shell');
        $shell->run();
        if ($shell->hasErrors()) {
            $this->_errorHandler->terminate(1);
        }
    }
}
