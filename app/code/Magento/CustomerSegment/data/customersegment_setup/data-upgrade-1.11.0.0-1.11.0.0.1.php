<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerSegment
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Resave all segments for segment conditions regeneration
 */
$collection = \Mage::getResourceModel('\Magento\CustomerSegment\Model\Resource\Segment\Collection');
foreach($collection as $segment) {
    $segment->afterLoad();
    $segment->save();
}
