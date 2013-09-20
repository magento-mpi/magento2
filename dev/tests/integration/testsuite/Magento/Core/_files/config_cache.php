<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var \Magento\Core\Model\Cache\Type\Config $layoutCache */
$layoutCache = \Mage::getSingleton('Magento\Core\Model\Cache\Type\Config');
$layoutCache->save('fixture config cache data', 'CONFIG_CACHE_FIXTURE');
