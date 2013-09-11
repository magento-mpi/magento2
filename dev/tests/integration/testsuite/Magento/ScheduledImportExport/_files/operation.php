<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ScheduledImportExport
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $operation \Magento\ScheduledImportExport\Model\Scheduled\Operation */
$operation = Mage::getModel('Magento\ScheduledImportExport\Model\Scheduled\Operation');

$data = array(
    'operation_type'    => 'export',
    'name'              => 'Test Export ' . microtime(),
    'entity_type'       => 'catalog_product',
    'file_info'         => array(
        'file_format' => 'csv',
        'server_type' => 'file',
        'file_path'   => 'export',
    ),
    'start_time'        => '00:00:00',
    'freq'              => \Magento\Cron\Model\Config\Source\Frequency::CRON_DAILY,
    'status'            => '1',
    'email_receiver'    => 'general',
    'email_sender'      => 'general',
    'email_template'    => 'magento_scheduledimportexport_export_failed',
    'email_copy_method' => 'bcc',
    'entity_attributes' => array(
        'export_filter' => array(
            'cost' => array ('','')
        ),
    ),

);
$operation->setId(1);
$operation->isObjectNew(true);
$operation->setData($data);
$operation->save();