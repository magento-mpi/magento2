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
 * @category   Mage
 * @package    Mage_Rule
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_Rule_Block_Editable extends Mage_Core_Block_Abstract
    implements Varien_Data_Form_Element_Renderer_Interface
{
	public function render(Varien_Data_Form_Element_Abstract $element)
	{
	    $valueName = $element->getValueName();

	    if ($valueName=='') {
	        $valueName = '...';
	    } else {
	        $valueName = Mage::helper('core/string')->truncate($valueName, 30);
	    }
	    if ($element->getShowAsText()) {
	        $html = ' <input type="hidden" id="'.$element->getHtmlId().'" name="'.$element->getName().'" value="'.$element->getValue().'"/> ';

	        $html.= htmlspecialchars($valueName).'&nbsp;';
	    } else {
    		$html = ' <span class="rule-param" id="'.$element->getParamId().'">';

    		$html.= '<a href="javascript:void(0)" class="label">';

    		$html.= htmlspecialchars($valueName);

    		$html.= '</a><span class="element"> ';

    		$html.= $element->getElementHtml();

    		if ($element->getExplicitApply()) {
    		    $html.= ' <a href="javascript:void(0)" class="rule-param-apply"><img src="'.$this->getSkinUrl('images/rule_component_apply.gif').'" class="v-middle" title="'.$this->__('Apply').'"/></a> ';
    		}

    		$html.= '</span></span>&nbsp;';
	    }
		return $html;
	}
}