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
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Catalog\Block\Adminhtml\Form\Renderer\Attribute;

class Urlkey
    extends \Magento\Catalog\Block\Adminhtml\Form\Renderer\Fieldset\Element
{
    /**
     * Catalog data
     *
     * @var \Magento\Catalog\Helper\Data
     */
    protected $_catalogData = null;

    /**
     * @var \Magento\Data\Form\Element\Factory
     */
    protected $_elementFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Data\Form\Element\Factory $elementFactory
     * @param \Magento\Catalog\Helper\Data $catalogData
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Data\Form\Element\Factory $elementFactory,
        \Magento\Catalog\Helper\Data $catalogData,
        array $data = array()
    ) {
        $this->_elementFactory = $elementFactory;
        $this->_catalogData = $catalogData;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getElementHtml()
    {
        /** @var \Magento\Data\Form\Element\AbstractElement $element */
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
        /** @var \Magento\Data\Form\Element\Hidden $hidden */
        $hidden = $this->_elementFactory->create('hidden', array('data' => $data));
        $hidden->setForm($element->getForm());

        $storeId = $element->getForm()->getDataObject()->getStoreId();
        $data['html_id'] = $element->getHtmlId() . '_create_redirect';
        $data['label'] = __('Create Permanent Redirect for old URL');
        $data['value'] = $element->getValue();
        $data['checked'] = $this->_catalogData->shouldSaveUrlRewritesHistory($storeId);
        /** @var \Magento\Data\Form\Element\Checkbox $checkbox */
        $checkbox = $this->_elementFactory->create('checkbox', array('data' => $data));
        $checkbox->setForm($element->getForm());

        return parent::getElementHtml() . '<br/>' . $hidden->getElementHtml() . $checkbox->getElementHtml() . $checkbox->getLabelHtml();
    }
}
