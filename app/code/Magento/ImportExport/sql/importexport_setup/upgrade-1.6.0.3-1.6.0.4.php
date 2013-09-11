<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer \Magento\ImportExport\Model\Resource\Setup */
$installer = $this;

$installer->getConnection()
    ->dropColumn($installer->getTable('importexport_importdata'), 'entity_subtype');
