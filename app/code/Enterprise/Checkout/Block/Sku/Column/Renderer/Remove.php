<?php
/**
 * SKU failed information block renderer
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Enterprise_Checkout_Block_Sku_Column_Renderer_Remove extends Mage_Backend_Block_Widget_Grid_Column_Renderer_Button
{
    public function render(Varien_Object $row)
    {
        $removeButtonHtml = $this->getLayout()->createBlock('Mage_Adminhtml_Block_Widget_Button', '', array(
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
