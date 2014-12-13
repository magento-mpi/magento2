<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

/** @var \Magento\Store\Model\StoreManagerInterface $storeManager */
$storeManager = $objectManager->get('Magento\Store\Model\StoreManagerInterface');

/** @var \Magento\Search\Model\Query $model */
$model = $objectManager->create('Magento\Search\Model\Query');
$model->setData(
    [
        'query_text' => 'Laptop',
        'synonym_for' => 'Notebook',
        'is_active' => true,
        'store_id' => $storeManager->getStore()->getId(),
    ]
);
$model->save();
