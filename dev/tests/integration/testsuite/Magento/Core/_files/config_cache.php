<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var \Magento\Framework\App\Cache\Type\Config $layoutCache */
$layoutCache = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->get('Magento\Framework\App\Cache\Type\Config');
$layoutCache->save('fixture config cache data', 'CONFIG_CACHE_FIXTURE');
