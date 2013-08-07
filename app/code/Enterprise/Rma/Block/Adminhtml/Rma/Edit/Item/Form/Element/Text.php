<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * RMA Item Widget Form Text Element Block
 *
 * @category    Enterprise
 * @package     Enterprise_Rma
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Rma_Block_Adminhtml_Rma_Edit_Item_Form_Element_Text extends Magento_Data_Form_Element_Text
{
    /**
     * Return Form Element HTML
     *
     * @return string
     */
    public function getElementHtml()
    {
        $additionalClasses = Mage::helper('Enterprise_Rma_Helper_Eav')
            ->getAdditionalTextElementClasses($this->getEntityAttribute());
        foreach ($additionalClasses as $additionalClass) {
            $this->addClass($additionalClass);
        }
        return parent::getElementHtml();
    }
}
