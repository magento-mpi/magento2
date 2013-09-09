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
 * Recurring profile attribute edit renderer
 */
class Magento_Adminhtml_Block_Catalog_Product_Edit_Tab_Price_Recurring
    extends Magento_Adminhtml_Block_Catalog_Form_Renderer_Fieldset_Element
{
    /**
     * Core event manager proxy
     *
     * @var Magento_Core_Model_Event_Manager_Proxy
     */
    protected $_eventManager = null;

    /**
     * @param Magento_Core_Model_Event_Manager_Proxy $eventManager
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Event_Manager_Proxy $eventManager,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_eventManager = $eventManager;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Element output getter
     *
     * @return string
     */
    public function getElementHtml()
    {
        $result = new StdClass;
        $result->output = '';
        $this->_eventManager->dispatch('catalog_product_edit_form_render_recurring', array(
            'result' => $result,
            'product_element' => $this->_element,
            'product'   => Mage::registry('current_product'),
        ));
        return $result->output;
    }
}
