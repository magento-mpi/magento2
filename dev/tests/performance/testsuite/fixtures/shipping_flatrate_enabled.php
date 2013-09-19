<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var \Magento\Core\Model\Config\Value $configData */
$configData = \Mage::getModel('Magento\Core\Model\Config\Value');
$configData->setPath('carriers/flatrate/active')
    ->setScope(\Magento\Core\Model\Config::SCOPE_DEFAULT)
    ->setScopeId(0)
    ->setValue(1)
    ->save();
 \Mage::app()->cleanCache(array(\Magento\Core\Model\Config::CACHE_TAG));
