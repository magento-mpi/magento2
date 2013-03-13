<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_CustomerSegment
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Resave all segments for segment conditions regeneration
 */
$collection = Mage::getResourceModel('Enterprise_CustomerSegment_Model_Resource_Segment_Collection');
foreach($collection as $segment) {
    $segment->afterLoad();
    $segment->save();
}
