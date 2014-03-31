<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Paypal\Model\Resource\Report\Settlement;

/**
 * Report settlement row resource model
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Row extends \Magento\Model\Resource\Db\AbstractDb
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
