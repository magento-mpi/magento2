<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $cacheTypeList Magento_Core_Model_Cache_TypeListInterface */
$cacheTypeList = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Core_Model_Cache_TypeListInterface');
$cacheTypeList->invalidate(array_keys($cacheTypeList->getTypes()));
