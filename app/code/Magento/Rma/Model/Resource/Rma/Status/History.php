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
 * RMA entity resource model
 *
 * @category   Magento
 * @package    Magento_Rma
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Rma\Model\Resource\Rma\Status;

class History extends \Magento\Core\Model\Resource\Db\AbstractDb
{
    /**
     * Internal constructor
     */
    protected function _construct()
    {
        $this->_init('magento_rma_status_history', 'entity_id');
    }
}
