<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Rma\Model\Resource;

use Magento\Rma\Model\Rma as ModelRma;

/**
 * RMA shipping resource model
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Shipping extends \Magento\Framework\Model\Resource\Db\AbstractDb
{
    /**
     * Internal constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('magento_rma_shipping_label', 'entity_id');
    }

    /**
     * Delete tracking numbers for current rma shipping label
     *
     * @param ModelRma|int $rma
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
            \Magento\Rma\Model\Shipping::IS_ADMIN_STATUS_ADMIN_LABEL_TRACKING_NUMBER
        );

        return $adapter->delete($this->getTable('magento_rma_shipping_label'), $where);
    }
}
