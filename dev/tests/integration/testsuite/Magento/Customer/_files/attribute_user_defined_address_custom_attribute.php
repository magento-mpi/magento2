<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

$model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Customer\Model\Attribute');
$model->setName(
    'custom_attribute1'
)->setEntityTypeId(
    2
)->setAttributeSetId(
    2
)->setAttributeGroupId(
    1
)->setFrontendInput(
    'text'
)->setFrontendLabel(
    'custom_attribute_frontend_label'
)->setIsUserDefined(
    1
);
$model->save();

$model2 = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Customer\Model\Attribute');
$model2->setName(
    'custom_attribute2'
)->setEntityTypeId(
    2
)->setAttributeSetId(
    2
)->setAttributeGroupId(
    1
)->setFrontendInput(
    'text'
)->setFrontendLabel(
    'custom_attribute_frontend_label'
)->setIsUserDefined(
    1
);
$model2->save();

/** @var \Magento\Customer\Model\Resource\Setup $setupResource */
$setupResource = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
    'Magento\Customer\Model\Resource\Setup',
    ['resourceName' => 'customer_setup']
);

$data = [['form_code' => 'customer_address_edit', 'attribute_id' => $model->getAttributeId()]];
$setupResource->getConnection()->insertMultiple($setupResource->getTable('customer_form_attribute'), $data);

$data2 = [['form_code' => 'customer_address_edit', 'attribute_id' => $model2->getAttributeId()]];
$setupResource->getConnection()->insertMultiple($setupResource->getTable('customer_form_attribute'), $data2);
