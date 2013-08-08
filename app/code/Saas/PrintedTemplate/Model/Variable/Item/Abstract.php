<?php
/**
 * {license_notice}
 *
 * @category   Saas
 * @package    Saas_PrintedTemplate
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Abstract Container for Item variable
 *
 * Container that can restrict access to properties and method
 * with black list or white list.
 *
 * @category   Saas
 * @package    Saas_PrintedTemplate
 * @subpackage Models
 */
abstract class Saas_PrintedTemplate_Model_Variable_Item_Abstract
    extends Saas_PrintedTemplate_Model_Variable_Abstract
{
    /**
     * Key for config and variables creation
     *
     * @see Saas_PrintedTemplate_Model_Variable_Abstract::_setListsFromConfig()
     * @var string
     */
    protected $_itemType;

    /**
     * Constructor
     *
     * @param mixed $value
     */
    public function __construct($value = null)
    {
        parent::__construct($value);
        $this->_setListsFromConfig($this->_itemType);
    }

    /**
     * Get Order Item
     * @return
     */
    public function getOrderItem()
    {
        return $this->_value->getOrderItem();
    }

    /**
     * Returns parent entity
     *
     * @return Magento_Core_Model_Abstract
     */
    abstract protected function _getParentEntity();

    /**
     * Get child items
     *
     * @return array
     */
    public function getChildren()
    {
        $items = $this->_getParentEntity()->getAllItems();

        $parentItemId = $this->_value->getOrderItem()->getId();
        $children = array();
        foreach ($items as $item) {
            $parentItem = $item->getOrderItem()->getParentItem();
            if (($parentItem && $parentItem->getId() == $parentItemId)
                || (!$parentItem && $item->getOrderItem()->getId() == $parentItemId)
            ) {
                $children[$item->getOrderItemId()] = $this->_getVariableModel(array('value' => $item));
            }
        }

        return $children;
    }

    protected function _getVariableModel($args)
    {
        return Mage::getModel('Saas_PrintedTemplate_Model_Variable_' . uc_words($this->_itemType), $args);
    }

    /**
     * Formats currency using order formater
     *
     * @param float
     * @return string
     */
    public function formatCurrency($value)
    {
        return (null !== $value) ? $this->_getParentEntity()->getOrder()->formatPriceTxt($value) : '';
    }

    /**
     * Formats tax rates array
     *
     * @param array $value Array of Saas_PrintedTemplate_Model_Tax_Order_Item
     */
    public function formatTaxRates($value)
    {
        if (empty($value)) {
            return $this->formatPercent(0);
        }

        $separator = '<br />';
        $formattedTaxes = array();
        foreach ($value as $tax) {
            $formattedTaxes[] = $this->formatPercent($tax->getPercent());
        }
        return implode($separator, $formattedTaxes);
    }

    /**
     * Returns type of item
     *
     * @return string
     */
    public function getItemType()
    {
        return $this->_itemType;
    }
}
