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
class Enterprise_CustomerSegment_Model_Processor extends Mage_Core_Model_Abstract
{
    protected $_segmentMap = array();

    protected function _construct()
    {
        $this->_init('enterprise_customersegment/processor');
    }

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

    public function process(Enterprise_CustomerSegment_Model_Segment $segment, $customer = null)
    {
        $result = $segment->validate($customer);
        $resultText = ($result ? '<span style="color: #00CC00;">PASSED</span>' : '<span style="color: #CC0000;">FAILED</span>');
        echo "SEGMENT #{$segment->getId()} VALIDATION AGAINST CUSTOMER #{$customer->getId()} {$resultText}\n<br /><br />\n";
        return $result;
    }
}
