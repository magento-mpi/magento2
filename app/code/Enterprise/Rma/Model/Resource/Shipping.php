<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * RMA shipping resource model
 *
 * @category   Enterprise
 * @package    Enterprise_Rma
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Rma_Model_Resource_Shipping extends Magento_Core_Model_Resource_Db_Abstract
{
    /**
     * Internal constructor
     */
    protected function _construct() {
        $this->_init('enterprise_rma_shipping_label', 'entity_id');
    }

    /**
     * Delete tracking numbers for current rma shipping label
     *
     * @var Enterprise_Rma_Model_Rma|int $rma
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
            Enterprise_Rma_Model_Shipping::IS_ADMIN_STATUS_ADMIN_LABEL_TRACKING_NUMBER
        );

        return $adapter->delete($this->getTable('enterprise_rma_shipping_label'), $where);
    }
}
