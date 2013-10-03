<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ScheduledImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Operation resource model
 *
 * @category    Magento
 * @package     Magento_ScheduledImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\ScheduledImportExport\Model\Resource\Scheduled;

class Operation extends \Magento\Core\Model\Resource\Db\AbstractDb
{
    /**
     * Initialize resource operation model
     *
     */
    protected function _construct()
    {
        $this->_init('magento_scheduled_operations', 'id');

        $this->_useIsObjectNew = true;
    }
}
