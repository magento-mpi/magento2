<?php
/**
 * {license_notice}
 *
 * @category    Social
 * @package     Social_Facebook
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Social_Facebook_Model_Resource_Setup */
$installer = $this;

$installer->getConnection()->modifyColumn(
    $installer->getTable('social_facebook_actions'),
    'facebook_action',
    array(
        'type'      => Magento_DB_Ddl_Table::TYPE_TEXT,
        'length'    => 100,
        'nullable'  => false,
        'comment'   => 'User Action'
    )
);