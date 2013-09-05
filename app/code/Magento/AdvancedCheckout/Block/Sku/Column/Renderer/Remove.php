<?php
/**
 * SKU failed information block renderer
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_AdvancedCheckout_Block_Sku_Column_Renderer_Remove extends Magento_Backend_Block_Widget_Grid_Column_Renderer_Button
{
    public function render(\Magento\Object $row)
    {
        $removeButtonHtml = $this->getLayout()->createBlock('Magento_Adminhtml_Block_Widget_Button', '', array(
            'data' => array(
                'class' => 'delete',
                'label' => 'Remove',
                'onclick' => 'addBySku.removeFailedItem(this)',
                'type' => 'button',
            )
        ));

        return $removeButtonHtml->toHtml();
    }
}
