<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

/** @var \Magento\Store\Model\StoreManagerInterface $storeManager */
$storeManager = $objectManager->get('Magento\Store\Model\StoreManagerInterface');

/** @var \Magento\Search\Model\Query $model */
$model = $objectManager->create('Magento\Search\Model\Query');
$model->setData(
    array(
        'query_text' => 'Laptop',
        'synonym_for' => 'Notebook',
        'is_active' => true,
        'store_id' => $storeManager->getStore()->getId()
    )
);
$model->save();
