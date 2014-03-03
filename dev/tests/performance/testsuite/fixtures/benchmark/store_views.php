<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var \Magento\TestFramework\Application $this */

/**
 * @var \Magento\Core\Model\Store $store
 */
$store = $this->getObjectManager()->create('Magento\Core\Model\Store');
$storesCount = \Magento\TestFramework\Helper\Cli::getOption('store_views', 4);

/**
 * @var \Magento\Core\Model\StoreManager $storeManager
 */
$storeManager = $this->getObjectManager()->create('Magento\Core\Model\StoreManager');

/** @var $defaultStoreView \Magento\Core\Model\Store */
$defaultStoreView = $storeManager->getDefaultStoreView();

for ($i = 0; $i < $storesCount; $i++) {
    $store = clone $defaultStoreView;
    $t = microtime(true) * 10000;
    $storeCode = sprintf('store_view_%s', $t);
    $storeName = sprintf('Store View %s', $t);
    $store->addData(array(
        'store_id' => null,
        'code'     => $storeCode,
        'name'     => $storeName
    ));
    $store->save();
    usleep(20);
}
