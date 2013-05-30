<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var Mage_Core_Model_Cache_Type_Config $layoutCache */
$layoutCache = Mage::getSingleton('Mage_Core_Model_Cache_Type_Config');
$layoutCache->save('fixture config cache data', 'CONFIG_CACHE_FIXTURE');
