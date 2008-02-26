<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Reports
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Report Reviews collection
 *
 * @category   Mage
 * @package    Mage_Reports
 * @author     Dmytro Vasylenko  <dimav@varien.com>
 */

class Mage_Reports_Model_Mysql4_Report_Collection
{

    protected $_from;
    protected $_to;
    protected $_period;

    public $_modelArray = array();

    protected $_intervals;

    protected $_pageSize;

    protected function _construct()
    {

    }

    public function setPeriod($period)
    {
        $this->_period = $period;
    }

    public function setInterval($from, $to)
    {
        $this->_from = $from;
        $this->_to = $to;
    }

    public function getIntervals()
    {
        if (!$this->_intervals) {
            $dateStart = new Zend_Date($this->_from);
            $dateStart2 = new Zend_Date($this->_from);

            $dateEnd = new Zend_Date($this->_to);
            $this->_intervals = array();

            $t = array();
            while ($dateStart->compare($dateEnd)<=0) {

                switch ($this->_period) {
                    case 'day' :
                        $t['title'] = $dateStart->toString('MM/dd/yyyy');
                        $t['start'] = $dateStart->toString('yyyy-MM-dd 00:00:00');
                        $t['end'] = $dateStart->toString('yyyy-MM-dd 23:59:59');
                        $dateStart->addDay(1);
                        break;
                    case 'month':
                        $t['title'] =  $dateStart->toString('MM/yyyy');
                        $t['start'] = $dateStart->toString('yyyy-MM-01 00:00:00');
                        $t['end'] = $dateStart->toString('yyyy-MM-31 23:59:59');
                        $dateStart->addMonth(1);
                        break;
                    case 'year':
                        $t['title'] =  $dateStart->toString('yyyy');
                        $t['start'] = $dateStart->toString('yyyy-01-01 00:00:00');
                        $t['end'] = $dateStart->toString('yyyy-12-31 23:59:59');
                        $dateStart->addYear(1);
                        break;
                }
                $this->_intervals[] = $t;
            }

            if ($this->_period != 'day') {
                $this->_intervals[0]['start'] = $dateStart2->toString('yyyy-MM-dd 00:00:00');
                $this->_intervals[count($this->_intervals)-1]['end'] = $dateEnd->toString('yyyy-MM-dd 23:59:59');
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
            'day'=>Mage::helper('reports')->__('1 Day'),
            'month'=>Mage::helper('reports')->__('1 Month'),
		    'year'=>Mage::helper('reports')->__('1 Year')
        );
    }

    public function getSize()
    {
        return count($this->_modelArray);
    }

    public function setPageSize($size)
    {
        $this->_pageSize = $size;
        return $this;
    }

    public function getPageSize()
    {
        return $this->_pageSize;
    }

    public function initReport($modelClass)
    {
        $this->_modelArray = array();
        foreach ($this->getIntervals() as $key=>$interval) {
            $this->_modelArray[] = Mage::getModel('reports/report')
                ->setPageSize($this->getPageSize())
                ->setPeriodTitle($interval['title'])
                ->setStartDate($interval['start'])
                ->setEndDate($interval['end'])
                ->initCollection($modelClass);
        }
    }

    public function getModels()
    {
        return $this->_modelArray;
    }
}
