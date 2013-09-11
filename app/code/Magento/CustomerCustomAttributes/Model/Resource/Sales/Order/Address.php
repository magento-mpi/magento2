<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerCustomAttributes
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Customer Order Address resource model
 *
 * @category    Magento
 * @package     Magento_CustomerCustomAttributes
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\CustomerCustomAttributes\Model\Resource\Sales\Order;

class Address
    extends \Magento\CustomerCustomAttributes\Model\Resource\Sales\Address\AbstractAddress
{
    /**
     * Main entity resource model name
     *
     * @var string
     */
    protected $_parentResourceModelName = '\Magento\Sales\Model\Resource\Order\Address';

    /**
     * Initializes resource
     */
    protected function _construct()
    {
        $this->_init('magento_customercustomattributes_sales_flat_order_address', 'entity_id');
    }
}
