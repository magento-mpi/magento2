<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var \Magento\Framework\App\Cache\Type\Layout $layoutCache */
$layoutCache = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Framework\App\Cache\Type\Layout');
$layoutCache->save('fixture layout cache data', 'LAYOUT_CACHE_FIXTURE');
