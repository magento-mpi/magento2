<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
/** @var \Magento\Paypal\Model\Billing\Agreement $billingAgreement */
$billingAgreement = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
    'Magento\Paypal\Model\Billing\Agreement'
)->setAgreementLabel(
    'TEST'
)->setCustomerId(
    1
)->setMethodCode(
    'paypal_express'
)->setReferenceId(
    'REF-ID-TEST-678'
)->setStatus(
    Magento\Paypal\Model\Billing\Agreement::STATUS_ACTIVE
)->setStoreId(
    1
)->save();
