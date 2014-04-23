<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

require __DIR__ . '/../../../Magento/Customer/_files/customer.php';

/** @var \Magento\Persistent\Model\Session $persistentSession */
$persistentSession = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
    'Magento\Persistent\Model\Session'
);
$persistentSession->setCustomerId($customer->getId())->save();
