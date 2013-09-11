<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Gift registry form fieldset element renderer
 *
 * @category   Magento
 * @package    Magento_GiftRegistry
 */
namespace Magento\GiftRegistry\Block\Adminhtml\Giftregistry\Form\Renderer;

class Element
    extends \Magento\Adminhtml\Block\Widget\Form\Renderer\Fieldset\Element
{
    protected $_template = 'form/renderer/element.phtml';

    /**
     * Retrieve data object related with form
     *
     * @return \Magento\Object
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
        $storeValue = $this->getDataObject()->getData($this->getElement()->getId() . '_store');
        return $storeValue === null;
    }

    /**
     * Disable field in default value using case
     *
     * @return Magento_GiftRegistry_Block_Adminhtml_Giftregistry_Form_Renderer_Fieldset_Element
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
        if (\Mage::app()->isSingleStoreMode()) {
            return $html;
        }
        if ($element->getScope() == 'global' || $element->getScope() === null) {
            $html .= __('[GLOBAL]');
        } elseif ($element->getScope() == 'website') {
            $html .= __('[WEBSITE]');
        } elseif ($element->getScope() == 'store') {
            $html .= __('[STORE VIEW]');
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
