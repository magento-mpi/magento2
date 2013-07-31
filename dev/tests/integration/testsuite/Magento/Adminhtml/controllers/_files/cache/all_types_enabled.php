<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $cache Mage_Core_Model_Cache */
$cache = Mage::getModel('Mage_Core_Model_Cache');
$types = array_keys($cache->getTypes());

/** @var $cacheTypes Mage_Core_Model_Cache_Types */
$cacheTypes = Mage::getObjectManager()->get('Mage_Core_Model_Cache_Types');
foreach ($types as $type) {
    $cacheTypes->setEnabled($type, true);
}
$cacheTypes->persist();
