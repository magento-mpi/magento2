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
 * Customer Quote Address resource model
 */
namespace Magento\CustomerCustomAttributes\Model\Resource\Sales\Quote;

class Address
    extends \Magento\CustomerCustomAttributes\Model\Resource\Sales\Address\AbstractAddress
{
    /**
     * Main entity resource model
     *
     * @var \Magento\Sales\Model\Resource\Quote\Address
     */
    protected $_parentResourceModel;

    /**
     * @param \Magento\Core\Model\Resource $resource
     * @param \Magento\Sales\Model\Resource\Quote\Address $parentResourceModel
     */
    public function __construct(
        \Magento\Core\Model\Resource $resource,
        \Magento\Sales\Model\Resource\Quote\Address $parentResourceModel
    ) {
        $this->_parentResourceModel = $parentResourceModel;
        parent::__construct($resource);
    }

    /**
     * Initialize resource
     */
    protected function _construct()
    {
        $this->_init('magento_customercustomattributes_sales_flat_quote_address', 'entity_id');
    }

    /**
     * Return resource model of the main entity
     *
     * @return \Magento\Sales\Model\Resource\Quote\Address
     */
    protected function _getParentResourceModel()
    {
        return $this->_parentResourceModel;
    }
}
