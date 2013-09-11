<?php
/**
 * Job resource
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webhook\Model\Resource;

class Job extends \Magento\Core\Model\Resource\Db\AbstractDb
{
    /**
     * Initializes resource model
     */
    public function _construct()
    {
        $this->_init('webhook_dispatch_job', 'dispatch_job_id');
    }
}
