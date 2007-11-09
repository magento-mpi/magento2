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
 * Reports orders collection
 *
 * @category   Mage
 * @package    Mage_Reports
 * @author     Ivan Chepurnyi  <mitch@varien.com>
 */

class Mage_Reports_Model_Mysql4_Order_Collection extends Mage_Sales_Model_Entity_Order_Collection
{
    public function prepareSummary($range, $customStart, $customEnd, $storeId=0)
    {

        if ($storeId==0) {
            $this->addExpressionAttributeToSelect('revenue', 'SUM({{grand_total}}*{{store_to_base_rate}})', array('grand_total','store_to_base_rate'));
        } else{
            $this->addExpressionAttributeToSelect('revenue', 'SUM({{grand_total}})', 'grand_total');
        }

        $this->addExpressionAttributeToSelect('amouth', 'COUNT({{attribute}})', 'entity_id')
            ->addExpressionAttributeToSelect('range', $this->_getRangeExpression($range), 'created_at')
            ->addAttributeToFilter('created_at', $this->_getDateRange($range, $customStart, $customEnd))
            ->groupByAttribute('range')
            ->getSelect()->order('range', 'asc');

        return $this;
    }

    protected function _getRangeExpression($range)
    {
        switch ($range)
        {
        	case '24h':
        		$expression = 'DATE_FORMAT({{attribute}}, \'%Y-%m-%d %H:00\')';
        		break;

        	case '7d':
        	case '1m':
        	   $expression = 'DATE_FORMAT({{attribute}}, \'%Y-%m-%d 00:00\')';
        	   break;


        	case '1y':
        	case 'custom':
        	default:
        	    $expression = 'DATE_FORMAT({{attribute}}, \'%Y-%m-01 00:00\')';
        		break;
        }

        return $expression;
    }

    protected function _getDateRange($range, $customStart, $customEnd)
    {
        $dateEnd = new Zend_Date();
        $dateStart = clone $dateEnd;
        switch ($range)
        {
        	case '24h':
        		$dateStart->subHour(24);
        		break;

            case '7d':
        		$dateStart->subDay(7);
        		break;

            case '1m':
                $dateStart->subMonth(1);
        		break;

            case 'custom':
                $dateStart = $customStart;
                $dateEnd   = $customEnd;
                break;

            case '1y':
        	default:
        	    $dateStart->subYear(1);
        		break;
        }

        return array('from'=>$dateStart, 'to'=>$dateEnd, 'datetime'=>true);
    }
}// Class Mage_Reports_Model_Mysql4_Order_Collection END