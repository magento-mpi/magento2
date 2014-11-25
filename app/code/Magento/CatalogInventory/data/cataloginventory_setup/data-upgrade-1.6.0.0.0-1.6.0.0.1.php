<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $this \Magento\Eav\Model\Entity\Setup */

$this->getConnection()->insertForce(
    $this->getTable('cataloginventory_stock'),
    array(
        'stock_name' => 'Default',
        'website_id' => \Magento\CatalogInventory\Model\Configuration::DEFAULT_WEBSITE_ID
        // TODO iterate available websites
    )
);
