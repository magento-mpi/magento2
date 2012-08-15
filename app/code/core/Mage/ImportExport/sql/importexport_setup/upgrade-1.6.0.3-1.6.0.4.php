<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_ImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Mage_ImportExport_Model_Resource_Setup */
$installer = $this;

$installer->getConnection()
    ->dropColumn($installer->getTable('importexport_importdata'), 'entity_subtype');
