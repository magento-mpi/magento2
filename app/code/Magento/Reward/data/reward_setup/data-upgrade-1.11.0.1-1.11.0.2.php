<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

/** @var $installer \Magento\Reward\Model\Resource\Setup */
$installer = $this;
$installer->startSetup();

$installer->updateAttribute('customer', 'reward_update_notification', 'is_required', '0');

$installer->updateAttribute('customer', 'reward_warning_notification', 'is_required', '0');

$installer->endSetup();
