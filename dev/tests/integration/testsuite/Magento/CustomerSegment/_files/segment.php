<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerSegment
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

$data = array(
    'name'        => 'Customer Segment 1',
    'website_ids' => array(1),
    'is_active'   => '1',
);
/** @var $segment Magento_CustomerSegment_Model_Segment */
$segment = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
    ->create('Magento_CustomerSegment_Model_Segment');
$segment->loadPost($data);
$segment->save();
