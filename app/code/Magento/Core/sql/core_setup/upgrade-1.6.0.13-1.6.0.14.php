<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright  {copyright}
 * @license    {license_link}
 */

/* @var $this \Magento\Core\Model\Resource\Setup */
$connection = $this->getConnection();
$connection->dropTable('core_theme_file_update');