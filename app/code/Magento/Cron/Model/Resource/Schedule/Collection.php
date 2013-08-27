<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cron
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Schedules Collection
 *
 * @category    Magento
 * @package     Magento_Cron
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Cron_Model_Resource_Schedule_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Initialize resource collection
     *
     */
    public function _construct()
    {
        $this->_init('Magento_Cron_Model_Schedule', 'Magento_Cron_Model_Resource_Schedule');
    }
}
