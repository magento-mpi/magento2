<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_CatalogEvent
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog Events form fieldset element renderer
 *
 * @category   Enterprise
 * @package    Enterprise_CatalogEvent
 */
class Enterprise_CatalogEvent_Block_Adminhtml_Form_Renderer_Fieldset_Element
    extends Mage_Adminhtml_Block_Widget_Form_Renderer_Fieldset_Element
{
    protected $_template = 'form/renderer/fieldset/element.phtml';

    /**
     * Retrieve data object related with form
     *
     * @return Varien_Object
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
        if ($element = $this->getElement()) {
            if ($element->getScope() != 'global'
                && $element->getScope() != null
                && $this->getDataObject()
                && $this->getDataObject()->getId()
                && $this->getDataObject()->getStoreId()) {
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
        $defaultValue = $this->getDataObject()->getData($this->getElement()->getId() . '_default');
        return $defaultValue === null;
    }

    /**
     * Disable field in default value using case
     *
     * @return Enterprise_CatalogEvent_Block_Adminhtml_Form_Renderer_Fieldset_Element
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
            $html .= Mage::helper('Mage_Adminhtml_Helper_Data')->__('[GLOBAL]');
        } elseif ($element->getScope() == 'website') {
            $html .= Mage::helper('Mage_Adminhtml_Helper_Data')->__('[WEBSITE]');
        } elseif ($element->getScope() == 'store') {
            $html .= Mage::helper('Mage_Adminhtml_Helper_Data')->__('[STORE VIEW]');
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