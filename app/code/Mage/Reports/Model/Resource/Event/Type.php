<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Reports
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Report event type resource model
 *
 * @category    Mage
 * @package     Mage_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Reports_Model_Resource_Event_Type extends Magento_Core_Model_Resource_Db_Abstract
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
