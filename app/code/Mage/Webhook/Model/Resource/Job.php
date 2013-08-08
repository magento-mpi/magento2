<?php
/**
 * Job resource
 *
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webhook_Model_Resource_Job extends Magento_Core_Model_Resource_Db_Abstract
{
    /**
     * Initializes resource model
     */
    public function _construct()
    {
        $this->_init('webhook_dispatch_job', 'dispatch_job_id');
    }
}
