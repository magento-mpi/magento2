<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer \Magento\Framework\Module\Setup */
$installer = $this;

$data = array(
    array('type_id' => 1, 'type_code' => 'hour', 'period' => 1, 'period_type' => 'HOUR'),
    array('type_id' => 2, 'type_code' => 'day', 'period' => 1, 'period_type' => 'DAY')
);

foreach ($data as $bind) {
    $installer->getConnection()->insertForce($installer->getTable('log_summary_type'), $bind);
}
