<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rating
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer \Magento\Core\Model\Resource\Setup */
$installer = $this;

$installer->getConnection()
    ->addColumn($installer->getTable('rating'), 'is_active', array(
        'type'      => \Magento\DB\Ddl\Table::TYPE_SMALLINT,
        'nullable'  => false,
        'default'   => '1',
        'comment'   => 'Rating is active.'
    ));
