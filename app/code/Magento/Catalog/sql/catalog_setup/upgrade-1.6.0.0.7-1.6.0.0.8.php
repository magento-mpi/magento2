<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer \Magento\Catalog\Model\Resource\Setup */
$installer  = $this;
$connection = $installer->getConnection();

$connection->addIndex(
    $installer->getTable('catalog_category_entity'),
    $installer->getIdxName(
        'catalog_category_entity',
        array('path', 'entity_id')
    ),
    array('path', 'entity_id')
);
