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
/** @var $segment \Magento\CustomerSegment\Model\Segment */
$segment = \Mage::getModel('Magento\CustomerSegment\Model\Segment');
$segment->loadPost($data);
$segment->save();
