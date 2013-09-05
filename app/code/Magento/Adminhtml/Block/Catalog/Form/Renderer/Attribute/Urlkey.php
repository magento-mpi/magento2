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
     * @var Magento_Data_Form_ElementFactory
     */
    protected $_elementFactory;

    /**
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Data_Form_ElementFactory $elementFactory
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Block_Template_Context $context,
        Magento_Data_Form_ElementFactory $elementFactory,
        array $data = array()
    ) {
        $this->_elementFactory = $elementFactory;
        parent::__construct($context, $data);
    }

    public function getElementHtml()
    {
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
        $hidden =  $this->_elementFactory->create('Magento_Data_Form_Element_Hidden', $data);
        $hidden->setForm($element->getForm());

        $storeId = $element->getForm()->getDataObject()->getStoreId();
        $data['html_id'] = $element->getHtmlId() . '_create_redirect';
        $data['label'] = __('Create Permanent Redirect for old URL');
        $data['value'] = $element->getValue();
        $data['checked'] = Mage::helper('Magento_Catalog_Helper_Data')->shouldSaveUrlRewritesHistory($storeId);
        $checkbox = $this->_elementFactory->create('Magento_Data_Form_Element_Checkbox', $data);
        $checkbox->setForm($element->getForm());

        return parent::getElementHtml() . '<br/>' . $hidden->getElementHtml() . $checkbox->getElementHtml() . $checkbox->getLabelHtml();
    }
}
