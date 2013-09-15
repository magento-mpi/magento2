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
 * Renderer for URL key input
 * Allows to manage and overwrite URL Rewrites History save settings
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Magento_Adminhtml_Block_Catalog_Form_Renderer_Attribute_Urlkey
    extends Magento_Adminhtml_Block_Catalog_Form_Renderer_Fieldset_Element
{
    /**
     * Catalog data
     *
     * @var Magento_Catalog_Helper_Data
     */
    protected $_catalogData = null;

    /**
     * @var Magento_Data_Form_Element_Factory
     */
    protected $_elementFactory;

    /**
     * @param Magento_Data_Form_Element_Factory $elementFactory
     * @param Magento_Catalog_Helper_Data $catalogData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Data_Form_Element_Factory $elementFactory,
        Magento_Catalog_Helper_Data $catalogData,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_elementFactory = $elementFactory;
        $this->_catalogData = $catalogData;
        parent::__construct($coreData, $context, $data);
    }

    public function getElementHtml()
    {
        /** @var Magento_Data_Form_Element_Abstract $element */
        $element = $this->getElement();
        if(!$element->getValue()) {
            return parent::getElementHtml();
        }
        $element->setOnkeyup("onUrlkeyChanged('" . $element->getHtmlId() . "')");
        $element->setOnchange("onUrlkeyChanged('" . $element->getHtmlId() . "')");

        $data = array(
            'name' => $element->getData('name') . '_create_redirect',
            'disabled' => true,
        );
        /** @var Magento_Data_Form_Element_Hidden $hidden */
        $hidden = $this->_elementFactory->create('hidden', array('attributes' => $data));
        $hidden->setForm($element->getForm());

        $storeId = $element->getForm()->getDataObject()->getStoreId();
        $data['html_id'] = $element->getHtmlId() . '_create_redirect';
        $data['label'] = __('Create Permanent Redirect for old URL');
        $data['value'] = $element->getValue();
        $data['checked'] = $this->_catalogData->shouldSaveUrlRewritesHistory($storeId);
        /** @var Magento_Data_Form_Element_Checkbox $checkbox */
        $checkbox = $this->_elementFactory->create('checkbox', array('attributes' => $data));
        $checkbox->setForm($element->getForm());

        return parent::getElementHtml() . '<br/>' . $hidden->getElementHtml() . $checkbox->getElementHtml() . $checkbox->getLabelHtml();
    }
}
