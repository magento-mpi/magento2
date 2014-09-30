<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

/** @var \Magento\Framework\StoreManagerInterface $storeManager */
$storeManager = $objectManager->get('Magento\Framework\StoreManagerInterface');

/** @var Magento\CatalogSearch\Model\Query $model */
$model = $objectManager->create('Magento\CatalogSearch\Model\Query');
$model->setData(
    array(
        'query_text' => 'Laptop',
        'synonym_for' => 'Notebook',
        'is_active' => true,
        'store_id' => $storeManager->getStore()->getId()
    )
);
$model->save();
