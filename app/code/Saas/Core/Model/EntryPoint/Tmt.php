<?php
/**
 * Tmt entry point
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Core_Model_EntryPoint_Tmt extends Magento_Core_Model_EntryPointAbstract
{
    const TENANT_COMMAND_NAMESPACE = 'Saas_Saas_Model_Tenant_Command_';

    /**
     * Params set for request processing
     *
     * @var array
     */
    protected $_params = array();

    /**
     * @param Magento_Core_Model_Config_Primary $config
     * @param Magento_ObjectManager $objectManager
     * @param array $params
     */
    public function __construct(
        Magento_Core_Model_Config_Primary $config,
        Magento_ObjectManager $objectManager = null,
        $params = array()
    ) {
        parent::__construct($config, $objectManager);
        $this->_params = $params;
    }

    /**
     * Process TMT request
     */
    protected function _processRequest()
    {
        $this->_objectManager->get('Magento_Core_Model_App')
            ->loadArea(Magento_Core_Model_App_Area::AREA_ADMINHTML);

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
