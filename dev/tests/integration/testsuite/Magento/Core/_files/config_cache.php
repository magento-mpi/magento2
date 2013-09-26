<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var Magento_Core_Model_Cache_Type_Config $layoutCache */
$layoutCache = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_Cache_Type_Config');
$layoutCache->save('fixture config cache data', 'CONFIG_CACHE_FIXTURE');
