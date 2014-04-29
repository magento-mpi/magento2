<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $cacheTypeList \Magento\Framework\App\Cache\TypeListInterface */
$cacheTypeList = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
    'Magento\Framework\App\Cache\TypeListInterface'
);
$cacheTypeList->invalidate(array_keys($cacheTypeList->getTypes()));
