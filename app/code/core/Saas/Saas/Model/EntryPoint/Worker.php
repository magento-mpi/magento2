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
    const TASK_KEY = 'worker_task_options';

    /**
     * @var array
     */
    private $_params;

    /**
     * Memorize parameters for further reuse in some methods
     *
     * @param string $baseDir
     * @param array $params
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(
        $baseDir, array $params = array(), Magento_ObjectManager $objectManager = null
    ) {
        $this->_params = $params;
        parent::__construct($baseDir, $params, $objectManager);
    }

    /**
     * Execute worker task(s)
     */
    protected function _processRequest()
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
        foreach($this->_params[self::TASK_KEY] as $taskDetails) {
            if (!isset($taskDetails['task_name'], $taskDetails['params'])) {
                Mage::log(
                    sprintf('Incorrect task details. Task: %s.', $taskDetails['task_name']),
                    Zend_Log::WARN
                );
                continue;
            }
            $dispatcher->dispatch($taskDetails['task_name'], $taskDetails['params']);
        }
    }
}
