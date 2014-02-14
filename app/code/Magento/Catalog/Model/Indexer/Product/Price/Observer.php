<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Indexer\Product\Price;

class Observer
{
    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\App\Resource
     */
    protected $_resource;

    /**
     * @var \Magento\Stdlib\DateTime
     */
    protected $_dateTime;

    /**
     * @var \Magento\Core\Model\LocaleInterface
     */
    protected $_locale;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $_eavConfig;

    /**
     * @var \Magento\Catalog\Model\Indexer\Product\Price\Processor
     */
    protected $_processor;

    /**
     * @var \Magento\DB\Adapter\AdapterInterface
     */
    protected $_connection;

    /**
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\App\Resource $resource
     * @param \Magento\Stdlib\DateTime $dateTime
     * @param \Magento\Core\Model\LocaleInterface $locale
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Catalog\Model\Indexer\Product\Price\Processor $processor
     */
    public function __construct(
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\App\Resource $resource,
        \Magento\Stdlib\DateTime $dateTime,
        \Magento\Core\Model\LocaleInterface $locale,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Catalog\Model\Indexer\Product\Price\Processor $processor
    ) {
        $this->_storeManager = $storeManager;
        $this->_resource = $resource;
        $this->_dateTime = $dateTime;
        $this->_locale = $locale;
        $this->_eavConfig = $eavConfig;
        $this->_processor = $processor;
    }

    /**
     * Retrieve write connection instance
     *
     * @return bool|\Magento\DB\Adapter\AdapterInterface
     */
    protected function _getWriteConnection()
    {
        if (null === $this->_connection) {
            $this->_connection = $this->_resource->getConnection('write');
        }
        return $this->_connection;
    }

    /**
     * Add products to changelog with price which depends on date
     */
    public function refreshSpecialPrices()
    {
        $connection = $this->_getWriteConnection();

        foreach ($this->_storeManager->getStores(true) as $store) {
            $timestamp = $this->_locale->storeTimeStamp($store);
            $currDate = $this->_dateTime->formatDate($timestamp, false);
            $currDateExpr = $connection->quote($currDate);

            // timestamp is locale based
            if (date(\Zend_Date::HOUR_SHORT, $timestamp) == '00') {
                $format = '%Y-%m-%d %H:%i:%s';
                $this->_refreshSpecialPriceByStore(
                    $store->getId(), 'special_from_date', $connection->getDateFormatSql($currDateExpr, $format)
                );

                $dateTo = $connection->getDateAddSql(
                    $currDateExpr, -1, \Magento\DB\Adapter\AdapterInterface::INTERVAL_DAY
                );
                $this->_refreshSpecialPriceByStore(
                    $store->getId(), 'special_to_date', $connection->getDateFormatSql($dateTo, $format)
                );
            }
        }
    }

    /**
     * Reindex affected products
     *
     * @param int $storeId
     * @param string $attrCode
     * @param \Zend_Db_Expr $attrConditionValue
     */
    protected function _refreshSpecialPriceByStore($storeId, $attrCode, $attrConditionValue)
    {
        $attribute = $this->_eavConfig->getAttribute(\Magento\Catalog\Model\Product::ENTITY, $attrCode);
        $attributeId = $attribute->getAttributeId();

        $connection = $this->_getWriteConnection();

        $select = $connection->select()
            ->from($this->_resource->getTableName(array('catalog_product_entity', 'datetime')), array('entity_id'))
            ->where('attribute_id = ?', $attributeId)
            ->where('store_id = ?', $storeId)
            ->where('value = ?', $attrConditionValue);

        $this->_processor->getIndexer()->reindexList(
            $connection->fetchCol($select, array('entity_id'))
        );
    }
}
