<?php
/**
 * Job notification massaction item updater
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_JobNotification_Model_Grid_Massaction_ItemUpdater implements Mage_Core_Model_Layout_Argument_UpdaterInterface
{
    /**
     * @var Mage_Core_Model_Authorization
     */
    protected $_authorization;

    /**
     * @param Mage_Core_Model_Authorization $authorization
     */
    public function __construct(Mage_Core_Model_Authorization $authorization)
    {
        $this->_authorization = $authorization;
    }

    /**
     * Update specified argument
     *
     * @param array $argument
     * @return array
     */
    public function update($argument)
    {
        if (false == $this->_authorization->isAllowed('Saas_JobNotification::notification_action_markread')) {
            unset($argument['mark_as_read']);
        }

        if (false == $this->_authorization->isAllowed('Saas_JobNotification::notification_action_remove')) {
            unset($argument['remove']);
        }

        return $argument;
    }
}