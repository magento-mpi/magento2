<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rma\Model\Resource\Rma\Status;

/**
 * RMA entity resource model
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class History extends \Magento\Framework\Model\Resource\Db\AbstractDb
{
    /**
     * Internal constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('magento_rma_status_history', 'entity_id');
    }
}
