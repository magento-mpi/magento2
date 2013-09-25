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
 * Catalog product options collection
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Magento_Catalog_Model_Resource_Product_Option_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Store manager
     *
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Option value factory
     *
     * @var Magento_Catalog_Model_Resource_Product_Option_Value_CollectionFactory
     */
    protected $_optionValueCollectionFactory;

    /**
     * Construct
     *
     * @param Magento_Catalog_Model_Resource_Product_Option_Value_CollectionFactory $optionValueCollectionFactory
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy
     * @param Magento_Core_Model_EntityFactory $entityFactory
     * @param Magento_Core_Model_Resource_Db_Abstract $resource
     */
    public function __construct(
        Magento_Catalog_Model_Resource_Product_Option_Value_CollectionFactory $optionValueCollectionFactory,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Model_Logger $logger,
        Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy,
        Magento_Core_Model_EntityFactory $entityFactory,
        Magento_Core_Model_Resource_Db_Abstract $resource = null
    ) {
        $this->_optionValueCollectionFactory = $optionValueCollectionFactory;
        $this->_storeManager = $storeManager;
        parent::__construct($eventManager, $logger, $fetchStrategy, $entityFactory, $resource);
    }

    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init('Magento_Catalog_Model_Product_Option', 'Magento_Catalog_Model_Resource_Product_Option');
    }

    /**
     * Adds title, price & price_type attributes to result
     *
     * @param int $storeId
     * @return Magento_Catalog_Model_Resource_Product_Option_Collection
     */
    public function getOptions($storeId)
    {
        $this->addPriceToResult($storeId)
             ->addTitleToResult($storeId);

        return $this;
    }

    /**
     * Add title to result
     *
     * @param int $storeId
     * @return Magento_Catalog_Model_Resource_Product_Option_Collection
     */
    public function addTitleToResult($storeId)
    {
        $productOptionTitleTable = $this->getTable('catalog_product_option_title');
        $adapter        = $this->getConnection();
        $titleExpr      = $adapter->getCheckSql(
            'store_option_title.title IS NULL',
            'default_option_title.title',
            'store_option_title.title'
        );

        $this->getSelect()
            ->join(array('default_option_title' => $productOptionTitleTable),
                'default_option_title.option_id = main_table.option_id',
                array('default_title' => 'title'))
            ->joinLeft(
                array('store_option_title' => $productOptionTitleTable),
                'store_option_title.option_id = main_table.option_id AND '
                    . $adapter->quoteInto('store_option_title.store_id = ?', $storeId),
                array(
                    'store_title'   => 'title',
                    'title'         => $titleExpr
                ))
            ->where('default_option_title.store_id = ?', Magento_Catalog_Model_Abstract::DEFAULT_STORE_ID);

        return $this;
    }

    /**
     * Add price to result
     *
     * @param int $storeId
     * @return Magento_Catalog_Model_Resource_Product_Option_Collection
     */
    public function addPriceToResult($storeId)
    {
        $productOptionPriceTable = $this->getTable('catalog_product_option_price');
        $adapter        = $this->getConnection();
        $priceExpr      = $adapter->getCheckSql(
            'store_option_price.price IS NULL',
            'default_option_price.price',
            'store_option_price.price'
        );
        $priceTypeExpr  = $adapter->getCheckSql(
            'store_option_price.price_type IS NULL',
            'default_option_price.price_type',
            'store_option_price.price_type'
        );

        $this->getSelect()
            ->joinLeft(
                array('default_option_price' => $productOptionPriceTable),
                'default_option_price.option_id = main_table.option_id AND '
                    . $adapter->quoteInto(
                        'default_option_price.store_id = ?',
                        Magento_Catalog_Model_Abstract::DEFAULT_STORE_ID
                    ),
                array(
                    'default_price' => 'price',
                    'default_price_type' => 'price_type'
                ))
            ->joinLeft(
                array('store_option_price' => $productOptionPriceTable),
                'store_option_price.option_id = main_table.option_id AND '
                    . $adapter->quoteInto('store_option_price.store_id = ?', $storeId),
                array(
                    'store_price'       => 'price',
                    'store_price_type'  => 'price_type',
                    'price'             => $priceExpr,
                    'price_type'        => $priceTypeExpr
                ));

        return $this;
    }

    /**
     * Add value to result
     *
     * @param int $storeId
     * @return Magento_Catalog_Model_Resource_Product_Option_Collection
     */
    public function addValuesToResult($storeId = null)
    {
        if ($storeId === null) {
            $storeId = $this->_storeManager->getStore()->getId();
        }
        $optionIds = array();
        foreach ($this as $option) {
            $optionIds[] = $option->getId();
        }
        if (!empty($optionIds)) {
            /** @var Magento_Catalog_Model_Resource_Product_Option_Value_Collection $values */
            $values = $this->_optionValueCollectionFactory->create();
            $values->addTitleToResult($storeId)
                ->addPriceToResult($storeId)
                ->addOptionToFilter($optionIds)
                ->setOrder('sort_order', self::SORT_ORDER_ASC)
                ->setOrder('title', self::SORT_ORDER_ASC);

            foreach ($values as $value) {
                $optionId = $value->getOptionId();
                if($this->getItemById($optionId)) {
                    $this->getItemById($optionId)->addValue($value);
                    $value->setOption($this->getItemById($optionId));
                }
            }
        }

        return $this;
    }

    /**
     * Add product_id filter to select
     *
     * @param array|Magento_Catalog_Model_Product|int $product
     * @return Magento_Catalog_Model_Resource_Product_Option_Collection
     */
    public function addProductToFilter($product)
    {
        if (empty($product)) {
            $this->addFieldToFilter('product_id', '');
        } elseif (is_array($product)) {
            $this->addFieldToFilter('product_id', array('in' => $product));
        } elseif ($product instanceof Magento_Catalog_Model_Product) {
            $this->addFieldToFilter('product_id', $product->getId());
        } else {
            $this->addFieldToFilter('product_id', $product);
        }

        return $this;
    }

    /**
     * Add is_required filter to select
     *
     * @param bool $required
     * @return Magento_Catalog_Model_Resource_Product_Option_Collection
     */
    public function addRequiredFilter($required = true)
    {
        $this->addFieldToFilter('main_table.is_require', (string)$required);
        return $this;
    }

    /**
     * Add filtering by option ids
     *
     * @param mixed $optionIds
     * @return Magento_Catalog_Model_Resource_Product_Option_Collection
     */
    public function addIdsToFilter($optionIds)
    {
        $this->addFieldToFilter('main_table.option_id', $optionIds);
        return $this;
    }

    /**
     * Call of protected method reset
     *
     * @return Magento_Catalog_Model_Resource_Product_Option_Collection
     */
    public function reset()
    {
        return $this->_reset();
    }
}
