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
 * Container for Mage_Sales_Model_Order_Creditmemo for creditmemo item variable
 *
 * Container that can restrict access to properties and method
 * with black list or white list.
 *
 * @category   Saas
 * @package    Saas_PrintedTemplate
 * @subpackage Models
 */
class Saas_PrintedTemplate_Model_Variable_Item_Creditmemo extends Saas_PrintedTemplate_Model_Variable_Item_Abstract
{
    /**
     * Item variable name
     *
     * @var string
     */
    protected $_itemType = 'item_creditmemo';

    /**
     * Set additional data to credimemo item object
     *
     * @return Saas_PrintedTemplate_Model_Variable_Item_Creditmemo
     */
    protected function _initVariable()
    {
        $value = $this->_value;
        if (!$value instanceof Magento_Object) {
            return $this;
        }

        if (!$value->hasDiscountAmount()) {
            $value->setDiscountAmount(0);
        }

        if (!$value->hasRowTotalInc()) {
            $value->setRowTotalInc(
                $value->getRowTotal() + $value->getTaxAmount() - $value->getDiscountAmount()
            );
        }
        if (!$this->_isFloatEqualToZero($value->getRowTotal())) {
            $value->setDiscountRate(100. * $value->getDiscountAmount() / $value->getRowTotal());
        } else {
            $value->setDiscountRate(0);
        }
        if (!$value->getPriceInclTax() && !$this->_isFloatEqualToZero($value->getQty())) {
            $value->setPriceInclTax(
                ($value->getRowTotal() + $value->getTaxAmount() + $value->getWeeeTaxAppliedRowAmount())/$value->getQty()
            );
        }
        if (!$value->hasRowTotalInclDiscountAndTax() && $value->hasRowTotalInclTax()) {
            $value->setRowTotalInclDiscountAndTax($value->getRowTotalInclTax() - $value->getDiscountAmount());
        }

        return $this;
    }

    /**
     * Retrieve item's parent entity
     *
     * @return Magento_Core_Model_Abstract
     */
    protected function _getParentEntity()
    {
        return $this->_value->getCreditmemo();
    }
}
