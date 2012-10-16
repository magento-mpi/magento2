<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Product form category field helper
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Catalog_Product_Helper_Form_Category extends Varien_Data_Form_Element_Multiselect
{
    /**
     * Get values for select
     * @return array
     */
    public function getValues()
    {
        $collection = $this->_getCategoriesCollection();
        $values = $this->getValue();
        if (!is_array($values)) {
            $values = explode(',', $values);
        }
        $collection->addAttributeToSelect('name');
        $collection->addIdFilter($values);

        $options = array();

        foreach ($collection as $category) {
            $options[] = array(
                'label' => $category->getName(),
                'value' => $category->getId()
            );
        }
        return $options;
    }

    /**
     * Get categories collection
     * @return Mage_Catalog_Model_Resource_Category_Collection
     */
    protected function _getCategoriesCollection()
    {
        return Mage::getResourceModel('Mage_Catalog_Model_Resource_Category_Collection');
    }

    /**
     * Get html for element
     *
     * @return string
     */
    public function getElementHtml()
    {
        return parent::getElementHtml()
            . "<script>//<![CDATA[\n jQuery("
            . json_encode('#' . $this->getHtmlId())
            . ").categorySelector(" . $this->_getCodeHelper()->jsonEncode($this->_getSelectorOptions()) . ");
            \n//]]></script>";
    }

    /**
     * Get selector options
     *
     * @return array
     */
    protected function _getSelectorOptions()
    {
        return array(
            'url' => $this->_getBackendHelper()->getUrl('adminhtml/catalog_category/suggestCategories'),
        );
    }

    /**
     * Get backend area helper
     *
     * @return Mage_Backend_Helper_Data"
     */
    protected function _getBackendHelper()
    {
        return Mage::helper("Mage_Backend_Helper_Data");
    }

    /**
     * Get code module helper
     *
     * @return Mage_Backend_Helper_Data"
     */
    protected function _getCodeHelper()
    {
        return Mage::helper("Mage_Core_Helper_Data");
    }

}
