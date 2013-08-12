<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_JobNotification_Model_Inbox
{
    /**
     * @var Saas_JobNotification_Model_NotificationFactory
     */
    protected $_factory;

    /**
     * @var Saas_JobNotification_Model_Config
     */
    protected $_config;

    /**
     * @param Saas_JobNotification_Model_NotificationFactory $factory
     * @param Saas_JobNotification_Model_Config $config
     */
    public function __construct(
        Saas_JobNotification_Model_NotificationFactory $factory,
        Saas_JobNotification_Model_Config $config
    ) {
        $this->_factory = $factory;
        $this->_config = $config;
    }

    /**
     * Add notification
     *
     * @param Magento_Event_Observer $observer
     */
    public function addNotification(Magento_Event_Observer $observer)
    {
        $taskName = $observer->getEvent()->getTaskName();
        if ($this->_config->isNotificationAllowed($taskName)) {
            $data = array(
                'task_name'  => $taskName,
                'title'      => $this->_config->getJobTitle($taskName),
            );
            $notification = $this->_factory->create();
            $notification->addData($data);
            $notification->save();
        }
    }
}