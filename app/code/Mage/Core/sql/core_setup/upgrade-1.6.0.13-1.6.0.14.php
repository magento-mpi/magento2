<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright  {copyright}
 * @license    {license_link}
 */

/* @var $this Mage_Core_Model_Resource_Setup */
$connection = $this->getConnection();
$connection->dropTable('core_theme_file_update');