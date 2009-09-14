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
 * @category   Mage
 * @package    Mage_GoogleBase
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * WYSIWYG widget options form
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Cms_Widget_Chooser extends Mage_Adminhtml_Block_Template
{
    /**
     * Chooser source URL getter
     *
     * @return string
     */
    public function getSourceUrl()
    {
        return $this->getData('source_url');
    }

    /**
     * Chooser form element getter
     *
     * @return Varien_Data_Form_Element_Abstract
     */
    public function getElement()
    {
        return $this->getData('element');
    }

    /**
     * Flag to indicate include hidden field before chooser or not
     *
     * @return bool
     */
    public function getHiddenEnabled()
    {
        return $this->hasData('hidden_enabled') ? (bool)$this->getData('hidden_enabled') : true;
    }

    /**
     * Return chooser HTML and init scripts
     *
     * @return string
     */
    protected function _toHtml()
    {
        $element = $this->getElement();

        $hiddenHtml = '';
        if ($this->getHiddenEnabled()) {
            $hidden = new Varien_Data_Form_Element_Hidden(array(
                'name'      => $element->getName(),
                'required'  => (bool)$element->required,
                'value'     => $element->getValue(),
                'class'     => $element->getClass(),
            ));
            $hidden->setId($element->getId());
            $hidden->setForm($element->getForm());
            $hiddenHtml = $hidden->getElementHtml();

            // Unset element value in favour of hidden field
            $element->setValue("");
        }

        $image = Mage::getDesign()->getSkinUrl('images/rule_chooser_trigger.gif');
        $chooserId = $element->getId() . md5(microtime());
        $chooserJsObject = $chooserId . 'JsChooser';

        $chooseButton = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setType('button')
            ->setId($chooserId)
            ->setClass('widget-option-chooser')
            ->setLabel($this->getChooserLabel() ? $this->getChooserLabel() : $this->helper('cms')->__('Choose'))
            ->setOnclick($chooserJsObject.'.choose()');

        $cancelButton = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setType('button')
            ->setId($chooserId.'_cancel')
            ->setStyle('display:none')
            ->setClass('widget-option-chooser-cancel')
            ->setLabel($this->getCancelLabel() ? $this->getCancelLabel() : $this->helper('cms')->__('Cancel'))
            ->setOnclick($chooserJsObject.'.hide()');

        $html = '
            <script type="text/javascript">
                '.$chooserJsObject.' = new WysiwygWidget.chooser("'.$chooserId.'", "'.$this->getSourceUrl().'");
            </script>
            '.$hiddenHtml.$chooseButton->toHtml().'&nbsp;'.$cancelButton->toHtml().'
            <label class="widget-option-label">'.($this->getLabel() ? $this->getLabel() : '').'</label>
        ';
        return $html;
    }


}
