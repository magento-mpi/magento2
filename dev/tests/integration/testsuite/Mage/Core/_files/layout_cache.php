<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var Mage_Core_Model_Cache_Type_Layout $layoutCache */
$layoutCache = Mage::getSingleton('Mage_Core_Model_Cache_Type_Layout');
$layoutCache->save('fixture layout cache data', 'LAYOUT_CACHE_FIXTURE');
