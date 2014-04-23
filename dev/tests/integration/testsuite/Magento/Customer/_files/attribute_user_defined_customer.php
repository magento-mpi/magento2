<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var Magento\Customer\Model\Attribute $model */
$model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Customer\Model\Attribute');

$model->setName(
    'user_attribute'
)->setEntityTypeId(
    1
)->setIsUserDefined(
    1
)->setAttributeSetId(
    1
)->setAttributeGroupId(
    1
)->setFrontendInput(
    'text'
)->setFrontendLabel(
    'user_attribute'
);

$model->save();
