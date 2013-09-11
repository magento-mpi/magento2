<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var \Magento\Core\Model\Cache\Type\Layout $layoutCache */
$layoutCache = Mage::getSingleton('Magento\Core\Model\Cache\Type\Layout');
$layoutCache->save('fixture layout cache data', 'LAYOUT_CACHE_FIXTURE');
