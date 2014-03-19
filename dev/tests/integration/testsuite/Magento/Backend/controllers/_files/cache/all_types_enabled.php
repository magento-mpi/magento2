<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $cacheTypeList \Magento\App\Cache\TypeListInterface */
$cacheTypeList = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
    'Magento\App\Cache\TypeListInterface'
);
$types = array_keys($cacheTypeList->getTypes());

/** @var $cacheState \Magento\App\Cache\StateInterface */
$cacheState = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\App\Cache\StateInterface');
foreach ($types as $type) {
    $cacheState->setEnabled($type, true);
}
$cacheState->persist();
