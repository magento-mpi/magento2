<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Widget
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * WYSIWYG widget options form
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Mage_Widget_Block_Adminhtml_Widget_Chooser extends Mage_Adminhtml_Block_Template
{
    /**
     * Chooser source URL getter
     *
     * @return string
     */
    public function getSourceUrl()
    {
        return $this->_getData('source_url');
    }

    /**
     * Chooser form element getter
     *
     * @return Varien_Data_Form_Element_Abstract
     */
    public function getElement()
    {
        return $this->_getData('element');
    }

    /**
     * Convert Array config to Object
     *
     * @return Varien_Object
     */
    public function getConfig()
    {
        if ($this->_getData('config') instanceof Varien_Object) {
            return $this->_getData('config');
        }

        $configArray = $this->_getData('config');
        $config = new Varien_Object();
        $this->setConfig($config);
        if (!is_array($configArray)) {
            return $this->_getData('config');
        }

        // define chooser label
        if (isset($configArray['label'])) {
            $config->setData('label', $this->getTranslationHelper()->__($configArray['label']));
        }

        // chooser control buttons
        $buttons = array(
            'open'  => Mage::helper('Mage_Widget_Helper_Data')->__('Choose...'),
            'close' => Mage::helper('Mage_Widget_Helper_Data')->__('Close')
        );
        if (isset($configArray['button']) && is_array($configArray['button'])) {
            foreach ($configArray['button'] as $id => $label) {
                $buttons[$id] = $this->getTranslationHelper()->__($label);
            }
        }
        $config->setButtons($buttons);

        return $this->_getData('config');
    }

    /**
     * Helper getter for translations
     *
     * @return Mage_Core_Helper_Abstract
     */
    public function getTranslationHelper()
    {
        if ($this->_getData('translation_helper') instanceof Mage_Core_Helper_Abstract) {
            return $this->_getData('translation_helper');
        }
        return $this->helper('Mage_Widget_Helper_Data');
    }

    /**
     * Unique identifier for block that uses Chooser
     *
     * @return string
     */
    public function getUniqId()
    {
        return $this->_getData('uniq_id');
    }

    /**
     * Form element fieldset id getter for working with form in chooser
     *
     * @return string
     */
    public function getFieldsetId()
    {
        return $this->_getData('fieldset_id');
    }

    /**
     * Flag to indicate include hidden field before chooser or not
     *
     * @return bool
     */
    public function getHiddenEnabled()
    {
        return $this->hasData('hidden_enabled') ? (bool)$this->_getData('hidden_enabled') : true;
    }

    /**
     * Return chooser HTML and init scripts
     *
     * @return string
     */
    protected function _toHtml()
    {
        $element   = $this->getElement();
        /* @var $fieldset Varien_Data_Form_Element_Fieldset */
        $fieldset  = $element->getForm()->getElement($this->getFieldsetId());
        $chooserId = $this->getUniqId();
        $config    = $this->getConfig();

        // add chooser element to fieldset
        $chooser = $fieldset->addField('chooser' . $element->getId(), 'note', array(
            'label'       => $config->getLabel() ? $config->getLabel() : '',
            'value_class' => 'value2',
        ));
        $hiddenHtml = '';
        if ($this->getHiddenEnabled()) {
            $hidden = new Varien_Data_Form_Element_Hidden($element->getData());
            $hidden->setId("{$chooserId}value")->setForm($element->getForm());
            if ($element->getRequired()) {
                $hidden->addClass('required-entry');
            }
            $hiddenHtml = $hidden->getElementHtml();
            $element->setValue('');
        }

        $buttons = $config->getButtons();
        $chooseButton = $this->getLayout()->createBlock('Mage_Adminhtml_Block_Widget_Button')
            ->setType('button')
            ->setId($chooserId . 'control')
            ->setClass('btn-chooser')
            ->setLabel($buttons['open'])
            ->setOnclick($chooserId.'.choose()')
            ->setDisabled($element->getReadonly());
        $chooser->setData('after_element_html', $hiddenHtml . $chooseButton->toHtml());

        // render label and chooser scripts
        $configJson = Mage::helper('Mage_Core_Helper_Data')->jsonEncode($config->getData());
        return '
            <label class="widget-option-label" id="' . $chooserId . 'label">'
            . ($this->getLabel() ? $this->getLabel() : Mage::helper('Mage_Widget_Helper_Data')->__('Not Selected')) . '</label>
            <div id="' . $chooserId . 'advice-container" class="hidden"></div>
            <script type="text/javascript">
                ' . $chooserId . ' = new WysiwygWidget.chooser("' . $chooserId . '", "' . $this->getSourceUrl() . '", '
            . $configJson . ');
                if ($("'.$chooserId.'value")) {
                    $("'.$chooserId.'value").advaiceContainer = "'.$chooserId.'advice-container";
                }
            </script>
        ';
    }
}
