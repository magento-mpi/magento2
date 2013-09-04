<?php
/**
 * Gearman task server client
 *
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\JobQueue\Client;

class Gearman implements \Magento\JobQueue\ClientInterface
{
    /**
     * Adapted gearman client
     *
     * @var \GearmanClient
     */
    protected $_adaptedClient;

    /**
     * @param \Magento\JobQueue\Client\ConfigInterface $config
     * @param \GearmanClient $adaptedClient
     */
    public function __construct(\Magento\JobQueue\Client\ConfigInterface $config, \GearmanClient $adaptedClient = null)
    {
        $this->_adaptedClient = $adaptedClient ?: new \GearmanClient();
        $this->_adaptedClient->addServers($config->getServers());
    }

    /**
     * Add task to queue
     *
     * @param string $name
     * @param array $params
     * @param mixed $priority
     * @param string $uniqueId
     * @return string
     */
    public function addBackgroundTask($name, $params, $priority = null, $uniqueId = null)
    {
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
        return $this->_adaptedClient->$priorityMethodName($name, json_encode($params), $uniqueId);
    }

    /**
     * Retrieve task status
     *
     * @param $taskHandle
     * @return array (
     *     'isEnqueued' => bool
     *     'isRunning' => bool
     *     'percentage' => int
     * )
     */
    public function getStatus($taskHandle)
    {
        $status = $this->_adaptedClient->jobStatus($taskHandle);
        $result = array(
            'isEnqueued' => $status[0],
            'isRunning' => $status[1],
            'percentage' => 0
        );
        if ($status[1] && $status[3]) {
            $result['percentage'] = $status[2]/$status[3];
        }
        return $result;
    }
}
