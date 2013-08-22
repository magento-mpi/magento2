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
class Magento_Reports_Model_Resource_Event_Type extends Magento_Core_Model_Resource_Db_Abstract
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
