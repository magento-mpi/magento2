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
 *  Refresh Statistic Grid collection
 *
 * @category    Magento
 * @package     Magento_Report
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Reports_Model_Resource_Refresh_Collection extends Magento_Data_Collection
{

    /**
     * @var Magento_Core_Model_LocaleInterface
     */
    protected $_locale;

    /**
     * @var Magento_Reports_Model_FlagFactory
     */
    protected $_reportsFlagFactory;

    /**
     * @param Magento_Core_Model_EntityFactory $entityFactory
     * @param Magento_Core_Model_LocaleInterface $locale
     * @param Magento_Reports_Model_FlagFactory $reportsFlagFactory
     */
    public function __construct(
        Magento_Core_Model_EntityFactory $entityFactory,
        Magento_Core_Model_LocaleInterface $locale,
        Magento_Reports_Model_FlagFactory $reportsFlagFactory
    ) {
        parent::__construct($entityFactory);
        $this->_locale = $locale;
        $this->_reportsFlagFactory = $reportsFlagFactory;
    }

    /**
     * Get if updated
     *
     * @param $reportCode
     * @return string|Zend_Date
     */
    protected function _getUpdatedAt($reportCode)
    {
        $flag = $this->_reportsFlagFactory
            ->create()
            ->setReportFlagCode($reportCode)
            ->loadSelf();
        return ($flag->hasData())
            ? $this->_locale
                ->storeDate(0, new Zend_Date($flag->getLastUpdate(), Magento_Date::DATETIME_INTERNAL_FORMAT), true)
            : '';
    }

    /**
     * Load data
     * @return Magento_Reports_Model_Resource_Refresh_Collection|Magento_Data_Collection
     */
    public function loadData($printQuery = false, $logQuery = false)
    {
        if (!count($this->_items)) {
            $data = array(
                array(
                    'id'            => 'sales',
                    'report'        => __('Orders'),
                    'comment'       => __('Total Ordered Report'),
                    'updated_at'    => $this->_getUpdatedAt(Magento_Reports_Model_Flag::REPORT_ORDER_FLAG_CODE)
                ),
                array(
                    'id'            => 'tax',
                    'report'        => __('Tax'),
                    'comment'       => __('Order Taxes Report Grouped by Tax Rates'),
                    'updated_at'    => $this->_getUpdatedAt(Magento_Reports_Model_Flag::REPORT_TAX_FLAG_CODE)
                ),
                array(
                    'id'            => 'shipping',
                    'report'        => __('Shipping'),
                    'comment'       => __('Total Shipped Report'),
                    'updated_at'    => $this->_getUpdatedAt(Magento_Reports_Model_Flag::REPORT_SHIPPING_FLAG_CODE)
                ),
                array(
                    'id'            => 'invoiced',
                    'report'        => __('Total Invoiced'),
                    'comment'       => __('Total Invoiced VS Paid Report'),
                    'updated_at'    => $this->_getUpdatedAt(Magento_Reports_Model_Flag::REPORT_INVOICE_FLAG_CODE)
                ),
                array(
                    'id'            => 'refunded',
                    'report'        => __('Total Refunded'),
                    'comment'       => __('Total Refunded Report'),
                    'updated_at'    => $this->_getUpdatedAt(Magento_Reports_Model_Flag::REPORT_REFUNDED_FLAG_CODE)
                ),
                array(
                    'id'            => 'coupons',
                    'report'        => __('Coupons'),
                    'comment'       => __('Promotion Coupons Usage Report'),
                    'updated_at'    => $this->_getUpdatedAt(Magento_Reports_Model_Flag::REPORT_COUPONS_FLAG_CODE)
                ),
                array(
                    'id'            => 'bestsellers',
                    'report'        => __('Bestsellers'),
                    'comment'       => __('Products Bestsellers Report'),
                    'updated_at'    => $this->_getUpdatedAt(Magento_Reports_Model_Flag::REPORT_BESTSELLERS_FLAG_CODE)
                ),
                array(
                    'id'            => 'viewed',
                    'report'        => __('Most Viewed'),
                    'comment'       => __('Most Viewed Products Report'),
                    'updated_at'    => $this->_getUpdatedAt(Magento_Reports_Model_Flag::REPORT_PRODUCT_VIEWED_FLAG_CODE)),
            );
            foreach ($data as $value) {
                $item = new Magento_Object();
                $item->setData($value);
                $this->addItem($item);
            }
        }
        return $this;
    }
}
