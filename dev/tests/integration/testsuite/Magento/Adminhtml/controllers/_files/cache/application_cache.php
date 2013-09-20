<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $cache \Magento\Core\Model\Cache */
$cache = \Mage::getModel('Magento\Core\Model\Cache');
$cache->save('application data', 'APPLICATION_FIXTURE');
