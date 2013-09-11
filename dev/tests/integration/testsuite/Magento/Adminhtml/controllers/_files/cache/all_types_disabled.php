<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $cacheTypeList \Magento\Core\Model\Cache\TypeListInterface */
$cacheTypeList = Mage::getModel('\Magento\Core\Model\Cache\TypeListInterface');
$types = array_keys($cacheTypeList->getTypes());

/** @var $cacheState \Magento\Core\Model\Cache\StateInterface */
$cacheState = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
    ->get('Magento\Core\Model\Cache\StateInterface');
foreach ($types as $type) {
    $cacheState->setEnabled($type, false);
}
$cacheState->persist();
