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
$collection = $this->createSegmentCollection();
foreach ($collection as $segment) {
    $segment->afterLoad();
    $segment->save();
}
