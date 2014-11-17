<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer \Magento\Core\Model\Resource\Setup */
$installer = $this;
$installer->startSetup();

$connection = $installer->getConnection();
$connection->update($installer->getTable('core_theme'), array('area' => 'frontend'), array('area = ?' => ''));

$installer->endSetup();
$installer->getEventManager()->dispatch('theme_registration_from_filesystem');
