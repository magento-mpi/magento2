<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Rma Item Form Renderer Block for select
 *
 * @category    Enterprise
 * @package     Enterprise_Rma
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Rma_Block_Form_Renderer_Select extends Enterprise_Eav_Block_Form_Renderer_Select
{
    /**
     * Prepare rma item attribute
     *
     * @return boolean | Enterprise_Rma_Model_Item_Attribute
     */
    public function getAttribute($code)
    {
        /* @var $itemModel  */
        $itemModel = Mage::getModel('Enterprise_Rma_Model_Item');

        /* @var $itemForm Enterprise_Rma_Model_Item_Form */
        $itemForm   = Mage::getModel('Enterprise_Rma_Model_Item_Form');
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

