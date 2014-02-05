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

$model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Magento\Customer\Model\Attribute');
$model->setName('user_attribute')
    ->setEntityTypeId(2)
    ->setIsUserDefined(1);
$model->save();

/** @var \Magento\Customer\Model\Resource\Setup $setupResource */
$setupResource = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Magento\Customer\Model\Resource\Setup', ['resourceName' => 'customer_setup']);
$data = [
    ['form_code' => 'customer_address_edit', 'attribute_id' => $model->getAttributeId()]
];
$setupResource->getConnection()->insertMultiple($setupResource->getTable('customer_form_attribute'), $data);
