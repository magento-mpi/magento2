<?php
/**
 * Abstract config form element renderer
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_System_Config_Form_Field
    extends Mage_Core_Block_Abstract
    implements Varien_Data_Form_Element_Renderer_Interface
{
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $id = $element->getHtmlId();
        $default = !$this->getRequest()->getParam('website') && !$this->getRequest()->getParam('store');

        // replace [value] with [inherit]
        $namePrefix = substr($element->getName(), 0, strlen($element->getName())-7);

        $inherit = $element->getInherit()==1 ? 'checked' : '';
        $options = $element->getValues();

        $html = '<tr><td class="label">'.$element->getLabel().'</td>';

        // custom value
        $html.= '<td class="value">'.$element->getElementHtml().'</td>';

        if (!$default) {
            $defText = $element->getDefaultValue();
            if ($options) {
                foreach ($options as $k=>$v) {
                    if ($v['value']==$defText) {
                        $defText = $v['label'];
                        break;
                    }
                }
            }

            // default value
            $html.= '<td class="default">';
            $html.= '<input id="'.$id.'_inherit" name="'.$namePrefix.'[inherit]" type="checkbox" value="1" class="input-checkbox config-inherit" '.$inherit.'>';
            $html.= '<label for="'.$id.'_inherit" class="inherit" title="'.htmlspecialchars($defText).'">'.__('Use default').'</label>';
            $html.= '<input type="hidden" name="'.$namePrefix.'[default_value]" value="'.$element->getDefaultValue().'">';
            $html.= '<input type="hidden" name="'.$namePrefix.'[old_value]" value="'.$element->getOldValue().'">';
            $html.= '</td>';
        }

        $html.= '</tr>';
        return $html;
    }
}
