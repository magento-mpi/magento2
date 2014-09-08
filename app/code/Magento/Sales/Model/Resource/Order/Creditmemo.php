<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\Resource\Order;

use Magento\Framework\App\Resource;
use Magento\Framework\Stdlib\DateTime;
use Magento\Sales\Model\Resource\Entity as SalesResource;
use Magento\Sales\Model\Resource\Order\Creditmemo\Grid as CreditmemoGrid;

/**
 * Flat sales order creditmemo resource
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Creditmemo extends SalesResource
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'sales_order_creditmemo_resource';

    /**
     * Model initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('sales_flat_creditmemo', 'entity_id');
    }
}
