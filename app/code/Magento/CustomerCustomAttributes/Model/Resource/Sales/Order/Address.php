<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\CustomerCustomAttributes\Model\Resource\Sales\Order;

/**
 * Customer Order Address resource model
 */
class Address extends \Magento\CustomerCustomAttributes\Model\Resource\Sales\Address\AbstractAddress
{
    /**
     * Main entity resource model
     *
     * @var \Magento\Sales\Model\Resource\Order\Address
     */
    protected $_parentResourceModel;

    /**
     * @param \Magento\Framework\App\Resource $resource
     * @param \Magento\Sales\Model\Resource\Order\Address $parentResourceModel
     */
    public function __construct(
        \Magento\Framework\App\Resource $resource,
        \Magento\Sales\Model\Resource\Order\Address $parentResourceModel
    ) {
        $this->_parentResourceModel = $parentResourceModel;
        parent::__construct($resource);
    }

    /**
     * Initializes resource
     *
     * @return void
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
