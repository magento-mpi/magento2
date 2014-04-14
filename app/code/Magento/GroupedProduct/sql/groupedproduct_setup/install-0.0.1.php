<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer \Magento\Catalog\Model\Resource\Setup */
$installer = $this;

$field = 'country_of_manufacture';
$applyTo = explode(',', $installer->getAttribute(\Magento\Catalog\Model\Product::ENTITY, $field, 'apply_to'));
if (!in_array('grouped', $applyTo)) {
    $applyTo[] = 'grouped';
    $installer->updateAttribute(\Magento\Catalog\Model\Product::ENTITY, $field, 'apply_to', implode(',', $applyTo));
}
