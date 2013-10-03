<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomAttribute
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * EAV entity attribute form fieldset element renderer
 *
 * @category   Magento
 * @package    Magento_CustomAttribute
 */
namespace Magento\CustomAttribute\Block\Adminhtml\Form\Renderer\Fieldset;

class Element
    extends \Magento\Backend\Block\Widget\Form\Renderer\Fieldset\Element
{
    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        array $data = array()
    ) {
        $this->_storeManager = $storeManager;
        parent::__construct($coreData, $context, $data);
    }


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
     * @return \Magento\CustomAttribute\Block\Adminhtml\Form\Renderer\Fieldset\Element
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
        if ($this->_storeManager->isSingleStoreMode()) {
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
