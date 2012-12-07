<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Reward
 * @copyright  {copyright}
 * @license    {license_link}
 */

/** @var $installer Enterprise_Reward_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$installer->updateAttribute(
    'customer',
    'reward_update_notification',
    'is_required',
    '0'
);

$installer->updateAttribute(
    'customer',
    'reward_warning_notification',
    'is_required',
    '0'
);

$installer->endSetup();
