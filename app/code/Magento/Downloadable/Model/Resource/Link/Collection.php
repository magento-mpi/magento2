<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Downloadable links resource collection
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Downloadable_Model_Resource_Link_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Init resource model
     */
    protected function _construct()
    {
        $this->_init('Magento_Downloadable_Model_Link', 'Magento_Downloadable_Model_Resource_Link');
    }

    /**
     * Method for product filter
     *
     * @param Magento_Catalog_Model_Product|array|integer|null $product
     * @return Magento_Downloadable_Model_Resource_Link_Collection
     */
    public function addProductToFilter($product)
    {
        if (empty($product)) {
            $this->addFieldToFilter('product_id', '');
        } elseif ($product instanceof Magento_Catalog_Model_Product) {
            $this->addFieldToFilter('product_id', $product->getId());
        } else {
            $this->addFieldToFilter('product_id', array('in' => $product));
        }

        return $this;
    }

    /**
     * Retrieve title for for current store
     *
     * @param integer $storeId
     * @return Magento_Downloadable_Model_Resource_Link_Collection
     */
    public function addTitleToResult($storeId = 0)
    {
        $ifNullDefaultTitle = $this->getConnection()
            ->getIfNullSql('st.title', 'd.title');
        $this->getSelect()
            ->joinLeft(array('d' => $this->getTable('downloadable_link_title')),
                'd.link_id=main_table.link_id AND d.store_id = 0',
                array('default_title' => 'title'))
            ->joinLeft(array('st' => $this->getTable('downloadable_link_title')),
                'st.link_id=main_table.link_id AND st.store_id = ' . (int)$storeId,
                array('store_title' => 'title','title' => $ifNullDefaultTitle))
            ->order('main_table.sort_order ASC')
            ->order('title ASC');

        return $this;
    }

    /**
     * Retrieve price for for current website
     *
     * @param integer $websiteId
     * @return Magento_Downloadable_Model_Resource_Link_Collection
     */
    public function addPriceToResult($websiteId)
    {
        $ifNullDefaultPrice = $this->getConnection()
            ->getIfNullSql('stp.price', 'dp.price');
        $this->getSelect()
            ->joinLeft(array('dp' => $this->getTable('downloadable_link_price')),
                'dp.link_id=main_table.link_id AND dp.website_id = 0',
                array('default_price' => 'price'))
            ->joinLeft(array('stp' => $this->getTable('downloadable_link_price')),
                'stp.link_id=main_table.link_id AND stp.website_id = ' . (int)$websiteId,
                array('website_price' => 'price','price' => $ifNullDefaultPrice));

        return $this;
    }
}
