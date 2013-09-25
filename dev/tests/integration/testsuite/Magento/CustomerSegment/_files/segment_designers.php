<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $segment Magento_CustomerSegment_Model_Segment */
$segment = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
    ->create('Magento_CustomerSegment_Model_Segment');
$segment->loadPost(array(
    'name' => 'Designers',
    'is_active' => '1',
));
$segment->save();
