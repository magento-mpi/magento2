<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Resave all segments for segment conditions regeneration
 */
/** @var $this \Magento\CustomerSegment\Model\Resource\Setup */
$collection = $this->createSegmentCollection();
/** @var $segment \Magento\CustomerSegment\Model\Segment */
foreach ($collection as $segment) {
    $segment->afterLoad();
    $segment->save();
}
