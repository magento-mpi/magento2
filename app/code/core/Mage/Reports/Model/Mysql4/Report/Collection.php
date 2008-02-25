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
    protected $_modelArray = array();
    protected $_intervals;

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
            $dateEnd = new Zend_Date($this->_to);
            $this->_intervals = array();

            $addPeriod = "add".ucfirst($this->_period);

            while ($dateStart->compare($dateEnd)<=0) {
                $this->_intervals[] = $dateStart->toString(Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT ));
                $dateStart->$addPeriod(1);
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
            'day'=>$this->__('1 Day'),
            'month'=>$this->__('1 Month'),
		    'year'=>$this->__('1 Year')
        );
    }

    public function getSize()
    {
        return count($this->_modelArray);
    }
}
