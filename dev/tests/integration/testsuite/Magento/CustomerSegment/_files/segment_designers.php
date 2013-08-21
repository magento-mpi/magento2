<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $segment Magento_CustomerSegment_Model_Segment */
$segment = Mage::getModel('Magento_CustomerSegment_Model_Segment');
$segment->loadPost(array(
    'name' => 'Designers',
    'is_active' => '1',
));
$segment->save();
