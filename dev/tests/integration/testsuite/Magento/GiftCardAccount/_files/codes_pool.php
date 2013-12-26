<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var \Magento\GiftCardAccount\Model\Resource\Pool $poolResourceModel */
$poolResourceModel = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Magento\GiftCardAccount\Model\Resource\Pool');
$poolResourceModel->saveCode('fixture_code_1');
$poolResourceModel->saveCode('fixture_code_2');
$poolResourceModel->saveCode('fixture_code_3');


/** @var \Magento\GiftCardAccount\Model\Pool $poolModel */
$poolModel = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Magento\GiftCardAccount\Model\Pool');
$poolModel->setCode('fixture_code_1')->setStatus(\Magento\GiftCardAccount\Model\Pool\AbstractPool::STATUS_USED);
$poolModel->save();
