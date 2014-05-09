<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Logging\Model\Resource\Event;

/**
 * Logging event changes model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Changes extends \Magento\Framework\Model\Resource\Db\AbstractDb
{
    /**
     * Initialize resource
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('magento_logging_event_changes', 'id');
    }
}
