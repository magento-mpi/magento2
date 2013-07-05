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
        $buttonType = 'button';
        $buttonClass = 'delete';
        $buttonLabel = 'Remove';
        $buttonOnclick = 'addBySku.removeFailedItem(this)';
        return '<button'
        . (' type="' . $buttonType . '"' )
        . (' class="' . $buttonClass . '"')
        . (' onclick="' . $buttonOnclick . '"' )
        . (' title="' . $buttonLabel . '"' )
        .'>'
        . $this->getColumn()->getHeader()
        . '</button>';
    }

}
