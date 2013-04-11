<?php
/**
 * Queue Gearman Adapter
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Enterprise_Queue_Model_Queue_Adapter_Gearman implements Enterprise_Queue_Model_Queue_AdapterInterface
{
    /**
     * @var Enterprise_Queue_Helper_Gearman
     */
    protected $_helperGearman;

    /**
     * @var GearmanClient
     */
    protected $_client;

    /**
     * @param Enterprise_Queue_Helper_Gearman $helperGearman
     * @param GearmanClient|null $client
     */
    public function __construct(Enterprise_Queue_Helper_Gearman $helperGearman, GearmanClient $client = null)
    {
        $this->_helperGearman = $helperGearman;

        if (!$client) {
            $client = new GearmanClient();
        }
        $client->addServers($this->_helperGearman->getServers());
        $this->_client = $client;
    }

    /**
     * Add task to queue
     *
     * @param string $eventName
     * @param array $data
     * @param string|null $priority
     * @return Enterprise_Queue_Model_Queue_AdapterInterface
     */
    public function addTask($eventName, $data, $priority = null)
    {
        $this->_client->doBackground($eventName, $this->_helperGearman->encodeData($data));

        return $this;
    }
}
