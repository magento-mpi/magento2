<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Grid column widget for rendering text grid cells
 *
 * @category    Enterprise
 * @package     Enterprise_Rma
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Rma_Block_Adminhtml_Rma_Edit_Tab_Items_Grid_Column_Renderer_Quantity
    extends Magento_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Renders quantity as integer
     *
     * @param Magento_Object $row
     * @return int|string
     */
    public function _getValue(Magento_Object $row)
    {
        if ($row->getProductType() == Magento_Catalog_Model_Product_Type::TYPE_BUNDLE) {
            return '';
        }
        $quantity = parent::_getValue($row);
        if ($row->getIsQtyDecimal()) {
            return sprintf("%01.4f", $quantity);
        } else {
            return intval($quantity);
        }
    }
}
