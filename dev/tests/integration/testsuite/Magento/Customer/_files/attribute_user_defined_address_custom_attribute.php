<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

$model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Customer\Model\Attribute');
$model->setName(
    'custom_attribute'
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
    'custom_attributes'
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
    array('resourceName' => 'customer_setup')
);

$data = array(array('form_code' => 'customer_address_edit', 'attribute_id' => $model->getAttributeId()));
$setupResource->getConnection()->insertMultiple($setupResource->getTable('customer_form_attribute'), $data);

$data2 = array(array('form_code' => 'customer_address_edit', 'attribute_id' => $model2->getAttributeId()));
$setupResource->getConnection()->insertMultiple($setupResource->getTable('customer_form_attribute'), $data2);
