<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Reports
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Report Reviews collection
 *
 * @category    Mage
 * @package     Mage_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Reports_Model_Resource_Report_Collection extends Magento_Data_Collection
{
    /**
     * From value
     *
     * @var string
     */
    protected $_from;

    /**
     * To value
     *
     * @var string
     */
    protected $_to;

    /**
     * Report period
     *
     * @var int
     */
    protected $_period;

    /**
     * Intervals
     *
     * @var int
     */
    protected $_intervals;

    /**
     * Intervals
     *
     * @var int
     */
    protected $_reports;

    /**
     * Page size
     *
     * @var int
     */
    protected $_pageSize;

    /**
     * Array of store ids
     *
     * @var array
     */
    protected $_storeIds;

    /**
     * Set the resource report collection class
     *
     * @var string
     */
    protected $_reportCollection = null;

    /**
     * @var  Zend_DateFactory
     */
    protected $_dateFactory;

    /**
     * @var Mage_Core_Model_LocaleInterface
     */
    protected $_locale;

    /**
     * @var Mage_Reports_Helper_Data
     */
    protected $_helper;

    /**
     * @var Mage_Reports_Model_Resource_Report_Collection_Factory
     */
    protected $_collectionFactory;

    /**
     * @param Mage_Core_Model_LocaleInterface $locale
     * @param Mage_Reports_Helper_Data $helper
     * @param Zend_DateFactory $dateFactory
     * @param Mage_Reports_Model_Resource_Report_Collection_Factory $collectionFactory
     */
    public function __construct(
        Mage_Core_Model_LocaleInterface $locale,
        Mage_Reports_Helper_Data $helper,
        Zend_DateFactory $dateFactory,
        Mage_Reports_Model_Resource_Report_Collection_Factory $collectionFactory
    ) {
        $this->_dateFactory = $dateFactory;
        $this->_locale = $locale;
        $this->_helper = $helper;
        $this->_collectionFactory = $collectionFactory;
        parent::__construct();
    }

    /**
     * Set period
     *
     * @param int $period
     * @return Mage_Reports_Model_Resource_Report_Collection
     */
    public function setPeriod($period)
    {
        $this->_period = $period;
        return $this;
    }

    /**
     * Set interval
     *
     * @param int $fromDate
     * @param int $toDate
     * @return Mage_Reports_Model_Resource_Report_Collection
     */
    public function setInterval($fromDate, $toDate)
    {
        $this->_from = $fromDate;
        $this->_to   = $toDate;

        return $this;
    }

    /**
     * Get intervals
     *
     * @return array
     */
    protected function _getIntervals()
    {
        if (!$this->_intervals) {
            $this->_intervals = array();
            if (!$this->_from && !$this->_to) {
                return $this->_intervals;
            }
            $dateStart  =  $this->_dateFactory->create(array('date' => $this->_from));
            $dateEnd    =  $this->_dateFactory->create(array('date' => $this->_to));

            $interval = array();
            $firstInterval = true;
            while ($dateStart->compare($dateEnd) <= 0) {
                switch ($this->_period) {
                    case 'day':
                        $interval = $this->_getDayInterval($dateStart);
                        $dateStart->addDay(1);
                        break;
                    case 'month':
                        $interval = $this->_getMonthInterval($dateStart, $dateEnd, $firstInterval);
                        $firstInterval = false;
                        break;
                    case 'year':
                        $interval = $this->_getYearInterval($dateStart, $dateEnd, $firstInterval);
                        $firstInterval = false;
                        break;
                    default:
                        break(2);
                }
                $this->_intervals[$interval['period']] = new Magento_Object($interval);
            }
        }
        return  $this->_intervals;
    }

    /**
     * Get interval for a day
     *
     * @param Zend_Date $dateStart
     * @return array
     */
    protected function _getDayInterval(Zend_Date $dateStart)
    {
        $interval = array(
            'period' => $dateStart->toString($this->_locale->getDateFormat()),
            'start'  => $dateStart->toString('yyyy-MM-dd HH:mm:ss'),
            'end'    => $dateStart->toString('yyyy-MM-dd 23:59:59')
        );
        return $interval;
    }

    /**
     * Get interval for a month
     *
     * @param Zend_Date $dateStart
     * @param Zend_Date $dateEnd
     * @param bool $firstInterval
     * @return array
     */
    protected function _getMonthInterval(Zend_Date $dateStart, Zend_Date $dateEnd, $firstInterval)
    {
        $interval = array();
        $interval['period'] =  $dateStart->toString('MM/yyyy');
        if ($firstInterval) {
            $interval['start'] = $dateStart->toString('yyyy-MM-dd 00:00:00');
        } else {
            $interval['start'] = $dateStart->toString('yyyy-MM-01 00:00:00');
        }

        $lastInterval = ($dateStart->compareMonth($dateEnd->getMonth()) == 0);

        if ($lastInterval) {
            $interval['end'] = $dateStart->setDay($dateEnd->getDay())->toString('yyyy-MM-dd 23:59:59');
        } else {
            $interval['end'] = $dateStart->toString('yyyy-MM-' . date('t', $dateStart->getTimestamp()) . ' 23:59:59');
        }

        $dateStart->addMonth(1);

        if ($dateStart->compareMonth($dateEnd->getMonth()) == 0) {
            $dateStart->setDay(1);
        }

        return $interval;
    }

    /**
     * Get Interval for a year
     *
     * @param Zend_Date $dateStart
     * @param Zend_Date $dateEnd
     * @param bool $firstInterval
     * @return array
     */
    protected function _getYearInterval(Zend_Date $dateStart, Zend_Date $dateEnd, $firstInterval)
    {
        $interval = array();
        $interval['period'] =  $dateStart->toString('yyyy');
        $interval['start'] = ($firstInterval) ? $dateStart->toString('yyyy-MM-dd 00:00:00')
            : $dateStart->toString('yyyy-01-01 00:00:00');

        $lastInterval = ($dateStart->compareYear($dateEnd->getYear()) == 0);

        $interval['end'] = ($lastInterval) ? $dateStart->setMonth($dateEnd->getMonth())
            ->setDay($dateEnd->getDay())->toString('yyyy-MM-dd 23:59:59')
            : $dateStart->toString('yyyy-12-31 23:59:59');
        $dateStart->addYear(1);

        if ($dateStart->compareYear($dateEnd->getYear()) == 0) {
            $dateStart->setMonth(1)->setDay(1);
        }

        return $interval;
    }

    /**
     * Return date periods
     *
     * @return array
     */
    public function getPeriods()
    {
        return array(
            'day'   => $this->_helper->__('Day'),
            'month' => $this->_helper->__('Month'),
            'year'  => $this->_helper->__('Year')
        );
    }

    /**
     * Set store ids
     *
     * @param array $storeIds
     * @return Mage_Reports_Model_Resource_Report_Collection
     */
    public function setStoreIds($storeIds)
    {
        $this->_storeIds = $storeIds;
        return $this;
    }

    /**
     * Get store ids
     *
     * @return array
     */
    public function getStoreIds()
    {
        return $this->_storeIds;
    }

    /**
     * Get size
     *
     * @return int
     */
    public function getSize()
    {
        return count($this->_getIntervals());
    }

    /**
     * Set page size
     *
     * @param int $size
     * @return Mage_Reports_Model_Resource_Report_Collection
     */
    public function setPageSize($size)
    {
        $this->_pageSize = $size;
        return $this;
    }

    /**
     * Get page size
     *
     * @return int
     */
    public function getPageSize()
    {
        return $this->_pageSize;
    }

    /**
     * Get report for some interval
     *
     * @param int $fromDate
     * @param int $toDate
     * @return Mage_Core_Model_Resource_Db_Collection_Abstract
     */
    protected function _getReport($fromDate, $toDate)
    {
        if ($this->_reportCollection === null) {
            return array();
        }
        $reportResource = $this->_collectionFactory->create($this->_reportCollection);
        $reportResource
            ->setDateRange($this->timeShift($fromDate), $this->timeShift($toDate))
            ->setStoreIds($this->getStoreIds());
        return $reportResource;

    }

    /**
     * Get Reports based on intervals
     *
     * @return array
     */
    public function getReports()
    {
        if (!$this->_reports) {
            $reports = array();
            foreach ($this->_getIntervals() as $interval) {
                $interval->setChildren(
                    $this->_getReport($interval->getStart(), $interval->getEnd())
                );
                if (count($interval->getChildren()) == 0) {
                    $interval->setIsEmpty(true);
                }
                $reports[] = $interval;
            }
            $this->_reports = $reports;
        }
        return $this->_reports;
    }

    /**
     * Retrieve time shift
     *
     * @param string $datetime
     * @return string
     */
    public function timeShift($datetime)
    {
        return $this->_locale
            ->utcDate(null, $datetime, true, Magento_Date::DATETIME_INTERNAL_FORMAT)
            ->toString(Magento_Date::DATETIME_INTERNAL_FORMAT);
    }

    /**
     * Load data
     *
     * @param bool $printQuery
     * @param bool $logQuery
     * @return Mage_Reports_Model_Resource_Report_Collection|Magento_Data_Collection
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function loadData($printQuery = false, $logQuery = false)
    {
        $this->_items = $this->getReports();
        return $this;
    }
}
