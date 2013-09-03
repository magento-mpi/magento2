<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Tax Rate Titles Fieldset
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Tax_Rate_Title_Fieldset extends \Magento\Data\Form\Element\Fieldset
{
    public function getBasicChildrenHtml()
    {
        return Mage::getBlockSingleton('Magento_Adminhtml_Block_Tax_Rate_Title')->toHtml();
    }
}
