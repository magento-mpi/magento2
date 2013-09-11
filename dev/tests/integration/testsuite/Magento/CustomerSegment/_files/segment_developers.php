<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $segment \Magento\CustomerSegment\Model\Segment */
$segment = Mage::getModel('Magento\CustomerSegment\Model\Segment');
$segment->loadPost(array(
    'name' => 'Developers',
    'is_active' => '1',
));
$segment->save();
