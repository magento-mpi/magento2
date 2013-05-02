<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

require __DIR__ . '/segment_designers.php';
require __DIR__ . '/../../../Mage/Customer/_files/customer.php';

/** @var $segment Enterprise_CustomerSegment_Model_Segment */
$segment = Mage::getModel('Enterprise_CustomerSegment_Model_Segment');
$segment->load('Designers', 'name');

/** @var Enterprise_CustomerSegment_Model_Customer $segmentCustomer */
$segmentCustomer = Mage::getSingleton('Enterprise_CustomerSegment_Model_Customer');
$segmentCustomer->addCustomerToWebsiteSegments(1, Mage::app()->getWebsite()->getId(), array($segment->getId()));
