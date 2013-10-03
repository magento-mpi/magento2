<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
return array(
    'cataloginventory_stock' => array(
        'name' => 'cataloginventory_stock',
        'instance' => 'Magento\CatalogInventory\Model\Indexer\Stock',
        'depends' => array()
    ),
    'catalog_product_attribute' => array(
        'name' => 'catalog_product_attribute',
        'depends' => array(
            'cataloginventory_stock'
        )
    ),
);

