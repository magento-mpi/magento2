<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$installer->getConnection()->dropForeignKey(
    $installer->getTable('core_cache_tag'),
    $installer->getFkName('core_cache_tag', 'cache_id', 'core_cache', 'id')
);

$installer->endSetup();
