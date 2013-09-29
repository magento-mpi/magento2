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
namespace Magento\Cron\Model\Resource\Schedule;

class Collection extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Initialize resource collection
     *
     */
    public function _construct()
    {
        $this->_init('Magento\Cron\Model\Schedule', 'Magento\Cron\Model\Resource\Schedule');
    }
}
