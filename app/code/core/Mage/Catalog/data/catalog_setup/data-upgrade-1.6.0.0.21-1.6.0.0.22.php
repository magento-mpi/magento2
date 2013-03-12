<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $this Mage_Catalog_Model_Resource_Setup */

$entityTypeId = $this->getEntityTypeId(Mage_Catalog_Model_Product::ENTITY);
$this->removeAttributeSet($entityTypeId, 'Minimal');
