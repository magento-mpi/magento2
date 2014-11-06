<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var \Magento\Catalog\Model\Resource\Eav\Attribute $attribute */
$attribute = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Magento\Catalog\Model\Resource\Eav\Attribute');
$attribute->setAttributeCode('test_attribute_code_666')
    ->setEntityTypeId(3)
    ->setIsGlobal(1);
$attribute->save();
