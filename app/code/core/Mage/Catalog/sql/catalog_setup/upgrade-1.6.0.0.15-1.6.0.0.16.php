<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

$installer = $this;
/** @var $installer Mage_Catalog_Model_Resource_Setup */

$installer->removeAttribute(Mage_Catalog_Model_Product::ENTITY, 'options_container');
