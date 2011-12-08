<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Xmlconnect color form element
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Adminhtml_Mobile_Form_Element_Color
    extends Varien_Data_Form_Element_Text
{
    /**
     * Return html code for current block
     *
     * @return mixed|string
     */
    public function getHtml()
    {
        $this->addClass('color {required:false,hash:true}');
        return parent::getHtml();
    }
}
