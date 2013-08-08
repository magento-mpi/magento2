<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerCustomAttributes
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Rma Item Form Renderer Block for select
 *
 * @category    Magento
 * @package     Magento_Rma
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Rma_Block_Form_Renderer_Select extends Magento_CustomAttribute_Block_Form_Renderer_Select
{
    /**
     * Prepare rma item attribute
     *
     * @return boolean | Magento_Rma_Model_Item_Attribute
     */
    public function getAttribute($code)
    {
        /* @var $itemModel  */
        $itemModel = Mage::getModel('Magento_Rma_Model_Item');

        /* @var $itemForm Magento_Rma_Model_Item_Form */
        $itemForm   = Mage::getModel('Magento_Rma_Model_Item_Form');
        $itemForm->setFormCode('default')
            ->setStore($this->getStore())
            ->setEntity($itemModel);

        $attribute = $itemForm->getAttribute($code);
        if ($attribute->getIsVisible()) {
            return $attribute;
        }
        return false;
    }
}

