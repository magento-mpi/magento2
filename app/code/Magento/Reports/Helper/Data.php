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
 * Reports data helper
 */
namespace Magento\Reports\Helper;

class Data extends \Magento\Core\Helper\AbstractHelper
{
    const REPORT_PERIOD_TYPE_DAY    = 'day';
    const REPORT_PERIOD_TYPE_MONTH  = 'month';
    const REPORT_PERIOD_TYPE_YEAR   = 'year';

    /**
     * @var \Magento\Adminhtml\Model\Report\ItemFactory
     */
    protected $_itemFactory;

    /**
     * @param \Magento\Core\Helper\Context $context
     * @param \Magento\Adminhtml\Model\Report\ItemFactory $itemFactory
     */
    public function __construct(
        \Magento\Core\Helper\Context $context,
        \Magento\Adminhtml\Model\Report\ItemFactory $itemFactory
    ) {
        parent::__construct($context);
        $this->_itemFactory = $itemFactory;
    }

    /**
     * Retrieve array of intervals
     *
     * @param string $from
     * @param string $to
     * @param string $period
     * @return array
     */
    public function getIntervals($from, $to, $period = self::REPORT_PERIOD_TYPE_DAY)
    {
        $intervals = array();
        if (!$from && !$to){
            return $intervals;
        }

        $start = new \Zend_Date($from, \Magento\Date::DATE_INTERNAL_FORMAT);

        if ($period == self::REPORT_PERIOD_TYPE_DAY) {
            $dateStart = $start;
        }

        if ($period == self::REPORT_PERIOD_TYPE_MONTH) {
            $dateStart = new \Zend_Date(date("Y-m", $start->getTimestamp()), \Magento\Date::DATE_INTERNAL_FORMAT);
        }

        if ($period == self::REPORT_PERIOD_TYPE_YEAR) {
            $dateStart = new \Zend_Date(date("Y", $start->getTimestamp()), \Magento\Date::DATE_INTERNAL_FORMAT);
        }

        $dateEnd = new \Zend_Date($to, \Magento\Date::DATE_INTERNAL_FORMAT);

        while ($dateStart->compare($dateEnd) <= 0) {
            switch ($period) {
                case self::REPORT_PERIOD_TYPE_DAY :
                    $t = $dateStart->toString('yyyy-MM-dd');
                    $dateStart->addDay(1);
                    break;
                case self::REPORT_PERIOD_TYPE_MONTH:
                    $t = $dateStart->toString('yyyy-MM');
                    $dateStart->addMonth(1);
                    break;
                case self::REPORT_PERIOD_TYPE_YEAR:
                    $t = $dateStart->toString('yyyy');
                    $dateStart->addYear(1);
                    break;
            }
            $intervals[] = $t;
        }
        return  $intervals;
    }

    public function prepareIntervalsCollection($collection, $from, $to, $periodType = self::REPORT_PERIOD_TYPE_DAY)
    {
        $intervals = $this->getIntervals($from, $to, $periodType);

        foreach ($intervals as $interval) {
            $item = $this->_itemFactory->create();
            $item->setPeriod($interval);
            $item->setIsEmpty();
            $collection->addItem($item);
        }
    }
}
