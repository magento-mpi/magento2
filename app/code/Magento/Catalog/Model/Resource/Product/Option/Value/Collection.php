<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Resource\Product\Option\Value;

/**
 * Catalog product option values collection
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Collection
    extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\Catalog\Model\Product\Option\Value', 'Magento\Catalog\Model\Resource\Product\Option\Value');
    }

    /**
     * Add price, title to result
     *
     * @param int $storeId
     * @return $this
     */
    public function getValues($storeId)
    {
        $this->addPriceToResult($storeId)
             ->addTitleToResult($storeId);

        return $this;
    }

    /**
     * Add titles to result
     *
     * @param int $storeId
     * @return $this
     */
    public function addTitlesToResult($storeId)
    {
        $adapter = $this->getConnection();
        $optionTypePriceTable = $this->getTable('catalog_product_option_type_price');
        $optionTitleTable     = $this->getTable('catalog_product_option_type_title');
        $priceExpr = $adapter->getCheckSql(
            'store_value_price.price IS NULL',
            'default_value_price.price',
            'store_value_price.price'
        );
        $priceTypeExpr = $adapter->getCheckSql(
            'store_value_price.price_type IS NULL',
            'default_value_price.price_type',
            'store_value_price.price_type'
        );
        $titleExpr = $adapter->getCheckSql(
            'store_value_title.title IS NULL',
            'default_value_title.title',
            'store_value_title.title'
        );
        $joinExprDefaultPrice = 'default_value_price.option_type_id = main_table.option_type_id AND '
                  . $adapter->quoteInto('default_value_price.store_id = ?', \Magento\Store\Model\Store::DEFAULT_STORE_ID);

        $joinExprStorePrice = 'store_value_price.option_type_id = main_table.option_type_id AND '
                       . $adapter->quoteInto('store_value_price.store_id = ?', $storeId);

        $joinExprTitle = 'store_value_title.option_type_id = main_table.option_type_id AND '
                       . $adapter->quoteInto('store_value_title.store_id = ?', $storeId);

        $this->getSelect()
            ->joinLeft(
                array('default_value_price' => $optionTypePriceTable),
                $joinExprDefaultPrice,
                array('default_price'=>'price','default_price_type'=>'price_type')
            )
            ->joinLeft(
                array('store_value_price' => $optionTypePriceTable),
                $joinExprStorePrice,
                array(
                    'store_price'       => 'price',
                    'store_price_type'  => 'price_type',
                    'price'             => $priceExpr,
                    'price_type'        => $priceTypeExpr
                )
            )
            ->join(
                array('default_value_title' => $optionTitleTable),
                'default_value_title.option_type_id = main_table.option_type_id',
                array('default_title' => 'title')
            )
            ->joinLeft(
                array('store_value_title' => $optionTitleTable),
                $joinExprTitle,
                array(
                    'store_title' => 'title',
                    'title'       => $titleExpr)
            )
            ->where('default_value_title.store_id = ?', \Magento\Store\Model\Store::DEFAULT_STORE_ID);

        return $this;
    }

    /**
     * Add title result
     *
     * @param int $storeId
     * @return $this
     */
    public function addTitleToResult($storeId)
    {
        $optionTitleTable = $this->getTable('catalog_product_option_type_title');
        $titleExpr = $this->getConnection()
            ->getCheckSql('store_value_title.title IS NULL', 'default_value_title.title', 'store_value_title.title');

        $joinExpr = 'store_value_title.option_type_id = main_table.option_type_id AND '
                  . $this->getConnection()->quoteInto('store_value_title.store_id = ?', $storeId);
        $this->getSelect()
            ->join(
                array('default_value_title' => $optionTitleTable),
                'default_value_title.option_type_id = main_table.option_type_id',
                array('default_title' => 'title')
            )
            ->joinLeft(
                array('store_value_title' => $optionTitleTable),
                $joinExpr,
                array(
                    'store_title'   => 'title',
                    'title'         => $titleExpr
                )
            )
            ->where('default_value_title.store_id = ?', \Magento\Store\Model\Store::DEFAULT_STORE_ID);

        return $this;
    }

    /**
     * Add price to result
     *
     * @param int $storeId
     * @return $this
     */
    public function addPriceToResult($storeId)
    {
        $optionTypeTable = $this->getTable('catalog_product_option_type_price');
        $priceExpr = $this->getConnection()
            ->getCheckSql('store_value_price.price IS NULL', 'default_value_price.price', 'store_value_price.price');
        $priceTypeExpr = $this->getConnection()
            ->getCheckSql(
                'store_value_price.price_type IS NULL',
                'default_value_price.price_type',
                'store_value_price.price_type'
            );

        $joinExprDefault = 'default_value_price.option_type_id = main_table.option_type_id AND '
                        . $this->getConnection()->quoteInto('default_value_price.store_id = ?', \Magento\Store\Model\Store::DEFAULT_STORE_ID);
        $joinExprStore = 'store_value_price.option_type_id = main_table.option_type_id AND '
                       . $this->getConnection()->quoteInto('store_value_price.store_id = ?', $storeId);
        $this->getSelect()
            ->joinLeft(
                array('default_value_price' => $optionTypeTable),
                $joinExprDefault,
                array(
                    'default_price' => 'price',
                    'default_price_type'=>'price_type'
                )
            )
            ->joinLeft(
                array('store_value_price' => $optionTypeTable),
                $joinExprStore,
                array(
                    'store_price'       => 'price',
                    'store_price_type'  => 'price_type',
                    'price'             => $priceExpr,
                    'price_type'        => $priceTypeExpr
                )
            );

        return $this;
    }

    /**
     * Add option filter
     *
     * @param array $optionIds
     * @param int $storeId
     * @return $this
     */
    public function getValuesByOption($optionIds, $storeId = null)
    {
        if (!is_array($optionIds)) {
            $optionIds = array($optionIds);
        }

        return $this->addFieldToFilter('main_table.option_type_id', array('in' => $optionIds));
    }

    /**
     * Add option to filter
     *
     * @param array|\Magento\Catalog\Model\Product\Option|int $option
     * @return $this
     */
    public function addOptionToFilter($option)
    {
        if (empty($option)) {
            $this->addFieldToFilter('option_id', '');
        } elseif (is_array($option)) {
            $this->addFieldToFilter('option_id', array('in' => $option));
        } elseif ($option instanceof \Magento\Catalog\Model\Product\Option) {
            $this->addFieldToFilter('option_id', $option->getId());
        } else {
            $this->addFieldToFilter('option_id', $option);
        }

        return $this;
    }
}
