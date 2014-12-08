<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Report event type resource model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Reports\Model\Resource\Event;

class Type extends \Magento\Framework\Model\Resource\Db\AbstractDb
{
    /**
     * Main table initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('report_event_types', 'event_type_id');
    }
}
