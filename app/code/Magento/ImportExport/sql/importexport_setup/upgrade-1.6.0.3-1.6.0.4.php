<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer \Magento\Framework\Module\Setup */
$installer = $this;

$installer->getConnection()->dropColumn($installer->getTable('importexport_importdata'), 'entity_subtype');
