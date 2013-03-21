<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $cache Mage_Core_Model_Cache */
$cache = Mage::getModel('Mage_Core_Model_Cache');
$cache->save('application data', 'APPLICATION_FIXTURE');
