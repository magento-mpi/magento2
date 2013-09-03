<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Grid column widget for rendering text grid cells
 *
 * @category    Magento
 * @package     Magento_Rma
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Rma_Block_Adminhtml_Rma_Edit_Tab_Items_Grid_Column_Renderer_Quantity
    extends Magento_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Renders quantity as integer
     *
     * @param \Magento\Object $row
     * @return int|string
     */
    public function _getValue(\Magento\Object $row)
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
