<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reports
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Most viewed product report aggregate resource model
 *
 * @category    Magento
 * @package     Magento_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Reports_Model_Resource_Report_Product_Viewed extends Magento_Sales_Model_Resource_Report_Abstract
{
    /**
     * Aggregation key daily
     */
    const AGGREGATION_DAILY   = 'report_viewed_product_aggregated_daily';

    /**
     * Aggregation key monthly
     */
    const AGGREGATION_MONTHLY = 'report_viewed_product_aggregated_monthly';

    /**
     * Aggregation key yearly
     */
    const AGGREGATION_YEARLY  = 'report_viewed_product_aggregated_yearly';

    /**
     * @var Magento_Catalog_Model_Resource_Product
     */
    protected $_productResource;

    /**
     * @var Magento_Reports_Model_Resource_HelperFactory
     */
    protected $_helperFactory;

    /**
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Core_Model_Resource $resource
     * @param Magento_Core_Model_LocaleInterface $locale
     * @param Magento_Reports_Model_FlagFactory $reportsFlagFactory
     * @param Magento_Catalog_Model_Resource_Product $productResource
     * @param Magento_Reports_Model_Resource_HelperFactory $helperFactory
     */
    public function __construct(
        Magento_Core_Model_Logger $logger,
        Magento_Core_Model_Resource $resource,
        Magento_Core_Model_LocaleInterface $locale,
        Magento_Reports_Model_FlagFactory $reportsFlagFactory,
        Magento_Catalog_Model_Resource_Product $productResource,
        Magento_Reports_Model_Resource_HelperFactory $helperFactory
    ) {
        parent::__construct($logger, $resource, $locale, $reportsFlagFactory);
        $this->_productResource = $productResource;
        $this->_helperFactory = $helperFactory;
    }

    /**
     * Model initialization
     *
     */
    protected function _construct()
    {
        $this->_init(self::AGGREGATION_DAILY, 'id');
    }

    /**
     * Aggregate products view data
     *
     * @param mixed $from
     * @param mixed $to
     * @return Magento_Sales_Model_Resource_Report_Bestsellers
     */
    public function aggregate($from = null, $to = null)
    {
        $mainTable   = $this->getMainTable();
        $adapter = $this->_getWriteAdapter();

        // convert input dates to UTC to be comparable with DATETIME fields in DB
        $from = $this->_dateToUtc($from);
        $to = $this->_dateToUtc($to);

        $this->_checkDates($from, $to);

        if ($from !== null || $to !== null) {
            $subSelect = $this->_getTableDateRangeSelect(
                $this->getTable('report_event'),
                'logged_at', 'logged_at', $from, $to
            );
        } else {
            $subSelect = null;
        }
        $this->_clearTableByDateRange($mainTable, $from, $to, $subSelect);
        // convert dates from UTC to current admin timezone
        $periodExpr = $adapter->getDatePartSql(
            $this->getStoreTZOffsetQuery(
                array('source_table' => $this->getTable('report_event')),
                'source_table.logged_at', $from, $to
            )
        );
        $select = $adapter->select();

        $select->group(array(
            $periodExpr,
            'source_table.store_id',
            'source_table.object_id'
        ));

        $viewsNumExpr = new Zend_Db_Expr('COUNT(source_table.event_id)');

        $columns = array(
            'period'                 => $periodExpr,
            'store_id'               => 'source_table.store_id',
            'product_id'             => 'source_table.object_id',
            'product_name'           => new Zend_Db_Expr(sprintf('MIN(%s)', $adapter->getIfNullSql(
                'product_name.value',
                'product_default_name.value'
            ))),
            'product_price' => new Zend_Db_Expr(sprintf('MIN(%s)', $adapter->getIfNullSql(
                $adapter->getIfNullSql('product_price.value', 'product_default_price.value'), 0
            ))),
            'views_num' => $viewsNumExpr
        );

        $select->from(array('source_table' => $this->getTable('report_event')), $columns)
            ->where('source_table.event_type_id = ?', Magento_Reports_Model_Event::EVENT_PRODUCT_VIEW);

        $select->joinInner(
            array('product' => $this->getTable('catalog_product_entity')),
            'product.entity_id = source_table.object_id',
            array()
        );

        // join product attributes Name & Price
        $nameAttribute = $this->_productResource->getAttribute('name');
        $joinExprProductName = array(
            'product_name.entity_id = product.entity_id',
            'product_name.store_id = source_table.store_id',
            $adapter->quoteInto('product_name.attribute_id = ?', $nameAttribute->getAttributeId())
        );
        $joinExprProductName = implode(' AND ', $joinExprProductName);
        $joinProductName = array(
            'product_default_name.entity_id = product.entity_id',
            'product_default_name.store_id = 0',
            $adapter->quoteInto('product_default_name.attribute_id = ?', $nameAttribute->getAttributeId())
        );
        $joinProductName = implode(' AND ', $joinProductName);
        $select->joinLeft(
            array('product_name' => $nameAttribute->getBackend()->getTable()),
            $joinExprProductName,
            array()
        )
        ->joinLeft(
            array('product_default_name' => $nameAttribute->getBackend()->getTable()),
            $joinProductName,
            array()
        );
        $priceAttribute = $this->_productResource->getAttribute('price');
        $joinExprProductPrice = array(
            'product_price.entity_id = product.entity_id',
            'product_price.store_id = source_table.store_id',
            $adapter->quoteInto('product_price.attribute_id = ?', $priceAttribute->getAttributeId())
        );
        $joinExprProductPrice = implode(' AND ', $joinExprProductPrice);

        $joinProductPrice = array(
            'product_default_price.entity_id = product.entity_id',
            'product_default_price.store_id = 0',
            $adapter->quoteInto('product_default_price.attribute_id = ?', $priceAttribute->getAttributeId())
        );
        $joinProductPrice = implode(' AND ', $joinProductPrice);
        $select->joinLeft(
            array('product_price' => $priceAttribute->getBackend()->getTable()),
            $joinExprProductPrice,
            array()
        )
        ->joinLeft(
            array('product_default_price' => $priceAttribute->getBackend()->getTable()),
            $joinProductPrice,
            array()
        );

        $havingPart = array($adapter->prepareSqlCondition($viewsNumExpr, array('gt' => 0)));
        if (null !== $subSelect) {
            $subSelectHavingPart = $this->_makeConditionFromDateRangeSelect($subSelect, 'period');
            if ($subSelectHavingPart) {
                $havingPart[] = '(' . $subSelectHavingPart . ')';
            }
        }
        $select->having(implode(' AND ', $havingPart));

        $select->useStraightJoin();
        $insertQuery = $select->insertFromSelect($this->getMainTable(), array_keys($columns));
        $adapter->query($insertQuery);

        $helper = $this->_helperFactory->create();
        $helper->updateReportRatingPos('day', 'views_num', $mainTable, $this->getTable(self::AGGREGATION_DAILY));
        $helper->updateReportRatingPos('month', 'views_num', $mainTable, $this->getTable(self::AGGREGATION_MONTHLY));
        $helper->updateReportRatingPos('year', 'views_num', $mainTable, $this->getTable(self::AGGREGATION_YEARLY));

        $this->_setFlagData(Magento_Reports_Model_Flag::REPORT_PRODUCT_VIEWED_FLAG_CODE);

        return $this;
    }
}
