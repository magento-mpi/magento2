<?php
/**
 * Gearman task server client
 *
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_Queue_Client_Gearman implements Magento_Queue_ClientInterface
{
    /**
     * Adapted gearman client
     *
     * @var GearmanClient
     */
    protected $_adaptedClient;

    /**
     * Queue config
     *
     * @var Magento_Queue_Client_ConfigInterface
     */
    protected $_taskParams;

    /**
     * @param Magento_Queue_Client_ConfigInterface $config
     * @param GearmanClient $adaptedClient
     */
    public function __construct(Magento_Queue_Client_ConfigInterface $config, GearmanClient $adaptedClient = null)
    {
        $this->_adaptedClient = $adaptedClient ?: new GearmanClient();
        $this->_adaptedClient->addServers($config->getServers());
        $this->_taskParams = $config->getTaskParams();
    }

    /**
     * Add task to queue
     *
     * @param string $name
     * @param array $params
     * @param mixed $priority
     * @return string
     */
    public function addTask($name, $params, $priority = null)
    {
        if ($this->_taskParams) {
            $params = array_merge_recursive($this->_taskParams, $params);
        }

        switch ($priority) {
            case self::TASK_PRIORITY_HIGH:
                $priorityMethodName = 'doHighBackground';
                break;
            case self::TASK_PRIORITY_LOW:
                $priorityMethodName = 'doLowBackground';
                break;
            case self::TASK_PRIORITY_MIDDLE:
            default:
                $priorityMethodName = 'doBackground';
                break;
        }
        $this->_adaptedClient->$priorityMethodName($name, json_encode($params));
    }
}
