<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * SEO Products Sitemap block
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Block_Seo_Sitemap_Product extends Mage_Catalog_Block_Seo_Sitemap_Abstract
{

    /**
     * Initialize products collection
     *
     * @return Mage_Catalog_Block_Seo_Sitemap_Category
     */
    protected function _prepareLayout()
    {
        $collection = Mage::getModel('Mage_Catalog_Model_Product')->getCollection();
        /* @var $collection Mage_Catalog_Model_Resource_Product_Collection */

        $collection->addAttributeToSelect('name')
            ->addAttributeToSelect('url_key')
            ->addStoreFilter()
            ->setVisibility(Mage::getSingleton('Mage_Catalog_Model_Product_Visibility')->getVisibleInCatalogIds());

        $this->setCollection($collection);

        return $this;
    }

    /**
     * Get item URL
     *
     * @param Mage_Catalog_Model_Product $category
     * @return string
     */
    public function getItemUrl($product)
    {
        $helper = Mage::helper('Mage_Catalog_Helper_Product');
        /* @var $helper Mage_Catalog_Helper_Product */
        return $helper->getProductUrl($product);
    }

}
