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
 * Customer new password field renderer
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Customer_Edit_Renderer_Newpass
    extends Mage_Core_Block_Template
    implements Varien_Data_Form_Element_Renderer_Interface
{

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $html = '<tr>';
        $html.= '<td class="label">'.$element->getLabelHtml().'</td>';
        $html.= '<td class="value">'.$element->getElementHtml().'</td>';
        $html.= '</tr>'."\n";
        $html.= '<tr>';
        $html.= '<td class="label"><label>&nbsp;</label></td>';
        $html.= '<td class="value">'.Mage::helper('Mage_Customer_Helper_Data')->__('or').'</td>';
        $html.= '</tr>'."\n";
        $html.= '<tr>';
        $html.= '<td class="label"><label>&nbsp;</label></td>';
        $html.= '<td class="value"><input type="checkbox" id="account-send-pass" name="'.$element->getName().'" value="auto" onclick="setElementDisable(\''.$element->getHtmlId().'\', this.checked)"/>&nbsp;';
        $html.= '<label for="account-send-pass">'.Mage::helper('Mage_Customer_Helper_Data')->__('Send auto-generated password').'</label></td>';
        $html.= '</tr>'."\n";

        return $html;
    }

}
