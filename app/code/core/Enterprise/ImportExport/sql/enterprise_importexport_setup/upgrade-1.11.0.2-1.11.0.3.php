<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_ImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Enterprise_Rma_Model_Resource_Setup */
$installer = $this;

$installer->getConnection()
    ->dropColumn($installer->getTable('enterprise_scheduled_operations'), 'entity_subtype');
