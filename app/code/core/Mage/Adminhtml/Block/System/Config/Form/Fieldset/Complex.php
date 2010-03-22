<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Two-columns (fields + extended comments) detailed fieldset renderer
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_System_Config_Form_Fieldset_Complex extends Mage_Adminhtml_Block_System_Config_Form_Fieldset
{
    /**
     * Assign 
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        foreach ($element->getSortedElements() as $field) {
            $field->setUseContainerId(true);
        }
        return parent::render($element);
    }

    /**
     * Return footer html for fieldset
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getFooterHtml($element)
    {
        $html = '</tbody></table>';
        foreach ($element->getSortedElements() as $field) {
            $comment = $field->getComment();
            if ($field->getCommentBlock()) {
                $comment = $this->getLayout()->createBlock($field->getCommentBlock())->toHtml();
            }
            if ($comment) {
                $html .= sprintf('<div id="row_%s_comment" class="tool-tip" style="display:none;"><span class="tool-tip-bg"><span class="tool-tip-corner">%s</span></span></div>',
                    $field->getId(), $comment
                );
            }
        }

        $html .= '</fieldset>' . $this->_getExtraJs($element);
        return $html;
    }

    /**
     * Return full css class name for form fieldset
     *
     * @return string
     */
    protected function _getFieldsetCss()
    {
        return parent::_getFieldsetCss();
    }

    /**
     * Return js code for fieldset:
     * - observe fieldset rows;
     * - apply collapse;
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getExtraJs($element)
    {
        $id = $element->getHtmlId();
        $js = "Fieldset.applyCollapse('{$id}');";
        $js.= "$$('#{$id} table tbody tr').each(function(tr) {
                   Event.observe(tr, 'mouseover', function (event) {
                       $$('div.row-comment').invoke('hide');
                       var tr = Event.findElement(event, 'tr')
                       var id = tr.id + '_comment';
                       if ($(id) != undefined) {
                           var trLeft = tr.cumulativeOffset().left;
                           var trTop  = tr.cumulativeOffset().top;
                           var tipOffsetLeft = 5;
                           var tipOffsetTop  = tr.select('label')[0].getDimensions().height + 5;
                           $(id).setStyle({left : trLeft + tipOffsetLeft + 'px', top : trTop + tipOffsetTop + 'px'}).show();
                           
                           Event.observe(id, 'mouseover', function() {
                               this.setStyle({display:'block'});
                           });
                           Event.observe(id, 'mouseout', function() {
                               if(!($(tr.id).hasClassName('hover')))
                                   this.hide();
                           });
                       }
                   });
                   Event.observe(tr, 'mouseout', function (event) {
                       var tr = Event.findElement(event, 'tr')
                       var id = tr.id + '_comment';
                       if ($(id) != undefined) {
                           $(id).hide();
                       }
                   });
               });";

        return Mage::helper('adminhtml/js')->getScript($js);
    } 
}
