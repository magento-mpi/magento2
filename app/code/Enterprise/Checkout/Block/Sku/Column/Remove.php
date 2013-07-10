<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * SKU failed information Block
 *
 * @category   Enterprise
 * @package    Enterprise_Checkout
 *
 * @method Mage_Sales_Model_Quote_Item getItem()
 */
class Enterprise_Checkout_Block_Sku_Column_Remove extends Mage_Backend_Block_Widget_Grid_Column_Renderer_Button
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
