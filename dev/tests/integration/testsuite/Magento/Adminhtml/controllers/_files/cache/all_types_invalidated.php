<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $cacheTypeList \Magento\Core\Model\Cache\TypeListInterface */
$cacheTypeList = \Mage::getModel('Magento\Core\Model\Cache\TypeListInterface');
$cacheTypeList->invalidate(array_keys($cacheTypeList->getTypes()));
