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
 * SKU failed description block renderer
 *
 * @category   Enterprise
 * @package    Enterprise_Checkout
 *
 * @method Mage_Sales_Model_Quote_Item getItem()
 */
class Enterprise_Checkout_Block_Sku_Column_Description extends Mage_Backend_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $descriptionBlock = $this->getLayout()->createBlock(
            'Enterprise_Checkout_Block_Adminhtml_Sku_Errors_Grid_Description',
            '',
            array('data' => array('product' => $row->getProduct(), 'item' => $row))
        );

        return $descriptionBlock->toHtml();
    }

}
