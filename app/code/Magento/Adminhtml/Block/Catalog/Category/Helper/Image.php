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
 * Category form image field helper
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Catalog_Category_Helper_Image extends Magento_Data_Form_Element_Image
{
    /**
     * @var Magento_Core_Model_StoreManager
     */
    protected $_storeManager;

    /**
     * @param Magento_Core_Model_StoreManager $storeManager
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Data_Form_Element_Factory $factoryElement
     * @param Magento_Data_Form_Element_CollectionFactory $factoryCollection
     * @param Magento_Core_Model_UrlInterface $urlBuilder
     * @param array $attributes
     */
    public function __construct(
        Magento_Core_Model_StoreManager $storeManager,
        Magento_Core_Helper_Data $coreData,
        Magento_Data_Form_Element_Factory $factoryElement,
        Magento_Data_Form_Element_CollectionFactory $factoryCollection,
        Magento_Core_Model_UrlInterface $urlBuilder,
        $attributes = array()
    ) {
        $this->_storeManager = $storeManager;
        parent::__construct($coreData, $factoryElement, $factoryCollection, $urlBuilder, $attributes);
    }

    protected function _getUrl()
    {
        $url = false;
        if ($this->getValue()) {
            $url = $this->_storeManager->getStore()->getBaseUrl('media') . 'catalog/category/' . $this->getValue();
        }
        return $url;
    }
}
