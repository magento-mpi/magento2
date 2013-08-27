<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Log
 * @copyright   {copyright}
 * @license     {license_link}
 */

$installer = $this;
/* @var $installer Magento_Core_Model_Resource_Setup */

$data = array(
    array(
        'type_id'     => 1,
        'type_code'   => 'hour',
        'period'      => 1,
        'period_type' => 'HOUR',
    ),

    array(
        'type_id'     => 2,
        'type_code'   => 'day',
        'period'      => 1,
        'period_type' => 'DAY',
    ),
);

foreach ($data as $bind) {
    $installer->getConnection()->insertForce($installer->getTable('log_summary_type'), $bind);
}
