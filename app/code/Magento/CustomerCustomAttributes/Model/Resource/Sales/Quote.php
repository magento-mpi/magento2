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
 * Customer Quote resource
 */
namespace Magento\CustomerCustomAttributes\Model\Resource\Sales;

class Quote
    extends \Magento\CustomerCustomAttributes\Model\Resource\Sales\AbstractSales
{
    /**
     * Main entity resource model
     *
     * @var \Magento\Sales\Model\Resource\Quote
     */
    protected $_parentResourceModel;

    /**
     * @param \Magento\Core\Model\Resource $resource
     * @param \Magento\Sales\Model\Resource\Quote $parentResourceModel
     */
    public function __construct(
        \Magento\Core\Model\Resource $resource,
        \Magento\Sales\Model\Resource\Quote $parentResourceModel
    ) {
        $this->_parentResourceModel = $parentResourceModel;
        parent::__construct($resource);
    }

    /**
     * Initialize resource
     */
    protected function _construct()
    {
        $this->_init('magento_customercustomattributes_sales_flat_quote', 'entity_id');
    }

    /**
     * Return resource model of the main entity
     *
     * @return \Magento\Sales\Model\Resource\Quote
     */
    protected function _getParentResourceModel()
    {
        return $this->_parentResourceModel;
    }
}
