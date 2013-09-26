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
$collection = $this->createSegmentCollection();
foreach ($collection as $segment) {
    $segment->afterLoad();
    $segment->save();
}
