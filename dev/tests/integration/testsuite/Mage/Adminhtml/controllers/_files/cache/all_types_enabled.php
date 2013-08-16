<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $cacheTypeList Mage_Core_Model_Cache_TypeListInterface */
$cacheTypeList = Mage::getModel('Mage_Core_Model_Cache_TypeListInterface');
$types = array_keys($cacheTypeList->getTypes());

/** @var $cacheState Mage_Core_Model_Cache_StateInterface */
$cacheState = Mage::getObjectManager()->get('Mage_Core_Model_Cache_StateInterface');
foreach ($types as $type) {
    $cacheState->setEnabled($type, true);
}
$cacheState->persist();
