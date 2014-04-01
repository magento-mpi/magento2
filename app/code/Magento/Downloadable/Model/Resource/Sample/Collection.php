<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Downloadable\Model\Resource\Sample;

/**
 * Downloadable samples resource collection
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Collection extends \Magento\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Init resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\Downloadable\Model\Sample', 'Magento\Downloadable\Model\Resource\Sample');
    }

    /**
     * Method for product filter
     *
     * @param \Magento\Catalog\Model\Product|array|int|null $product
     * @return $this
     */
    public function addProductToFilter($product)
    {
        if (empty($product)) {
            $this->addFieldToFilter('product_id', '');
        } elseif (is_array($product)) {
            $this->addFieldToFilter('product_id', array('in' => $product));
        } else {
            $this->addFieldToFilter('product_id', $product);
        }

        return $this;
    }

    /**
     * Add title column to select
     *
     * @param int $storeId
     * @return $this
     */
    public function addTitleToResult($storeId = 0)
    {
        $ifNullDefaultTitle = $this->getConnection()->getIfNullSql('st.title', 'd.title');
        $this->getSelect()->joinLeft(
            array('d' => $this->getTable('downloadable_sample_title')),
            'd.sample_id=main_table.sample_id AND d.store_id = 0',
            array('default_title' => 'title')
        )->joinLeft(
            array('st' => $this->getTable('downloadable_sample_title')),
            'st.sample_id=main_table.sample_id AND st.store_id = ' . (int)$storeId,
            array('store_title' => 'title', 'title' => $ifNullDefaultTitle)
        )->order(
            'main_table.sort_order ASC'
        )->order(
            'title ASC'
        );

        return $this;
    }
}
