<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

$model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Customer\Model\Attribute');
$model->setName(
    'address_user_attribute'
)->setEntityTypeId(
    2
)->setAttributeSetId(
    2
)->setAttributeGroupId(
    1
)->setFrontendInput(
    'text'
)->setFrontendLabel(
    'Address user attribute'
)->setIsUserDefined(
    1
);
$model->save();

/** @var \Magento\Customer\Model\Resource\Setup $setupResource */
$setupResource = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
    'Magento\Customer\Model\Resource\Setup',
    array('resourceName' => 'customer_setup')
);
$data = array(array('form_code' => 'customer_address_edit', 'attribute_id' => $model->getAttributeId()));
$setupResource->getConnection()->insertMultiple($setupResource->getTable('customer_form_attribute'), $data);
