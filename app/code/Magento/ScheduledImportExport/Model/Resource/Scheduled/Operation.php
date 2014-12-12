<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\ScheduledImportExport\Model\Resource\Scheduled;

/**
 * Operation resource model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Operation extends \Magento\Framework\Model\Resource\Db\AbstractDb
{
    /**
     * Initialize resource operation model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('magento_scheduled_operations', 'id');

        $this->_useIsObjectNew = true;
    }
}
