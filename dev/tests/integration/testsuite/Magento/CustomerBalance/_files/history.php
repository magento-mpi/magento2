<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

\Magento\TestFramework\Helper\Bootstrap::getInstance()->loadArea(
    \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE
);

require __DIR__ . '/../../../Magento/Customer/_files/customer.php';
/** @var $balance \Magento\CustomerBalance\Model\Balance */
$balance = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
    'Magento\CustomerBalance\Model\Balance'
);
$balance->setCustomerId(
    $customer->getId()
)->setWebsiteId(
    \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
        'Magento\Framework\StoreManagerInterface'
    )->getStore()->getWebsiteId()
);
$balance->save();

/** @var $history \Magento\CustomerBalance\Model\Balance\History */
$history = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
    'Magento\CustomerBalance\Model\Balance\History'
);
$history->setCustomerId(
    $customer->getId()
)->setWebsiteId(
    \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
        'Magento\Framework\StoreManagerInterface'
    )->getStore()->getWebsiteId()
)->setBalanceModel(
    $balance
);
$history->save();
