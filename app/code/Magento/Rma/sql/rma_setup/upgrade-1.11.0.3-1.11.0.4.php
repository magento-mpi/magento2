<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Magento_Rma_Model_Resource_Setup */
$installer = $this;

/* setting is_qty_decimal field in rma_item_entity table as a static attribute */
$installer->addAttribute('rma_item', 'is_qty_decimal', array(
            'type'               => 'static',
            'label'              => 'Is item quantity decimal',
            'input'              => 'text',
            'visible'            => false,
            'sort_order'         => 15,
            'position'           => 15,
));
