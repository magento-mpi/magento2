<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $cache Magento_Core_Model_Cache */
$cache = Mage::getModel('Magento_Core_Model_Cache');
$types = array_keys($cache->getTypes());

/** @var $cacheTypes Magento_Core_Model_Cache_Types */
$cacheTypes = Mage::getObjectManager()->get('Magento_Core_Model_Cache_Types');
foreach ($types as $type) {
    $cacheTypes->setEnabled($type, false);
}
$cacheTypes->persist();
