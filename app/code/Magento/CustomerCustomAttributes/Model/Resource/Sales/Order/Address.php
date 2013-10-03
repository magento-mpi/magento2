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
 */
namespace Magento\CustomerCustomAttributes\Model\Resource\Sales\Order;

class Address
    extends \Magento\CustomerCustomAttributes\Model\Resource\Sales\Address\AbstractAddress
{
    /**
     * Main entity resource model
     *
     * @var \Magento\Sales\Model\Resource\Order\Address
     */
    protected $_parentResourceModel;

    /**
     * @param \Magento\Core\Model\Resource $resource
     * @param \Magento\Sales\Model\Resource\Order\Address $parentResourceModel
     */
    public function __construct(
        \Magento\Core\Model\Resource $resource,
        \Magento\Sales\Model\Resource\Order\Address $parentResourceModel
    ) {
        $this->_parentResourceModel = $parentResourceModel;
        parent::__construct($resource);
    }

    /**
     * Initializes resource
     */
    protected function _construct()
    {
        $this->_init('magento_customercustomattributes_sales_flat_order_address', 'entity_id');
    }

    /**
     * Return resource model of the main entity
     *
     * @return \Magento\Sales\Model\Resource\Order\Address
     */
    protected function _getParentResourceModel()
    {
        return $this->_parentResourceModel;
    }
}
