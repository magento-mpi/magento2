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
 * SEO Categories Sitemap block
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Block_Seo_Sitemap_Category extends Mage_Catalog_Block_Seo_Sitemap_Abstract
{

    /**
     * Initialize categories collection
     *
     * @return Mage_Catalog_Block_Seo_Sitemap_Category
     */
    protected function _prepareLayout()
    {
        $helper = Mage::helper('Mage_Catalog_Helper_Category');
        /* @var $helper Mage_Catalog_Helper_Category */
        $collection = $helper->getStoreCategories('name', true, false);
        $this->setCollection($collection);
        return $this;
    }

    /**
     * Get item URL
     *
     * @param Mage_Catalog_Model_Category $category
     * @return string
     */
    public function getItemUrl($category)
    {
        $helper = Mage::helper('Mage_Catalog_Helper_Category');
        /* @var $helper Mage_Catalog_Helper_Category */
        return $helper->getCategoryUrl($category);
    }

}
