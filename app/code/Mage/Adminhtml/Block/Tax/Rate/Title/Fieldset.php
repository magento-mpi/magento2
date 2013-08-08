<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Tax Rate Titles Fieldset
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Tax_Rate_Title_Fieldset extends Magento_Data_Form_Element_Fieldset
{
    public function getBasicChildrenHtml()
    {
        return Mage::getBlockSingleton('Mage_Adminhtml_Block_Tax_Rate_Title')->toHtml();
    }
}
