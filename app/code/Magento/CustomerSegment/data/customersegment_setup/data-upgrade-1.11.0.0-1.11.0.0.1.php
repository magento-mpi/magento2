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
$collection = Mage::getResourceModel('Magento_CustomerSegment_Model_Resource_Segment_Collection');
foreach($collection as $segment) {
    $segment->afterLoad();
    $segment->save();
}
