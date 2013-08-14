<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_ImportExport
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $operation Enterprise_ImportExport_Model_Scheduled_Operation */
$operation = Mage::getModel('Enterprise_ImportExport_Model_Scheduled_Operation');

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
    'freq'              => Magento_Cron_Model_Config_Source_Frequency::CRON_DAILY,
    'status'            => '1',
    'email_receiver'    => 'general',
    'email_sender'      => 'general',
    'email_template'    => 'enterprise_importexport_export_failed',
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