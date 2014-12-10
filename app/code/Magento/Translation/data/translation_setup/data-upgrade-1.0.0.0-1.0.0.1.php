<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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
