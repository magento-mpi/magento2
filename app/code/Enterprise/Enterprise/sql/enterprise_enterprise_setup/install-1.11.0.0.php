<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Enterprise
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer Magento_Core_Model_Resource_Setup */
$installer = $this;

$installer->getConnection()->truncateTable($installer->getTable('adminnotification_inbox'));
