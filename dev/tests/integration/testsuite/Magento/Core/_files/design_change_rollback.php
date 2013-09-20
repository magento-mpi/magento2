<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $cache \Magento\Core\Model\Cache */
$cache = \Mage::getModel('Magento\Core\Model\Cache');
$cache->clean(array(\Magento\Core\Model\Design::CACHE_TAG));
