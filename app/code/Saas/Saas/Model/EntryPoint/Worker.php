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
     * Key of event name in task params
     */
    const EVENT_NAME_KEY = 'event_name';

    /**
     * Key of event area in task params
     */
    const EVENT_AREA_KEY = 'event_area';

    /**
     * Key of event data in task params
     */
    const EVENT_DATA_KEY = 'event_data';

    /**
     * Execute worker task(s)
     */
    protected function _processRequest()
    {
        /** @var $app Mage_Core_Model_App */
        $app = $this->_objectManager->get('Mage_Core_Model_App');
        $app->setUseSessionInUrl(false);
        $app->requireInstalledInstance();

        //Set default invoker.
        //All events fired through this dispatcher will be processed directly
        /**
         * @var Mage_Core_Model_Event_Manager $dispatcher
         */
        $dispatcher = $this->_objectManager->create(
            'Mage_Core_Model_Event_Manager',
            array('invoker' => 'Mage_Core_Model_Event_Invoker_InvokerDefault')
        );

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
            /** @var $taskName string */
            $taskName = $option['task_name'];

            if (array_key_exists(self::EVENT_NAME_KEY, $option['params'])) {
                // Use worker task as event transport if event name is provided
                if (!array_key_exists(self::EVENT_DATA_KEY, $option['params'])) {
                    $option['params'][self::EVENT_DATA_KEY] = array();
                }
                if (array_key_exists(self::EVENT_AREA_KEY, $option['params'])) {
                    $dispatcher->addEventArea($option['params'][self::EVENT_AREA_KEY]);
                }
                $eventName = $option['params'][self::EVENT_NAME_KEY];
                $eventData = $option['params'][self::EVENT_DATA_KEY];
            } else {
                // The logic below is kept for backward compatibility
                $dispatcher->addEventArea(self::WORKER_EVENT_AREA);
                $eventName = $taskName;
                $eventData = $option['params'];
            }
            // Dispatch synchronous event
            $dispatcher->dispatch($eventName, $eventData);
            $dispatcher->dispatch('job_complete', array('task_name' => $taskName));
        }
    }
}
