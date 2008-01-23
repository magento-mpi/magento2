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
 * @package    Mage_Log
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_Log_Model_Aggregation extends Mage_Core_Model_Abstract
{
	protected function _construct()
	{
		$this->_init('log/aggregation');
	}

    public function run()
    {
        $this->_lastRecord = $this->getLastRecordDate();
        $periods = $this->getPeriodList();
        $stores = Mage::getResourceModel('core/store_collection');
        $last = false;

        foreach ($periods as $period) {
            //foreach ($stores as $store) {
            //    $this->_process($store->getId(), $period);
            //}
            $last = $this->_process(0, $period);
        }

        if ($last)
            $this->_removeEmpty($last);
    }

    private function _removeEmpty($last)
    {
        return $this->_getResource()->removeEmpty($last);
    }

    private function _process($store, $period)
    {
        $date = $this->_lastRecord;
        $upTo = time();

        while(strtotime($date) < $upTo){
            $to = strtotime("{$date} + {$period['period']} {$period['period_type']}");
            if ($to > time())
                break;

            $to = date('Y-m-d H:i:s', $to);
            $counts = $this->_getCounts($date, $to, $store);
            $date = $to;
            $data = array(
                'type_id'=>$period['type_id'],
                'store_id'=>$store,
                'visitor_count'=>$counts['visitors'],
                'customer_count'=>$counts['customers'],
                'add_date'=>$date
                );
            $this->_save($data, $date, $to);
        }
        return $date;
    }

    private function _save($data, $from, $to)
    {
        if ($logId = $this->_getResource()->getLogId($from, $to)) {
            $this->_update($logId, $data);
        } else {
            $this->_insert($data);
        }
    }

    private function _update($id, $data)
    {
        return $this->_getResource()->saveLog($data, $id);
    }

    private function _insert($data)
    {
        return $this->_getResource()->saveLog($data);
    }

    private function _getCounts($from, $to, $store)
    {
        return $this->_getResource()->getCounts($from, $to, $store);
    }

    public function getLastRecordDate()
    {
        $result = $this->_getResource()->getLastRecordDate();
        if (!$result)
            $result = date('Y-m-d H:i:s', strtotime('now - 2 months'));

        return $result;
    }

    public function getPeriodList()
    {
        return $this->_getResource()->getPeriodList();
    }

    private function date($in){
        $out = $in;
        // convert to gmt
        // if is num - date
        // round to hour:00:00
        return $out;
    }
}