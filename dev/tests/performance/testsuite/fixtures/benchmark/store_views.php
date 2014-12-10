<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/** @var \Magento\TestFramework\Application $this */

/**
 * @var \Magento\Store\Model\Store $store
 */
$store = $this->getObjectManager()->create('Magento\Store\Model\Store');
$storesCount = \Magento\TestFramework\Helper\Cli::getOption('store_views', 4);

/**
 * @var \Magento\Store\Model\StoreManager $storeManager
 */
$storeManager = $this->getObjectManager()->create('Magento\Store\Model\StoreManager');

/** @var $defaultStoreView \Magento\Store\Model\Store */
$defaultStoreView = $storeManager->getDefaultStoreView();

for ($i = 0; $i < $storesCount; $i++) {
    $store = clone $defaultStoreView;
    $t = microtime(true) * 10000;
    $storeCode = sprintf('store_view_%s', $t);
    $storeName = sprintf('Store View %s', $t);
    $store->addData(['store_id' => null, 'code' => $storeCode, 'name' => $storeName]);
    $store->save();
    usleep(20);
}
