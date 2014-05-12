<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Paypal\Model\Resource\Report\Settlement;

/**
 * Report settlement row resource model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Row extends \Magento\Framework\Model\Resource\Db\AbstractDb
{
    /**
     * Resource model initialization.
     * Set main entity table name and primary key field name.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('paypal_settlement_report_row', 'row_id');
    }
}
