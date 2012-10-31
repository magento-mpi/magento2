<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_GoogleOptimizer
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * GoogleOptimizer Cms page conversion renderer
 *
 * @category   Mage
 * @package    Mage_GoogleOptimizer
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_GoogleOptimizer_Block_Adminhtml_Cms_Page_Edit_Renderer_Conversion extends Mage_Adminhtml_Block_Widget_Form_Renderer_Fieldset_Element
{

    protected $_template = 'cms/edit/renderer/conversion.phtml';

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $this->_element = $element;
        return $this->toHtml();
    }

    public function getElement()
    {
        return $this->_element;
    }

    public function getStoreViews()
    {
        $storeViews = Mage::app()->getStores();
        return $storeViews;
    }

    public function getJsonStoreViews()
    {
        $storeViews = array();
        foreach ($this->getStoreViews() as $_store) {
            $storeViews[] = $_store->getCode();
        }
        $storeViews = new Varien_Object($storeViews);
        return $storeViews->toJson();
    }

    public function getJsonConversionPagesUrl()
    {
        $storeViewsUrls = array();
        foreach ($this->getStoreViews() as $_store) {
            Mage::helper('Mage_GoogleOptimizer_Helper_Data')->setStoreId($_store->getId());
            $storeViewsUrls[$_store->getCode()] = Mage::helper('Mage_GoogleOptimizer_Helper_Data')->getConversionPagesUrl()->getData();
        }
        $storeViewsUrls = new Varien_Object($storeViewsUrls);
        return $storeViewsUrls->toJson();
    }
}
