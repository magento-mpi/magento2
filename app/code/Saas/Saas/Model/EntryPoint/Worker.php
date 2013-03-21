<?php
/**
 * Worker entry point
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Saas_Model_EntryPoint_Worker extends Mage_Core_Model_EntryPointAbstract
{
    /**
     * Event area for worker observers
     */
    const WORKER_EVENT_AREA = 'workers';

    /**
     * Key of tasks node in config
     */
    const TASK_OPTIONS_KEY = 'worker_task_options';

    /**
     * Execute worker task(s)
     */
    public function processRequest()
    {
        /** @var $app Mage_Core_Model_App */
        $app = $this->_objectManager->get('Mage_Core_Model_App');
        $app->setUseSessionInUrl(false);
        $app->requireInstalledInstance();

        /**
         * @var Mage_Core_Model_Event_Manager $dispatcher
         */
        $dispatcher = $this->_objectManager->create('Mage_Core_Model_Event_Manager');
        $dispatcher->addEventArea(self::WORKER_EVENT_AREA);

        /** @var $primaryConfig Mage_Core_Model_Config_Primary */
        $primaryConfig = $this->_objectManager->get('Mage_Core_Model_Config_Primary');
        $taskOptions = $primaryConfig->getParam(self::TASK_OPTIONS_KEY);

        foreach ($taskOptions as $option) {
            if (!isset($option['task_name'], $option['params'])) {
                Mage::log(
                    sprintf('Incorrect task details. Task: %s.', $option['task_name']),
                    Zend_Log::WARN
                );
                continue;
            }
            $dispatcher->dispatch($option['task_name'], $option['params']);
        }
    }
}
