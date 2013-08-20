<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $cacheTypeList Mage_Core_Model_Cache_TypeListInterface */
$cacheTypeList = Mage::getModel('Mage_Core_Model_Cache_TypeListInterface');
$cacheTypeList->invalidate(array_keys($cacheTypeList->getTypes()));
