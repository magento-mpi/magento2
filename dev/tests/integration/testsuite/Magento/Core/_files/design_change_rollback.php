<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $cache \Magento\Framework\App\Cache */
$cache = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Framework\App\Cache');
$cache->clean([\Magento\Core\Model\Design::CACHE_TAG]);
