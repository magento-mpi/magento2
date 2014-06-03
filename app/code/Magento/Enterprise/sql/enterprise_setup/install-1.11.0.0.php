<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer \Magento\Framework\Module\Setup */
$installer = $this;

$installer->getConnection()->truncateTable($installer->getTable('adminnotification_inbox'));
