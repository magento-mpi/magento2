<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * RMA shipping resource model
 *
 * @category   Magento
 * @package    Magento_Rma
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Rma_Model_Resource_Shipping extends Magento_Core_Model_Resource_Db_Abstract
{
    /**
     * Internal constructor
     */
    protected function _construct() {
        $this->_init('magento_rma_shipping_label', 'entity_id');
    }

    /**
     * Delete tracking numbers for current rma shipping label
     *
     * @var Magento_Rma_Model_Rma|int $rma
     * @return string
     */
    public function deleteTrackingNumbers($rma)
    {
        if (!is_int($rma)) {
            $rma = $rma->getId();
        }

        $adapter = $this->_getWriteAdapter();

        $where = $adapter->quoteInto('rma_entity_id = ? ', $rma);
        $where .= $adapter->quoteInto(
            'AND is_admin = ? ',
            Magento_Rma_Model_Shipping::IS_ADMIN_STATUS_ADMIN_LABEL_TRACKING_NUMBER
        );

        return $adapter->delete($this->getTable('magento_rma_shipping_label'), $where);
    }
}
