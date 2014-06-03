<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

$model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
    'Magento\Catalog\Model\Resource\Eav\Attribute'
);
$model->setName('system_attribute')->setId(3)->setEntityTypeId(4)->setIsUserDefined(0)->setApplyTo(array('simple'));
$model->save();
