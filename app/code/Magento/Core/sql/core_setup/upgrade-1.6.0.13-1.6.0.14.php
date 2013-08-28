<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright  {copyright}
 * @license    {license_link}
 */

/* @var $this Magento_Core_Model_Resource_Setup */
$connection = $this->getConnection();
$connection->dropTable('core_theme_file_update');