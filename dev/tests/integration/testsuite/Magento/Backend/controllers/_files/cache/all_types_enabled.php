<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/** @var $cacheTypeList \Magento\Framework\App\Cache\TypeListInterface */
$cacheTypeList = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
    'Magento\Framework\App\Cache\TypeListInterface'
);
$types = array_keys($cacheTypeList->getTypes());

/** @var $cacheState \Magento\Framework\App\Cache\StateInterface */
$cacheState = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->get('Magento\Framework\App\Cache\StateInterface');
foreach ($types as $type) {
    $cacheState->setEnabled($type, true);
}
$cacheState->persist();
