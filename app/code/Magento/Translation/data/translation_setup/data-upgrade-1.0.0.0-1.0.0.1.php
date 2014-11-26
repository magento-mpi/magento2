<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer \Magento\Framework\Module\DataSetup */
$installer = $this;

$installer->startSetup();

$select = $installer->getConnection()
    ->select()
    ->from($installer->getTable('core_translate'))
    ->insertFromSelect($installer->getTable('translation'));

$installer->getConnection()->query($select);

$installer->getConnection()->dropTable($installer->getTable('core_translate'));

$installer->endSetup();
