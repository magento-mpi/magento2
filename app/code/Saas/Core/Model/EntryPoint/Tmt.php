<?php
/**
 * Tmt entry point
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Core_Model_EntryPoint_Tmt extends Mage_Core_Model_EntryPointAbstract
{
    const TENANT_COMMAND_NAMESPACE = 'Saas_Saas_Model_Tenant_Command_';

    /**
     * Params set for request processing
     *
     * @var array
     */
    protected $_params = array();

    /**
     * @param Mage_Core_Model_Config_Primary $config
     * @param Magento_ObjectManager $objectManager
     * @param array $params
     */
    public function __construct(
        Mage_Core_Model_Config_Primary $config,
        Magento_ObjectManager $objectManager = null,
        $params = array()
    ) {
        parent::__construct($config, $objectManager);
        $this->_params = $params;
    }

    /**
     * Process TMT request
     */
    public function processRequest()
    {
        $this->_objectManager->get('Mage_Core_Model_App')
            ->loadArea(Mage_Core_Model_App_Area::AREA_ADMINHTML);

        $command = $this->_params['command'];
        $commandProcessorName = self::TENANT_COMMAND_NAMESPACE . $command;

        if (class_exists($commandProcessorName, true)) {
            $commandProcessor = $this->_objectManager->create($commandProcessorName);
            $commandProcessor->execute($this->_params);
        } else {
            throw new Exception('Tenant command processor for \'' . $command . '\' was not found.');
        }
    }
}
