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
 * Customer Order resource
 */
class Magento_CustomerCustomAttributes_Model_Resource_Sales_Order
    extends Magento_CustomerCustomAttributes_Model_Resource_Sales_Abstract
{
    /**
     * Main entity resource model
     *
     * @var Magento_Sales_Model_Resource_Order
     */
    protected $_parentResourceModel;

    /**
     * @param Magento_Core_Model_Resource $resource
     * @param Magento_Sales_Model_Resource_Order $parentResourceModel
     */
    public function __construct(
        Magento_Core_Model_Resource $resource,
        Magento_Sales_Model_Resource_Order $parentResourceModel
    ) {
        $this->_parentResourceModel = $parentResourceModel;
        parent::__construct($resource);
    }

    /**
     * Initialize resource
     */
    protected function _construct()
    {
        $this->_init('magento_customercustomattributes_sales_flat_order', 'entity_id');
    }

    /**
     * Return resource model of the main entity
     *
     * @return Magento_Sales_Model_Resource_Order
     */
    protected function _getParentResourceModel()
    {
        return $this->_parentResourceModel;
    }
}
