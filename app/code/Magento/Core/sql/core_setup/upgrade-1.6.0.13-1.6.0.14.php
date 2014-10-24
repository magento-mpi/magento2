<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

/* @var $this Magento\Setup\Module\SetupModule */
$connection = $this->getConnection();
$connection->dropTable('core_theme_file_update');
