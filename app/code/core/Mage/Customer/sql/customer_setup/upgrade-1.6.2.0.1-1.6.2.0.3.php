<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Mage_Customer_Model_Resource_Setup */
$installer = $this;

$entities = $installer->getDefaultEntities();
foreach ($entities as $entityName => $entity) {
    $installer->addEntityType($entityName, $entity);
}
