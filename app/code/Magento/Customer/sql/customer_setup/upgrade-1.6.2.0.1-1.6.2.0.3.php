<?php
/**
 * {license_notice}
 *
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
