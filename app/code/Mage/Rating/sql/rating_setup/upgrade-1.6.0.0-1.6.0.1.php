<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Rating
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->getConnection()
    ->addColumn($installer->getTable('rating'), 'is_active', array(
        'type'      => Magento_DB_Ddl_Table::TYPE_SMALLINT,
        'nullable'  => false,
        'default'   => '1',
        'comment'   => 'Rating is active.'
    ));
