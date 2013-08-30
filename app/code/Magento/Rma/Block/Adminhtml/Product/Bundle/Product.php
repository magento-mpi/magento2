<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Rma_Block_Adminhtml_Product_Bundle_Product
    extends Magento_Adminhtml_Block_Widget_Grid_Column_Renderer_Text
{
    /**
     * Render product name to add Configure link
     *
     * @param   Magento_Object $row
     * @return  string
     */
    public function render(Magento_Object $row)
    {
        $rendered       =  parent::render($row);
        $link           = '';
        if ($row->getProductType() == Magento_Catalog_Model_Product_Type::TYPE_BUNDLE) {
            $link = sprintf(
                '<a href="javascript:void(0)" class="product_to_add" id="productId_%s">%s</a>',
                $row->getId(),
                __('Select Items')
            );
        }
        return $rendered.$link;
    }
}
