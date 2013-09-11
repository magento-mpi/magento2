<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reports
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Report event type resource model
 *
 * @category    Magento
 * @package     Magento_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Reports\Model\Resource\Event;

class Type extends \Magento\Core\Model\Resource\Db\AbstractDb
{
    /**
     * Main table initialization 
     *
     */
    protected function _construct()
    {
        $this->_init('report_event_types', 'event_type_id');
    }
}
