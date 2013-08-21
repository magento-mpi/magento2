<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $cacheTypeList Magento_Core_Model_Cache_TypeListInterface */
$cacheTypeList = Mage::getModel('Magento_Core_Model_Cache_TypeListInterface');
$types = array_keys($cacheTypeList->getTypes());

/** @var $cacheState Magento_Core_Model_Cache_StateInterface */
$cacheState = Mage::getObjectManager()->get('Magento_Core_Model_Cache_StateInterface');
foreach ($types as $type) {
    $cacheState->setEnabled($type, true);
}
$cacheState->persist();
