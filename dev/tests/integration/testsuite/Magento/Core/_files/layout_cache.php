<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var Magento_Core_Model_Cache_Type_Layout $layoutCache */
$layoutCache = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_Cache_Type_Layout');
$layoutCache->save('fixture layout cache data', 'LAYOUT_CACHE_FIXTURE');
