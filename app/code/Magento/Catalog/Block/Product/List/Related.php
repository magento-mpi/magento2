<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog product related items block
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Block_Product_List_Related extends Magento_Catalog_Block_Product_Abstract
{
    /**
     * Default MAP renderer type
     *
     * @var string
     */
    protected $_mapRenderer = 'msrp_noform';

    protected $_itemCollection;

    public function __construct(Magento_Tax_Helper_Data $taxData, Magento_Catalog_Helper_Data $catalogData, Magento_Core_Helper_Data $coreData, Magento_Core_Block_Template_Context $context, array $data = array())
    {
        parent::__construct($taxData, $catalogData, $coreData, $context, $data);
    }

    protected function _prepareData()
    {
        $product = Mage::registry('product');
        /* @var $product Magento_Catalog_Model_Product */

        $this->_itemCollection = $product->getRelatedProductCollection()
            ->addAttributeToSelect('required_options')
            ->setPositionOrder()
            ->addStoreFilter()
        ;

        if ($this->_catalogData->isModuleEnabled('Magento_Checkout')) {
            Mage::getResourceSingleton('Magento_Checkout_Model_Resource_Cart')
                ->addExcludeProductFilter(
                    $this->_itemCollection,
                    Mage::getSingleton('Magento_Checkout_Model_Session')->getQuoteId()
                );
            $this->_addProductAttributesAndPrices($this->_itemCollection);
        }
        $this->_itemCollection->setVisibility(
            Mage::getSingleton('Magento_Catalog_Model_Product_Visibility')->getVisibleInCatalogIds()
        );

        $this->_itemCollection->load();

        foreach ($this->_itemCollection as $product) {
            $product->setDoNotUseCategoryId(true);
        }

        return $this;
    }

    protected function _beforeToHtml()
    {
        $this->_prepareData();
        return parent::_beforeToHtml();
    }

    public function getItems()
    {
        return $this->_itemCollection;
    }
}
