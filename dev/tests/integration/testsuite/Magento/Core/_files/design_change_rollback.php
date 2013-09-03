<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $cache Magento_Core_Model_Cache */
$cache = Mage::getModel('Magento_Core_Model_Cache');
$cache->clean(array(Magento_Core_Model_Design::CACHE_TAG));
