<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Enterprise
 * @package     Enterprise_CustomerSegment
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */
class Enterprise_CustomerSegment_Model_Processor
{
    protected $_segmentMap = array();

    public function getActiveSegmentsForEvent($eventName, $websiteId)
    {
        if (!isset($this->_segmentMap[$eventName])) {
            $this->_segmentMap[$eventName] = Mage::getResourceModel('enterprise_customersegment/segment_collection')
                    ->addEventFilter($eventName)
                    ->addWebsiteFilter($websiteId)
                    ->addIsActiveFilter(1);
            $this->_segmentMap[$eventName]->walk('afterLoad');
        }

        return $this->_segmentMap[$eventName];
    }

    public function processEvent($eventName, $customer, $websiteId)
    {
        $segments = $this->getActiveSegmentsForEvent($eventName, $websiteId);

        foreach ($segments as $segment) {
            $this->process($segment, $customer);
        }
    }

    public function process(Enterprise_CustomerSegment_Model_Segment $segment, $customer = null, $website = null)
    {
        if (is_null($customer)) {
            if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
                return;
            }

            $customer = Mage::getSingleton('customer/session')->getCustomer();
        }

        if (is_null($website)) {
            $website = Mage::app()->getWebsite();
        }

        return $segment->validate($customer, $website);
    }
}
