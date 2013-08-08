<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_JobNotification_Service_Notification
{
    /**
     * Job notification model factory
     *
     * @var Saas_JobNotification_Model_NotificationFactory
     */
    protected $_factory;

    /**
     * Job Notification helper
     *
     * @var Saas_JobNotification_Helper_Data
     */
    protected $_helper;

    /**
     * @param Saas_JobNotification_Model_NotificationFactory $factory
     * @param Saas_JobNotification_Helper_Data $helper
     */
    public function __construct(
        Saas_JobNotification_Model_NotificationFactory $factory,
        Saas_JobNotification_Helper_Data $helper
    ) {
        $this->_factory = $factory;
        $this->_helper = $helper;
    }

    /**
     * Update specified notification
     *
     * @param string $notificationId
     * @param array $data
     * @throws InvalidArgumentException
     * @throws Saas_JobNotification_Service_Exception
     */
    public function update($notificationId, array $data = array())
    {
        $model = $this->_factory->create();
        $model->load($notificationId);

        if (false == $model->getId()) {
            throw new InvalidArgumentException($this->_helper->__('Invalid notification id'));
        }

        try {
            $model->addData($data);
            $model->save();
        } catch (Magento_Core_Exception $exception) {
            throw new Saas_JobNotification_Service_Exception($exception->getMessage());
        }
    }

    /**
     * Mass notifications update
     *
     * @param array $notificationIds
     * @param array $data
     * @throws InvalidArgumentException
     */
    public function massUpdate($notificationIds, array $data = array())
    {
        if (false == is_array($notificationIds) || empty($notificationIds)) {
            throw new InvalidArgumentException($this->_helper->__('Invalid notification ids list'));
        }

        foreach ($notificationIds as $notificationId) {
            try {
                $this->update($notificationId, $data);
            } catch (InvalidArgumentException $exception) {
                continue;
            }
        }
    }
}
