<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $cache \Magento\App\Cache */
$cache = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\App\Cache');
$cache->clean();
