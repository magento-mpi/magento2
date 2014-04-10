<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Enterprise
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer \Magento\Module\Setup */
$installer = $this;

$installer->getConnection()->truncateTable($installer->getTable('adminnotification_inbox'));
