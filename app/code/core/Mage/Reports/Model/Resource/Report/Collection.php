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
class Mage_Reports_Model_Resource_Report_Collection extends Varien_Data_Collection
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
     * @param int $from
     * @param int $to
     * @return Mage_Reports_Model_Resource_Report_Collection
     */
    public function setInterval($from, $to)
    {
        $this->_from = $from;
        $this->_to   = $to;

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
            $dateStart  = new Zend_Date($this->_from);
            $dateEnd    = new Zend_Date($this->_to);


            $t = array();
            $firstInterval = true;
            while ($dateStart->compare($dateEnd) <= 0) {

                switch ($this->_period) {
                    case 'day':
                        $t['period'] = $dateStart->toString(Mage::app()->getLocale()->getDateFormat());
                        $t['start'] = $dateStart->toString('yyyy-MM-dd HH:mm:ss');
                        $t['end'] = $dateStart->toString('yyyy-MM-dd 23:59:59');
                        $dateStart->addDay(1);
                        break;
                    case 'month':
                        $t['period'] =  $dateStart->toString('MM/yyyy');
                        $t['start'] = ($firstInterval) ? $dateStart->toString('yyyy-MM-dd 00:00:00')
                            : $dateStart->toString('yyyy-MM-01 00:00:00');

                        $lastInterval = ($dateStart->compareMonth($dateEnd->getMonth()) == 0);

                        $t['end'] = ($lastInterval) ? $dateStart->setDay($dateEnd->getDay())
                            ->toString('yyyy-MM-dd 23:59:59')
                            : $dateStart->toString('yyyy-MM-'.date('t', $dateStart->getTimestamp()).' 23:59:59');

                        $dateStart->addMonth(1);

                        if ($dateStart->compareMonth($dateEnd->getMonth()) == 0) {
                            $dateStart->setDay(1);
                        }

                        $firstInterval = false;
                        break;
                    case 'year':
                        $t['period'] =  $dateStart->toString('yyyy');
                        $t['start'] = ($firstInterval) ? $dateStart->toString('yyyy-MM-dd 00:00:00')
                            : $dateStart->toString('yyyy-01-01 00:00:00');

                        $lastInterval = ($dateStart->compareYear($dateEnd->getYear()) == 0);

                        $t['end'] = ($lastInterval) ? $dateStart->setMonth($dateEnd->getMonth())
                            ->setDay($dateEnd->getDay())->toString('yyyy-MM-dd 23:59:59')
                            : $dateStart->toString('yyyy-12-31 23:59:59');
                        $dateStart->addYear(1);

                        if ($dateStart->compareYear($dateEnd->getYear()) == 0) {
                            $dateStart->setMonth(1)->setDay(1);
                        }

                        $firstInterval = false;
                        break;
                }
                $this->_intervals[$t['period']] = new Varien_Object($t);
            }
        }
        return  $this->_intervals;
    }

    /**
     * Return date periods
     *
     * @return array
     */
    public function getPeriods()
    {
        return array(
            'day'   => Mage::helper('Mage_Reports_Helper_Data')->__('Day'),
            'month' => Mage::helper('Mage_Reports_Helper_Data')->__('Month'),
            'year'  => Mage::helper('Mage_Reports_Helper_Data')->__('Year')
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
     * @return arrays
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
     * get report full
     *
     * @param int $from
     * @param int $to
     * @return unknown
     */
    public function getReportFull($from, $to)
    {
        return $this->_model->getReportFull($this->timeShift($from), $this->timeShift($to));
    }

    /**
     * Get report for some interval
     *
     * @param int $from
     * @param int $to
     * @return Mage_Core_Model_Resource_Db_Collection_Abstract
     */
    protected function _getReport($from, $to)
    {
        if ($this->_reportCollectionClass === null) {
            return array();
        }
        $collectionClass = $this->_reportCollectionClass;
        $reportResource = new $collectionClass();
        $reportResource
            ->setDateRange($this->timeShift($from), $this->timeShift($to))
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
        return Mage::app()->getLocale()
            ->utcDate(null, $datetime, true, Varien_Date::DATETIME_INTERNAL_FORMAT)
            ->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
    }

    /**
     * Load data
     *
     * @param bool $printQuery
     * @param bool $logQuery
     * @return Mage_Reports_Model_Resource_Report_Collection|Varien_Data_Collection
     */
    public function loadData($printQuery = false, $logQuery = false)
    {
        $this->_items = $this->getReports();
        return $this;
    }
}
