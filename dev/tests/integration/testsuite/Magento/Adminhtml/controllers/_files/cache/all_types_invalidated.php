<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $cacheTypeList Magento_Core_Model_Cache_TypeListInterface */
$cacheTypeList = Mage::getModel('Magento_Core_Model_Cache_TypeListInterface');
$cacheTypeList->invalidate(array_keys($cacheTypeList->getTypes()));
