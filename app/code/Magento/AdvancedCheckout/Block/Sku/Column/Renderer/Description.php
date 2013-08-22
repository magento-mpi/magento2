<?php
/**
 * SKU failed description block renderer
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_AdvancedCheckout_Block_Sku_Column_Renderer_Description
    extends Magento_Backend_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Magento_Object $row)
    {
        $descriptionBlock = $this->getLayout()->createBlock(
            'Magento_AdvancedCheckout_Block_Adminhtml_Sku_Errors_Grid_Description',
            '',
            array('data' => array('product' => $row->getProduct(), 'item' => $row))
        );

        return $descriptionBlock->toHtml();
    }

}
