<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Eav
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * EAV entity attribute form fieldset element renderer
 *
 * @category   Enterprise
 * @package    Enterprise_Eav
 */
class Enterprise_Eav_Block_Adminhtml_Form_Renderer_Fieldset_Element
    extends Mage_Adminhtml_Block_Widget_Form_Renderer_Fieldset_Element
{
    /**
     * Retrieve data object related with form
     *
     * @return Magento_Object
     */
    public function getDataObject()
    {
        return $this->getElement()->getForm()->getDataObject();
    }

    /**
     * Check "Use default" checkbox display availability
     *
     * @return bool
     */
    public function canDisplayUseDefault()
    {
        $element = $this->getElement();
        if ($element) {
            if ($element->getScope() != 'global' && $element->getScope() != null && $this->getDataObject()
                && $this->getDataObject()->getId() && $this->getDataObject()->getWebsite()->getId()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check default value usage fact
     *
     * @return bool
     */
    public function usedDefault()
    {
        $key = $this->getElement()->getId();
        if (strpos($key, 'default_value_') === 0) {
            $key = 'default_value';
        }
        $storeValue = $this->getDataObject()->getData('scope_' . $key);
        return ($storeValue === null);
    }

    /**
     * Disable field in default value using case
     *
     * @return Enterprise_Eav_Block_Adminhtml_Form_Renderer_Fieldset_Element
     */
    public function checkFieldDisable()
    {
        if ($this->canDisplayUseDefault() && $this->usedDefault()) {
            $this->getElement()->setDisabled(true);
        }
        return $this;
    }

    /**
     * Retrieve label of attribute scope
     *
     * GLOBAL | WEBSITE | STORE
     *
     * @return string
     */
    public function getScopeLabel()
    {
        $html = '';
        $element = $this->getElement();
        if (Mage::app()->isSingleStoreMode()) {
            return $html;
        }

        if ($element->getScope() == 'global' || $element->getScope() === null) {
            $html = __('[GLOBAL]');
        } elseif ($element->getScope() == 'website') {
            $html = __('[WEBSITE]');
        } elseif ($element->getScope() == 'store') {
            $html = __('[STORE VIEW]');
        }

        return $html;
    }

    /**
     * Retrieve element label html
     *
     * @return string
     */
    public function getElementLabelHtml()
    {
        return $this->getElement()->getLabelHtml();
    }

    /**
     * Retrieve element html
     *
     * @return string
     */
    public function getElementHtml()
    {
        return $this->getElement()->getElementHtml();
    }
}
