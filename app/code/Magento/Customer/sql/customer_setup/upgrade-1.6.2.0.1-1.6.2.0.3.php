<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer \Magento\Customer\Model\Resource\Setup */
$installer = $this;

$installer->cleanCache();

$entities = $installer->getDefaultEntities();
foreach ($entities as $entityName => $entity) {
    $installer->addEntityType($entityName, $entity);
}
