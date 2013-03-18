<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$connection = $installer->getConnection();
$connection->update($installer->getTable('core_theme'), array('area' => 'frontend'), array('area = ?' => ''));

$installer->endSetup();

Mage::dispatchEvent('theme_registration_from_filesystem');
