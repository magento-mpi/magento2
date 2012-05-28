<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_CustomerSegment
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

$data = array(
    'name' => 'Customer Segment 1',
    'is_active' => '1',
);
$segment = new Enterprise_CustomerSegment_Model_Segment;
$segment->loadPost($data);
$segment->save();

Mage::register('_fixture/Enterprise_CustomerSegment_Model_Segment', $segment);
