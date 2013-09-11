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
 * Customer new password field renderer
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Customer\Edit\Renderer;

class Newpass
    extends \Magento\Backend\Block\AbstractBlock
    implements \Magento\Data\Form\Element\Renderer\RendererInterface
{

    public function render(\Magento\Data\Form\Element\AbstractElement $element)
    {
        $html = '<div class="field field-'.$element->getHtmlId().'">';
        $html.= $element->getLabelHtml();
        $html.= '<div class="control">'.$element->getElementHtml();
        $html.= '<div class="nested">';
        $html.= '<div class="field choice">';
        $html.= '<label for="account-send-pass" class="addbefore"><span>'.__('or ').'</span></label>';
        $html.= '<input type="checkbox" id="account-send-pass" name="'.$element->getName().'" value="auto" onclick="setElementDisable(\''.$element->getHtmlId().'\', this.checked)" />';
        $html.= '<label class="label" for="account-send-pass"><span>'.__(' Send auto-generated password').'</span></label>';
        $html.= '</div>'."\n";
        $html.= '</div>'."\n";
        $html.= '</div>'."\n";
        $html.= '</div>'."\n";

        return $html;
    }

}
