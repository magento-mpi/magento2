<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Cron
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Schedules Collection
 *
 * @category    Mage
 * @package     Mage_Cron
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Cron_Model_Resource_Schedule_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Initialize resource collection
     *
     */
    public function _construct()
    {
        $this->_init('Mage_Cron_Model_Schedule', 'Mage_Cron_Model_Resource_Schedule');
    }
}
