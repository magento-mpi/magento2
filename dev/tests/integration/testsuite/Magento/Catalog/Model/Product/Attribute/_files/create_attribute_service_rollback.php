<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
use Magento\TestFramework\Helper\Bootstrap;

/** @var \Magento\Framework\Registry $registry */
$registry = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Framework\Registry');
$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

/** @var \Magento\Catalog\Model\Resource\Eav\Attribute $attribute */
$attribute = Bootstrap::getObjectManager()->create('Magento\Catalog\Model\Resource\Eav\Attribute');
$attribute->loadByCode(4, 'label_attr_code3df4tr3');

if ($attribute->getId()) {
    $attribute->delete();
}

/** @var \Magento\Catalog\Model\Resource\Eav\Attribute $attribute */
$attribute = Bootstrap::getObjectManager()->create('Magento\Catalog\Model\Resource\Eav\Attribute');
$attribute->loadByCode(4, 'test_attribute_code_l');

if ($attribute->getId()) {
    $attribute->delete();
}