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
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Data helper for dashboard
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */

class Mage_Adminhtml_Helper_Dashboard_Data extends Mage_Core_Helper_Data
{
    protected $_locale = null;
    protected $_stores = null;

    public function getStores()
    {
        if(!$this->_stores) {
            $this->_stores = Mage::app()->getStore()->getResourceCollection()->load();
        }

        return $this->_stores;
    }

    public function countStores()
    {
        return sizeof($this->_stores->getItems());
    }

    public function getDatePeriods()
    {
        return array(
            '24h'=>$this->__('Last 24 hours'),
            '7d'=>$this->__('Last 7 days'),
		    '1m'=>$this->__('Last Month'),
		    '1y'=>$this->__('Last Year'),
		    '2y'=>$this->__('2 Last Years')
        );
    }
}